<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackupDatabaseCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-database-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CSV backup of the sensor_logs table in public/assets/dbbackup.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $relativeBackupPath = 'assets/dbbackup';
        // $timestamp= now()->setTimezone('Asia/Manila')->format('Y-m-d_H-i-s');
        $timestamp = 'Cybermate' . date('Y-m-d_H-i-s');
        $backupDirectory = public_path($relativeBackupPath . DIRECTORY_SEPARATOR . $timestamp);

        File::ensureDirectoryExists($backupDirectory);

        $tableName = 'sensor_logs';

        if (!Schema::hasTable($tableName)) {
            $this->warn("Table not found: {$tableName}");

            return self::SUCCESS;
        }

        $csvPath = $backupDirectory . DIRECTORY_SEPARATOR . $tableName . '.csv';
        $file = fopen($csvPath, 'wb');

        if ($file === false) {
            $this->error("Unable to write backup file for table: {$tableName}");

            return self::FAILURE;
        }

        $rows = DB::table($tableName)->get();

        if ($rows->isNotEmpty()) {
            $firstRow = (array) $rows->first();
            fputcsv($file, array_keys($firstRow));

            foreach ($rows as $row) {
                fputcsv($file, array_values((array) $row));
            }
        } else {
            $columns = Schema::getColumnListing($tableName);

            if (!empty($columns)) {
                fputcsv($file, $columns);
            }
        }

        fclose($file);
        $this->line("Backed up table: {$tableName}");


        $this->info("Database CSV backup completed: {$backupDirectory}");

        return self::SUCCESS;
    }
}