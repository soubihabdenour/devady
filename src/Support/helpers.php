<?php declare(strict_types=1);

use App\Support\I18n;

if (!function_exists('e')) {
    function e(?string $v): string
    {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('t')) {
    function t(string $key, array $vars = []): string
    {
        return I18n::t($key, $vars);
    }
}

if (!function_exists('money')) {
    function money(float $amount, string $currency = 'DZD'): string
    {
        $symbol = [
            'DZD' => 'DA', 'EUR' => '€', 'USD' => '$', 'GBP' => '£',
            'CAD' => '$', 'CHF' => 'CHF', 'JPY' => '¥', 'KRW' => '₩',
        ][$currency] ?? $currency;
        $decimals = $currency === 'KRW' || $currency === 'JPY' ? 0 : 2;
        return $symbol . ' ' . number_format($amount, $decimals);
    }
}

if (!function_exists('amount_in_words')) {
    function amount_in_words(float $amount, string $currency = 'DZD'): string
    {
        [$whole, $frac] = explode('.', number_format($amount, 2, '.', ''));
        $major = _int_to_words((int)$whole);
        $names = [
            'DZD' => ['Algerian dinars', 'centimes'],
            'EUR' => ['euros', 'cents'],
            'USD' => ['US dollars', 'cents'],
            'GBP' => ['pounds sterling', 'pence'],
            'KRW' => ['South Korean won', ''],
            'JPY' => ['Japanese yen', ''],
            'CHF' => ['Swiss francs', 'centimes'],
            'CAD' => ['Canadian dollars', 'cents'],
        ];
        [$majorName, $minorName] = $names[$currency] ?? [$currency, ''];
        $major = ucfirst($major ?: 'zero') . ' ' . $majorName;
        if ($minorName === '' || (int)$frac === 0) {
            return $major;
        }
        return $major . ' and ' . $frac . '/100';
    }
}

if (!function_exists('_int_to_words')) {
    function _int_to_words(int $n): string
    {
        if ($n < 0) return 'minus ' . _int_to_words(-$n);
        if ($n === 0) return 'zero';
        $units = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
                  'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
                  'seventeen', 'eighteen', 'nineteen'];
        $tens  = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
        $chunk = function (int $x) use ($units, $tens, &$chunk): string {
            if ($x < 20) return $units[$x];
            if ($x < 100) {
                $t = intdiv($x, 10);
                $u = $x % 10;
                return $tens[$t] . ($u ? '-' . $units[$u] : '');
            }
            $h = intdiv($x, 100);
            $r = $x % 100;
            return $units[$h] . ' hundred' . ($r ? ' ' . $chunk($r) : '');
        };
        $parts = [];
        foreach ([1_000_000_000 => 'billion', 1_000_000 => 'million', 1_000 => 'thousand', 1 => ''] as $scale => $name) {
            if ($n >= $scale) {
                $q = intdiv($n, $scale);
                $n %= $scale;
                $parts[] = trim($chunk($q) . ($name ? ' ' . $name : ''));
            }
        }
        return implode(' ', $parts);
    }
}

if (!function_exists('store_image_upload')) {
    function store_image_upload(string $field, string $slot, string $base): ?string
    {
        if (empty($_FILES[$field]['tmp_name'])) return null;
        $err = $_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($err === UPLOAD_ERR_NO_FILE) return null;
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException("Upload of $slot failed (error $err).");
        }
        if ($_FILES[$field]['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException("$slot image must be under 2 MB.");
        }
        $info = @getimagesize($_FILES[$field]['tmp_name']);
        $ext  = match ($info[2] ?? 0) {
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_JPEG => 'jpg',
            default => null,
        };
        if ($ext === null) {
            throw new RuntimeException("$slot must be a PNG or JPG image.");
        }
        $dir = $base . '/storage/uploads';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            throw new RuntimeException("Could not create storage/uploads directory.");
        }
        foreach (glob($dir . '/' . $slot . '.*') ?: [] as $old) @unlink($old);
        $target = $dir . '/' . $slot . '.' . $ext;
        if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
            throw new RuntimeException("Could not save uploaded $slot image.");
        }
        @chmod($target, 0644);
        return 'uploads/' . $slot . '.' . $ext;
    }
}

if (!function_exists('store_signature_dataurl')) {
    /** Decode a base64 data: URL signature drawing and store as PNG. */
    function store_signature_dataurl(string $dataUrl, string $slot, string $base): ?string
    {
        if (!preg_match('#^data:image/png;base64,([A-Za-z0-9+/=]+)$#', trim($dataUrl), $m)) {
            return null;
        }
        $bin = base64_decode($m[1], true);
        if ($bin === false || strlen($bin) > 2 * 1024 * 1024) return null;
        $dir = $base . '/storage/uploads';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) return null;
        foreach (glob($dir . '/' . $slot . '.*') ?: [] as $old) @unlink($old);
        $target = $dir . '/' . $slot . '.png';
        file_put_contents($target, $bin);
        @chmod($target, 0644);
        return 'uploads/' . $slot . '.png';
    }
}

if (!function_exists('delete_stored_image')) {
    function delete_stored_image(?string $relPath, string $base): void
    {
        if (!$relPath) return;
        $abs  = realpath($base . '/storage/' . $relPath);
        $root = realpath($base . '/storage');
        if ($abs && $root && str_starts_with($abs, $root . DIRECTORY_SEPARATOR)) {
            @unlink($abs);
        }
    }
}
