<?php

/**
 * Bastion Peak — no-terminal cPanel deploy helper.
 *
 * Upload this file to your cPanel account, then open it in a browser:
 *     https://bastionpeaks.com/deploy.php?key=YOUR_KEY
 * (if you place it inside the web root), or via the app's public folder.
 *
 * It auto-detects the Laravel application folder, so it works whether it sits
 * in the app root or in public_html.
 *
 * SECURITY: set a strong `DEPLOY_KEY=...` in the app's `.env`, then DELETE this
 * file from the server once setup is complete.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Bootstrap: locate the application and the public directory.
// ---------------------------------------------------------------------------
$appDir = null;
foreach ([__DIR__, dirname(__DIR__), __DIR__ . '/../bastionpeak-laravel', __DIR__ . '/bastionpeak-laravel', dirname(__DIR__) . '/bastionpeak-laravel'] as $candidate) {
    if (is_file($candidate . '/bootstrap/app.php') && is_file($candidate . '/vendor/autoload.php')) {
        $appDir = realpath($candidate);
        break;
    }
}

if ($appDir === null) {
    http_response_code(500);
    header('Content-Type: text/plain');
    exit("Could not locate the Laravel application. Place deploy.php next to the app folder (the one containing composer.json) or edit \$APP_DIR_SEARCH.\n");
}

$publicDir = null;
foreach ([__DIR__, ($appDir ? $appDir . '/public' : null)] as $candidate) {
    if ($candidate && is_file($candidate . '/index.php')) {
        $publicDir = realpath($candidate);
        break;
    }
}
if ($publicDir === null) {
    $publicDir = $appDir ? realpath($appDir . '/public') : __DIR__;
}

// ---------------------------------------------------------------------------
// Secret key: read DEPLOY_KEY from the app's .env (preferred), else fallback.
// ---------------------------------------------------------------------------
$deployKey = bp_env($appDir, 'DEPLOY_KEY');
$usingDefaultKey = false;
if (!$deployKey) {
    $deployKey = 'bp-change-this-key-9f2a71c4';
    $usingDefaultKey = true;
}

$key = (string) ($_GET['key'] ?? ($_POST['key'] ?? ''));
if (!hash_equals($deployKey, $key)) {
    http_response_code(403);
    header('Content-Type: text/plain');
    exit("Forbidden. Add ?key=YOUR_KEY to the URL.\n");
}

header('Content-Type: text/html; charset=utf-8');

$logs = [];
$ok = true;

function bp_out(string $s): void
{
    global $logs;
    $logs[] = $s;
}

function bp_copy_dir(string $src, string $dst): void
{
    if (!is_dir($dst)) {
        @mkdir($dst, 0755, true);
    }
    foreach (scandir($src) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $s = $src . '/' . $item;
        $d = $dst . '/' . $item;
        if (is_dir($s)) {
            bp_copy_dir($s, $d);
        } else {
            @copy($s, $d);
        }
    }
}

function bp_env(string $appDir, string $name, ?string $default = null): ?string
{
    $file = $appDir . '/.env';
    if (!is_file($file)) {
        return $default;
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        if (trim($k) === $name) {
            return trim($v, "\"'");
        }
    }
    return $default;
}

$action = (string) ($_GET['action'] ?? 'info');

$kernel = null;
$bootError = null;
try {
    require $appDir . '/vendor/autoload.php';
    $app = require $appDir . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
} catch (\Throwable $e) {
    $bootError = get_class($e) . ': ' . $e->getMessage();
}

function runArtisan(&$kernel, string $command, array $params = []): array
{
    try {
        $code = $kernel->call($command, $params);
        return ['code' => $code, 'output' => trim($kernel->output())];
    } catch (\Throwable $e) {
        return ['code' => 1, 'output' => get_class($e) . ': ' . $e->getMessage()];
    }
}

if ($kernel && $bootError === null) {
    switch ($action) {
        case 'migrate':
            $r = runArtisan($kernel, 'migrate', ['--force' => true]);
            bp_out("migrate (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $ok = $r['code'] === 0;
            break;

        case 'seed':
            $r = runArtisan($kernel, 'db:seed', ['--force' => true]);
            bp_out("db:seed (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $ok = $r['code'] === 0;
            break;

        case 'configcache':
            foreach ([['config:clear', []], ['config:cache', []]] as [$cmd, $p]) {
                $r = runArtisan($kernel, $cmd, $p);
                bp_out("$cmd (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            }
            break;

        case 'routecache':
            $r = runArtisan($kernel, 'route:clear');
            bp_out("route:clear (exit {$r['code']})");
            $r = runArtisan($kernel, 'route:cache');
            bp_out("route:cache (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            break;

        case 'viewcache':
            $r = runArtisan($kernel, 'view:clear');
            bp_out("view:clear (exit {$r['code']})");
            $r = runArtisan($kernel, 'view:cache');
            bp_out("view:cache (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $ok = $r['code'] === 0;
            break;

        case 'clearcache':
            foreach (['config:clear', 'route:clear', 'view:clear', 'event:clear', 'cache:clear'] as $cmd) {
                $r = runArtisan($kernel, $cmd);
                bp_out("$cmd (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            }
            break;

        case 'storage':
            $target = $publicDir . '/storage';
            $source = $appDir . '/storage/app/public';
            if (!is_dir($source)) {
                bp_out("Source missing: $source");
                $ok = false;
                break;
            }
            if (is_link($target)) {
                bp_out("Symlink already exists: $target -> " . readlink($target));
                break;
            }
            if (is_dir($target) && !is_link($target)) {
                bp_out("A real directory already exists at $target (leaving it). Symlink is preferred so new uploads appear.");
                break;
            }
            $linked = @symlink($source, $target);
            if ($linked) {
                bp_out("Symlink created: $target -> $source");
            } else {
                bp_copy_dir($source, $target);
                bp_out("symlink() unavailable — copied files instead. NOTE: new uploads will not appear automatically; ask your host to enable symlinks.");
            }
            break;

        case 'assets':
            $srcBuild = $appDir . '/public/build';
            $dstBuild = $publicDir . '/build';
            if (realpath($srcBuild) !== false && realpath($dstBuild) !== false && realpath($srcBuild) === realpath($dstBuild)) {
                bp_out("Build already in place at $dstBuild");
            } elseif (is_dir($srcBuild)) {
                bp_copy_dir($srcBuild, $dstBuild);
                bp_out("Copied build assets to $dstBuild");
            } else {
                bp_out("No build source at $srcBuild");
                $ok = false;
            }
            foreach (['favicon.ico', 'favicon.svg', 'robots.txt'] as $f) {
                if (is_file($appDir . '/public/' . $f)) {
                    @copy($appDir . '/public/' . $f, $publicDir . '/' . $f);
                }
            }
            break;

        case 'all':
            $r = runArtisan($kernel, 'migrate', ['--force' => true]);
            bp_out("migrate (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $ok = $ok && $r['code'] === 0;

            $r = runArtisan($kernel, 'db:seed', ['--force' => true]);
            bp_out("db:seed (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $ok = $ok && $r['code'] === 0;

            $target = $publicDir . '/storage';
            $source = $appDir . '/storage/app/public';
            if (is_link($target)) {
                bp_out("storage: symlink already present");
            } elseif (is_dir($source) && @symlink($source, $target)) {
                bp_out("storage: symlink created");
            } elseif (is_dir($source)) {
                bp_copy_dir($source, $target);
                bp_out("storage: copied (symlink unavailable)");
            }

            $srcBuild = $appDir . '/public/build';
            $dstBuild = $publicDir . '/build';
            if (is_dir($srcBuild) && realpath($srcBuild) !== realpath($dstBuild)) {
                bp_copy_dir($srcBuild, $dstBuild);
                bp_out("assets: copied build to $dstBuild");
            }

            $r = runArtisan($kernel, 'config:cache');
            bp_out("config:cache (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $r = runArtisan($kernel, 'route:cache');
            bp_out("route:cache (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            $r = runArtisan($kernel, 'view:cache');
            bp_out("view:cache (exit {$r['code']})\n" . ($r['output'] ?: '(no output)'));
            break;

        case 'pull':
            $output = [];
            $exitCode = 0;
            // Try 'git pull' in the application directory
            $command = 'cd ' . escapeshellarg($appDir) . ' && git pull 2>&1';
            exec($command, $output, $exitCode);
            bp_out("git pull (exit {$exitCode})\n" . implode("\n", $output));
            if ($exitCode === 0) {
                // Clear caches after pull
                foreach (['config:clear', 'route:clear', 'view:clear', 'event:clear'] as $cmd) {
                    $r = runArtisan($kernel, $cmd);
                    bp_out("$cmd (exit {$r['code']})");
                }
                // Re-cache optimized
                $r = runArtisan($kernel, 'config:cache');
                bp_out("config:cache (exit {$r['code']})");
                $r = runArtisan($kernel, 'route:cache');
                bp_out("route:cache (exit {$r['code']})");
                $r = runArtisan($kernel, 'view:cache');
                bp_out("view:cache (exit {$r['code']})");
                // Copy build assets if they changed
                $srcBuild = $appDir . '/public/build';
                $dstBuild = $publicDir . '/build';
                if (is_dir($srcBuild) && realpath($srcBuild) !== realpath($dstBuild)) {
                    bp_copy_dir($srcBuild, $dstBuild);
                    bp_out("assets: copied build to $dstBuild");
                }
            } else {
                $ok = false;
            }
            break;

        case 'info':
        default:
            break;
    }
}

$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl'];
$envName = bp_env($appDir, 'APP_NAME');
$envKey = bp_env($appDir, 'APP_KEY');
$envUrl = bp_env($appDir, 'APP_URL');
$dbConn = bp_env($appDir, 'DB_CONNECTION');
$dbHost = bp_env($appDir, 'DB_HOST');
$dbName = bp_env($appDir, 'DB_DATABASE');
$dbUser = bp_env($appDir, 'DB_USERNAME');
$sessDriver = bp_env($appDir, 'SESSION_DRIVER');

$dbOk = false;
$dbMsg = '';
if ($kernel && in_array($dbConn, ['mysql', 'mariadb'], true)) {
    try {
        $pdo = new PDO(
            'mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8mb4',
            $dbUser,
            bp_env($appDir, 'DB_PASSWORD', '') ?? '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]
        );
        $dbOk = true;
        $dbMsg = 'Connected to ' . $dbName;
    } catch (\Throwable $e) {
        $dbMsg = $e->getMessage();
    }
}

$base = basename(__FILE__);
$q = '?key=' . rawurlencode($deployKey);
$link = fn(string $a) => htmlspecialchars($base . $q . '&action=' . $a, ENT_QUOTES);

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bastion Peak · Deploy</title>
<style>
  body{font-family:system-ui,Segoe UI,Roboto,sans-serif;background:#0b1120;color:#e2e8f0;margin:0;padding:24px}
  .wrap{max-width:820px;margin:0 auto}
  h1{font-size:20px;margin:0 0 16px}
  .card{background:#111a2e;border:1px solid #1e2a44;border-radius:10px;padding:16px;margin-bottom:16px}
  code,pre{background:#0b1120;border:1px solid #1e2a44;border-radius:6px;padding:2px 6px;font-family:ui-monospace,Consolas,monospace}
  pre{padding:12px;overflow:auto;white-space:pre-wrap}
  table{width:100%;border-collapse:collapse}
  td,th{text-align:left;padding:6px 8px;border-bottom:1px solid #1e2a44;font-size:14px}
  .ok{color:#4ade80}.bad{color:#f87171}
  a.btn{display:inline-block;padding:9px 14px;margin:4px 6px 4px 0;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;font-size:14px}
  a.btn.alt{background:#374151}
  .warn{background:#3b1d1d;border-color:#7f1d1d;color:#fecaca}
</style>
</head>
<body>
<div class="wrap">
  <h1>Bastion Peak · cPanel Deploy</h1>

  <?php if ($usingDefaultKey): ?>
    <div class="card warn"><strong>Using the default deploy key.</strong>
      Add <code>DEPLOY_KEY=your-long-random-string</code> to <code>.env</code> so the URL key is not guessable.</div>
  <?php endif; ?>

  <?php if ($bootError !== null): ?>
    <div class="card warn"><strong>Laravel failed to boot:</strong><pre><?= htmlspecialchars($bootError) ?></pre>
      Check <code>.env</code> (DB credentials, APP_KEY) and that <code>vendor/</code> was uploaded.</div>
  <?php endif; ?>

  <div class="card">
    <table>
      <tr><th>App dir</th><td><code><?= htmlspecialchars($appDir ?: 'not found') ?></code></td></tr>
      <tr><th>Public dir</th><td><code><?= htmlspecialchars($publicDir ?: 'not found') ?></code></td></tr>
      <tr><th>PHP</th><td><?= htmlspecialchars(PHP_VERSION) ?> <?= version_compare(PHP_VERSION, '8.2.0', '>=') ? '<span class="ok">ok</span>' : '<span class="bad">needs 8.2+</span>' ?></td></tr>
      <tr><th>APP_NAME / APP_URL</th><td><?= htmlspecialchars((string) $envName) ?> · <?= htmlspecialchars((string) $envUrl) ?></td></tr>
      <tr><th>APP_KEY</th><td><?= $envKey ? '<span class="ok">set</span>' : '<span class="bad">missing</span>' ?></td></tr>
      <tr><th>Session driver</th><td><?= htmlspecialchars((string) $sessDriver) ?></td></tr>
      <tr><th>Database</th><td><?= $dbConn ?> · <?= htmlspecialchars((string) $dbName) ?>@<?= htmlspecialchars((string) $dbHost) ?> — <?= $dbOk ? '<span class="ok">' . htmlspecialchars($dbMsg) . '</span>' : '<span class="bad">' . htmlspecialchars($dbMsg ?: 'n/a') . '</span>' ?></td></tr>
      <tr><th>storage/ writable</th><td><?= is_writable($appDir . '/storage') ? '<span class="ok">yes</span>' : '<span class="bad">no</span>' ?></td></tr>
      <tr><th>bootstrap/cache writable</th><td><?= is_writable($appDir . '/bootstrap/cache') ? '<span class="ok">yes</span>' : '<span class="bad">no</span>' ?></td></tr>
    </table>
    <p style="margin:12px 0 6px"><strong>Extensions</strong></p>
    <table>
      <?php foreach ($extensions as $ext): ?>
        <tr><td><?= $ext ?></td><td><?= extension_loaded($ext) ? '<span class="ok">loaded</span>' : '<span class="bad">MISSING</span>' ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="card">
    <p><strong>Run steps</strong> (do these in order)</p>
    <a class="btn" href="<?= $link('migrate') ?>">1 · Run migrations</a>
    <a class="btn" href="<?= $link('seed') ?>">2 · Seed data</a>
    <a class="btn" href="<?= $link('assets') ?>">3 · Copy build assets</a>
    <a class="btn" href="<?= $link('storage') ?>">4 · Storage link</a>
    <a class="btn" href="<?= $link('configcache') ?>">5 · Cache config</a>
    <a class="btn" href="<?= $link('routecache') ?>">6 · Cache routes</a>
    <a class="btn" href="<?= $link('viewcache') ?>">7 · Cache views</a>
    <p style="margin-top:14px"></p>
    <a class="btn alt" href="<?= $link('pull') ?>">Git pull (webhook)</a>
    <a class="btn alt" href="<?= $link('all') ?>">Run everything</a>
    <a class="btn alt" href="<?= $link('clearcache') ?>">Clear caches</a>
    <a class="btn alt" href="<?= $link('info') ?>">Refresh</a>
  </div>

  <?php if (!empty($logs)): ?>
    <div class="card"><pre><?= htmlspecialchars(implode("\n\n", $logs)) ?></pre></div>
  <?php endif; ?>

  <div class="card warn">
    <strong>Delete deploy.php from the server once you are done.</strong>
    Anyone with the key can run migrations and seeders.
  </div>
</div>
</body>
</html>
