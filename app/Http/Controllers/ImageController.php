<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    protected array $templates = [
        'imperial' => [
            'path' => 'public/image/imperial_grace_book_v5.png',
            'width' => 1024,
            'height' => 1024,
        ],
        'teaching' => [
            'path' => 'public/image/registry_book_yellow_v6.png',
            'width' => 1024,
            'height' => 1024,
        ],
        'registry' => [
            'path' => 'public/image/registry_book_yellow_v2.png',
            'width' => 1024,
            'height' => 1024,
        ],
    ];

    public function folder(Request $request, string $template)
    {
        if (!isset($this->templates[$template])) {
            abort(404);
        }

        $cfg = $this->templates[$template];
        $lines = $request->input('lines', []);
        $fontPath = 'C:\Windows\Fonts\kaiu.ttf';

        if (!is_array($lines) || empty($lines)) {
            abort(400, 'No text lines provided');
        }

        $cacheKey = 'folder_img_' . $template . '_' . md5(json_encode($lines));
        $cachePath = storage_path('app/folder_cache/' . $cacheKey . '.webp');

        if (file_exists($cachePath)) {
            return response()->file($cachePath, [
                'Content-Type' => 'image/webp',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $bg = imagecreatefromjpeg(base_path($cfg['path']));
        if (!$bg) {
            abort(500, 'Failed to load background');
        }

        $w = $cfg['width'];
        $h = $cfg['height'];

        $img = imagecreatetruecolor($w, $h);
        imagecopy($img, $bg, 0, 0, 0, 0, $w, $h);
        imagedestroy($bg);

        // Enable alpha blending
        imagealphablending($img, true);
        imagesavealpha($img, false);

        foreach ($lines as $line) {
            $text = $line['text'] ?? '';
            $size = $line['size'] ?? 40;
            $color = $this->parseColor($img, $line['color'] ?? '#ffffff');
            $x = $line['x'] ?? ($w / 2);
            $y = $line['y'] ?? ($h / 2);
            $angle = $line['angle'] ?? 0;
            $align = $line['align'] ?? 'center';

            if (empty($text)) continue;

            // Calculate bounding box for alignment
            $bbox = imagettfbbox($size, $angle, $fontPath, $text);
            $tw = $bbox[2] - $bbox[0];
            $th = $bbox[1] - $bbox[7];

            if ($align === 'center') {
                $tx = $x - $tw / 2;
            } elseif ($align === 'right') {
                $tx = $x - $tw;
            } else {
                $tx = $x;
            }
            $ty = $y + $th / 2;

            // Draw drop shadow if requested
            if (!empty($line['shadow'])) {
                $shadowColor = $this->parseColor($img, $line['shadow']);
                imagettftext($img, $size, $angle, (int)$tx + 2, (int)$ty + 2, $shadowColor, $fontPath, $text);
            }

            imagettftext($img, $size, $angle, (int)$tx, (int)$ty, $color, $fontPath, $text);
        }

        // Ensure cache directory exists
        $cacheDir = storage_path('app/folder_cache');
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        imagewebp($img, $cachePath, 80);
        imagedestroy($img);

        return response()->file($cachePath, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    protected function parseColor($img, string $hex): int
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return imagecolorallocate($img, $r, $g, $b);
    }

    public function generateAll()
    {
        $templates = ['imperial', 'teaching', 'registry'];
        $results = [];

        $allLines = [
            'imperial' => [
                // Homepage: 重大皇恩專區
                [
                    ['text' => '重大皇恩專區', 'size' => 100, 'y' => 440, 'color' => '#fbbf24', 'shadow' => 'rgba(0,0,0,0.5)'],
                ],
                // Homepage: 未求得重大皇恩專區 (two lines)
                [
                    ['text' => '未求得', 'size' => 60, 'y' => 400, 'color' => '#fbbf24', 'shadow' => 'rgba(0,0,0,0.5)'],
                    ['text' => '重大皇恩專區', 'size' => 70, 'y' => 490, 'color' => '#fbbf24', 'shadow' => 'rgba(0,0,0,0.5)'],
                ],
                // Sub-folder
                [
                    ['text' => '重大皇恩專區', 'size' => 50, 'y' => 350, 'color' => '#fbbf24'],
                    ['text' => 'FOLDER_NAME', 'size' => 80, 'y' => 460, 'color' => '#fbbf24', 'shadow' => 'rgba(0,0,0,0.3)'],
                ],
            ],
            'teaching' => [
                // Homepage: 父皇仙師每日開示 (two lines)
                [
                    ['text' => '父皇仙師', 'size' => 90, 'y' => 400, 'color' => '#ffffff', 'shadow' => 'rgba(0,0,0,0.5)'],
                    ['text' => '每日開示', 'size' => 90, 'y' => 510, 'color' => '#ffffff', 'shadow' => 'rgba(0,0,0,0.5)'],
                ],
                // Homepage: 父皇仙師開示載錄 (two lines)
                [
                    ['text' => '父皇仙師', 'size' => 90, 'y' => 400, 'color' => '#ffffff', 'shadow' => 'rgba(0,0,0,0.5)'],
                    ['text' => '開示載錄', 'size' => 90, 'y' => 510, 'color' => '#ffffff', 'shadow' => 'rgba(0,0,0,0.5)'],
                ],
            ],
            'registry' => [
                // Homepage: 法寶登記專區 + 重大皇恩登記簿
                [
                    ['text' => '法寶登記專區', 'size' => 55, 'y' => 350, 'color' => '#8b0000'],
                    ['text' => '重大皇恩', 'size' => 65, 'y' => 440, 'color' => '#dc2626'],
                    ['text' => '登記簿', 'size' => 65, 'y' => 520, 'color' => '#dc2626'],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            $lines = $allLines[$tpl] ?? [];
            foreach ($lines as $idx => $textLines) {
                $req = new Request(['lines' => $textLines]);
                try {
                    $resp = $this->folder($req, $tpl);
                    $results[] = "$tpl/$idx: OK";
                } catch (\Exception $e) {
                    $results[] = "$tpl/$idx: " . $e->getMessage();
                }
            }
        }

        return response()->json($results);
    }
}
