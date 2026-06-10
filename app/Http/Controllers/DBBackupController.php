<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DBBackupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $backupPath = public_path('assets/dbbackup');
        $backups = collect();

        if (File::exists($backupPath)) {
            foreach (File::directories($backupPath) as $directory) {
                $backups->push([
                    'name' => basename($directory),
                    'is_directory' => true,
                    'modified_at' => File::lastModified($directory),
                ]);
            }

            foreach (File::files($backupPath) as $file) {
                $backups->push([
                    'name' => $file->getFilename(),
                    'is_directory' => false,
                    'modified_at' => $file->getMTime(),
                ]);
            }
        }

        $backups = $backups
            ->sortByDesc('modified_at')
            ->take(10)
            ->values()
            ->map(function (array $backup): array {
                $backup['formatted_date'] = Carbon::createFromTimestamp($backup['modified_at'])->format('Y-m-d H:i:s');

                return $backup;
            });

        return view('pages.dbbackup.index', compact('backups'));
    }

    /**
     * Download the selected backup file.
     */
    public function download(string $name)
    {
        if (basename($name) !== $name) {
            abort(404);
        }

        $backupPath = public_path('assets/dbbackup');
        $targetPath = $backupPath . DIRECTORY_SEPARATOR . $name;

        if (!File::exists($targetPath)) {
            abort(404);
        }

        if (File::isDirectory($targetPath)) {
            $tempZipDirectory = storage_path('app/tmp');
            File::ensureDirectoryExists($tempZipDirectory);

            $zipFilePath = $tempZipDirectory . DIRECTORY_SEPARATOR . $name . '.zip';
            $zip = new ZipArchive();

            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                abort(500, 'Unable to create backup archive.');
            }

            $basePathLength = strlen($targetPath) + 1;

            foreach (File::allFiles($targetPath) as $backupFile) {
                $fullPath = $backupFile->getRealPath();

                if ($fullPath === false) {
                    continue;
                }

                $zip->addFile($fullPath, substr($fullPath, $basePathLength));
            }

            $zip->close();

            return response()->download($zipFilePath, $name . '.zip')->deleteFileAfterSend(true);
        }

        return response()->download($targetPath);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
