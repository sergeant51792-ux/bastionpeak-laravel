return [
    'name' => 'bastion-peak',
    'theme' => 'bastion-peak',
    'dark_mode' => 'media',
    'builder' => BastionPeak\ThemeBuilder::class,
    'css' => [
        'public/css/tokens.css',
        'public/css/app.css',
    ],
    'js' => [
        'public/js/app.js',
    ],
    'fonts' => [
        'public/fonts/schibsted.css',
    ],
    'image_sizes' => [
        'account-card' => ['width' => 400, 'height' => 240],
        'proof-thumb' => ['width' => 800, 'height' => 600],
        'avatar' => ['width' => 128, 'height' => 128],
        'logo' => ['width' => 512, 'height' => 128],
    ],
    'lazy_load_images' => true,
    'purge' => [
        'resources/views/**/*.blade.php',
        'resources/views/**/*.html',
        'public/customer/**/*.html',
        'public/admin/**/*.html',
    ],
];
