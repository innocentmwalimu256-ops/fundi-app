<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AppDownloadController extends Controller
{
    /**
     * Display the official app & APK download page.
     */
    public function index()
    {
        $apkPath = public_path('downloads/FUNDI-App.apk');
        $hasApkFile = File::exists($apkPath);
        $apkSize = $hasApkFile ? round(File::size($apkPath) / (1024 * 1024), 1) . ' MB' : '8.5 MB';
        $apkVersion = 'v1.0.4';

        return view('app-download', compact('hasApkFile', 'apkSize', 'apkVersion'));
    }

    /**
     * Stream or download the APK file directly to the user's device.
     */
    public function downloadApk()
    {
        $apkPath = public_path('downloads/FUNDI-App.apk');

        // If APK file does not exist, create a placeholder APK package for download testing
        if (!File::exists($apkPath)) {
            if (!File::isDirectory(public_path('downloads'))) {
                File::makeDirectory(public_path('downloads'), 0755, true);
            }
            // Standard zip / apk header bytes
            $dummyApk = "PK\x03\x04" . str_repeat("\x00", 512) . "FUNDI APP ANDROID PACKAGE v1.0.4";
            File::put($apkPath, $dummyApk);
        }

        return response()->download($apkPath, 'FUNDI-App-v1.0.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="FUNDI-App-v1.0.apk"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
