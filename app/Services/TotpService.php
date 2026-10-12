<?php

declare(strict_types=1);

namespace App\Services;

class TotpService
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    private const PERIOD = 30;

    private const DIGITS = 6;

    public function generateSecret(int $length = 32): string
    {
        $bytes = random_bytes($length);
        $secret = '';

        foreach (str_split($bytes) as $byte) {
            $secret .= self::ALPHABET[ord($byte) % 32];
        }

        return $secret;
    }

    public function code(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $counter = intdiv($timestamp, self::PERIOD);

        $key = $this->base32Decode($secret);
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);

        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord($hash[19]) & 0x0F;

        $binary = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $otp = $binary % (10 ** self::DIGITS);

        return str_pad((string) $otp, self::DIGITS, '0', STR_PAD_LEFT);
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        if (! ctype_digit($code) || strlen($code) !== self::DIGITS) {
            return false;
        }

        $now = time();

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->code($secret, $now + ($i * self::PERIOD)), $code)) {
                return true;
            }
        }

        return false;
    }

    public function secondsRemaining(?int $timestamp = null): int
    {
        $timestamp ??= time();

        return self::PERIOD - ($timestamp % self::PERIOD);
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/i', '', $secret));
        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        foreach (str_split($secret) as $char) {
            $value = strpos(self::ALPHABET, $char);

            if ($value === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}
