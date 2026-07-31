<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    protected array $archMap = [
        'arm64-v8a'  => 'MyCampus-arm64-v8a.apk',
        'armeabi-v7a' => 'MyCampus-armeabi-v7a.apk',
        'x86_64'      => 'MyCampus-x86_64.apk',
        'universal'   => 'MyCampus.apk',
    ];

    public function downloadApk(Request $request, string $arch): BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        $arch = strtolower($arch);

        if (!isset($this->archMap[$arch])) {
            return response()->json([
                'success' => false,
                'message' => 'Arsitektur tidak valid. Pilihan: ' . implode(', ', array_keys($this->archMap)),
            ], 404);
        }

        $filename = $this->archMap[$arch];
        $filePath = public_path('apk/' . $filename);

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File APK tidak ditemukan untuk arsitektur ' . $arch,
            ], 404);
        }

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
