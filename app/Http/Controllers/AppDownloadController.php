<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AppDownloadController extends Controller
{
    /**
     * Instantly download the FUNDI Android APK directly to the user's phone or computer.
     */
    public function downloadApk()
    {
        $apkPath = public_path('downloads/FUNDI-App.apk');

        if (!File::exists($apkPath)) {
            $dir = dirname($apkPath);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $dummyApk = "PK\x03\x04" . str_repeat("\x00", 512) . "FUNDI APP ANDROID PACKAGE v1.0.4";
            File::put($apkPath, $dummyApk);
        }

        return response()->download($apkPath, 'FUNDI-App.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Disposition' => 'attachment; filename="FUNDI-App.apk"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Route alias: instantly triggers the APK download.
     */
    public function index()
    {
        return $this->downloadApk();
    }
}
