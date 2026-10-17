<?php
declare(strict_types=1);

const SAB_NAME = 'Sadaf Al Bahr';
const SAB_NAME_AR = 'صدف البحر';
const SAB_TAGLINE = 'Wallpaper, carpets, curtains, decoration and professional fixing';
const SAB_PHONE = '+971553079830';
const SAB_PHONE_DISPLAY = '+971 55 307 9830';
const SAB_WHATSAPP = '971553079830';
const SAB_EMAIL = 'hello@sadafalbahr.ae';
const SAB_ADDRESS = 'Dr. Ibrahim Al Marzouqi Building 187, Ground Floor, Naif Road, Al Murar, Deira, Dubai, UAE';
const SAB_HOURS = 'Daily 9:00 AM – 10:00 PM (break 1:30 – 4:30 PM)';
const SAB_MAPS = 'https://www.google.com/maps/search/?api=1&query=Sadaf+Al+Bahar+Curtains+Naif+Road+Deira+Dubai';
const SAB_MAPS_EMBED = 'https://maps.google.com/maps?q=Naif%20Road%20Al%20Murar%20Deira%20Dubai&t=&z=16&ie=UTF8&iwloc=&output=embed';
const SAB_BASE_PATH = '/sadafalbahr';
const SAB_LIVE_HOSTS = [];
const SAB_PROJECT_CATEGORIES = ['Wallpaper', 'Curtains', 'Carpets', 'Installation'];

function sabBasePath(): string
{
    $host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
    foreach (SAB_LIVE_HOSTS as $liveHost) {
        if ($host === strtolower((string) $liveHost)) {
            return '';
        }
    }
    $path = trim(SAB_BASE_PATH, '/');
    return $path === '' ? '' : '/' . $path;
}

function sabHref(string $path = ''): string
{
    $hash = '';
    if (str_contains($path, '#')) {
        [$path, $hashPart] = explode('#', $path, 2);
        $hash = '#' . $hashPart;
    }
    $query = '';
    if (str_contains($path, '?')) {
        [$path, $queryPart] = explode('?', $path, 2);
        $query = '?' . $queryPart;
    }
    $path = ltrim($path, '/');
    $base = sabBasePath();
    if ($base === '') {
        return ($path === '' ? '/' : '/' . $path) . $query . $hash;
    }
    return ($path === '' ? $base . '/' : $base . '/' . $path) . $query . $hash;
}

function sabAbsolute(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . sabHref($path);
}

function sabProjectUrl(string $slug): string
{
    return sabHref('project/' . rawurlencode($slug));
}

function sabBlogUrl(string $slug): string
{
    return sabHref('blog/' . rawurlencode($slug));
}

function sabMediaUrl(string $path): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return sabHref($path);
}

function sabWhatsApp(string $text = 'Hello Sadaf Al Bahr, I would like a quote for wallpaper, carpet or curtains.'): string
{
    return 'https://wa.me/' . SAB_WHATSAPP . '?text=' . rawurlencode($text);
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
