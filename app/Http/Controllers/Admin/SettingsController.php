<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $tables = $this->getDatabaseTables();

        // Get row count for each table
        $tableData = [];
        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->count();
                $tableData[] = [
                    'name' => $table,
                    'count' => $count
                ];
            } catch (\Exception $e) {
                // Skip tables that cause errors (like views)
                continue;
            }
        }

        // Get backup files
        $backupDir = storage_path('app/backups');
        $backupFiles = [];

        if (file_exists($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($backupDir . '/' . $file)) {
                    $filePath = $backupDir . '/' . $file;
                    $backupFiles[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'modified' => date('Y-m-d H:i:s', filemtime($filePath))
                    ];
                }
            }
        }

        return view('admin.settings.index', compact('tableData', 'backupFiles'));
    }

    /**
     * Reset a specific table.
     */
    public function resetTable(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string|in:' . implode(',', $this->getDatabaseTables())
        ]);

        $tableName = $request->input('table_name');

        try {
            // Disable foreign key checks to allow truncation
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Get the current row count before reset
            $oldCount = DB::table($tableName)->count();

            // Truncate the table
            DB::table($tableName)->truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \Log::info("Table {$tableName} reset by admin user " . auth()->id() . ". Rows deleted: {$oldCount}");

            return response()->json([
                'success' => true,
                'message' => "Table {$tableName} reset successfully. {$oldCount} rows deleted.",
                'new_count' => 0
            ]);
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'success' => false,
                'message' => 'Error resetting table: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a database backup.
     */
    public function createBackup()
    {
        try {
            // Create backups directory if it doesn't exist
            $backupDir = storage_path('app/backups');
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $path = storage_path("app/backups/{$filename}");

            // For a simple backup, we'll use a different approach
            // Generate a SQL dump manually for common tables
            $tables = $this->getDatabaseTables();

            $sql = "-- Database Backup Generated on " . now()->format('Y-m-d H:i:s') . "\n\n";

            foreach ($tables as $table) {
                if (in_array($table, ['migrations', 'cache', 'cache_locks', 'job_batches', 'jobs', 'sessions'])) {
                    continue; // Skip system tables
                }

                $sql .= "-- Backup for table: {$table}\n";
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";

                // Get table structure - validate table name first
                $allowedTables = $this->getDatabaseTables();
                if (!in_array($table, $allowedTables)) {
                    continue; // Skip invalid tables
                }

                // Validate table name to prevent SQL injection
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $table)) {
                    continue; // Skip tables with invalid names
                }

                $createResult = DB::select(DB::raw("SHOW CREATE TABLE `{$table}`"));
                if (!empty($createResult)) {
                    $sql .= $createResult[0]->{'Create Table'} . ";\n\n";

                    // Get table data with proper table name validation
                    $rows = DB::table($table)->get();
                    if ($rows->isNotEmpty()) {
                        $sql .= "INSERT INTO `{$table}` VALUES \n";
                        $values = [];
                        foreach ($rows as $row) {
                            $rowValues = [];
                            foreach ($row->getAttributes() as $value) {
                                if ($value === null) {
                                    $rowValues[] = 'NULL';
                                } else {
                                    $rowValues[] = "'" . addslashes($value) . "'";
                                }
                            }
                            $values[] = "(" . implode(', ', $rowValues) . ")";
                        }
                        $sql .= implode(",\n", $values) . ";\n\n";
                    }
                }
            }

            file_put_contents($path, $sql);

            return response()->json([
                'success' => true,
                'message' => 'Database backup created successfully.',
                'filename' => $filename
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore database from a backup file.
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        $filename = $request->input('filename');
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found.'
            ], 404);
        }

        try {
            // Disable foreign key checks to allow restoration
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Read and execute the SQL file
            $sql = file_get_contents($path);

            // Validate the backup file format before execution
            if (!$this->isValidBackupFile($sql)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid backup file format. File has been tampered with.'
                ], 400);
            }

            // Split the SQL into statements
            $statements = array_filter(
                array_map('trim', explode(";\n", $sql)),
                function($stmt) { return !empty($stmt); }
            );

            foreach ($statements as $statement) {
                if (trim($statement) !== '') {
                    // Validate each SQL statement before execution
                    if ($this->isAllowedSqlStatement($statement)) {
                        DB::unprepared($statement . ';');
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Backup file contains prohibited SQL statements.'
                        ], 400);
                    }
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            \Log::info("Database restored from backup {$filename} by admin user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Database restored successfully from backup.'
            ]);
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'success' => false,
                'message' => 'Error restoring backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        $filename = $request->input('filename');
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found.'
            ], 404);
        }

        try {
            unlink($path);

            \Log::info("Backup file {$filename} deleted by admin user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Backup file deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting backup file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of database tables.
     */
    private function getDatabaseTables()
    {
        $tables = DB::select('SHOW TABLES');

        $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');

        $tableNames = [];
        foreach ($tables as $table) {
            if (isset($table->$tableColumn)) {
                $tableNames[] = $table->$tableColumn;
            }
        }

        return $tableNames;
    }

    /**
     * Validate if the backup file is in expected format
     */
    private function isValidBackupFile($sql)
    {
        // Check if the file starts with the expected backup comment
        if (!str_starts_with($sql, '-- Database Backup Generated on')) {
            return false;
        }

        // Check for known malicious patterns
        $forbiddenPatterns = [
            '/DROP\s+DATABASE/i',
            '/CREATE\s+DATABASE/i',
            '/ALTER\s+DATABASE/i',
            '/DROP\s+USER/i',
            '/CREATE\s+USER/i',
            '/GRANT\s+/i',
            '/REVOKE\s+/i',
            '/LOAD_FILE/i',
            '/OUTFILE/i',
            '/SLEEP/i',
            '/BENCHMARK/i',
            '/EXEC/i',
            '/XP_/i', // SQL Server procedures
        ];

        foreach ($forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the SQL statement is allowed for execution
     */
    private function isAllowedSqlStatement($statement)
    {
        // List of allowed SQL operations in a backup
        $allowedOperations = [
            'CREATE TABLE',
            'INSERT INTO',
            'DROP TABLE',
            'ALTER TABLE',
        ];

        $statement = trim(strtoupper($statement));

        foreach ($allowedOperations as $operation) {
            if (str_starts_with($statement, $operation)) {
                return true;
            }
        }

        // Additional check for potentially dangerous operations
        $forbiddenOperations = [
            'DROP DATABASE',
            'CREATE DATABASE',
            'ALTER DATABASE',
            'DROP USER',
            'CREATE USER',
            'GRANT',
            'REVOKE',
            'LOAD_FILE',
            'OUTFILE',
            'SLEEP',
            'BENCHMARK',
            'EXEC',
            'XP_',
        ];

        foreach ($forbiddenOperations as $operation) {
            if (str_contains($statement, $operation)) {
                return false;
            }
        }

        return true; // By default, allow if not explicitly forbidden and starts with an allowed operation
    }
}