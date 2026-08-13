<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatabaseController extends Controller
{
    public function index(): View
    {
        return view('admin.database.index', [
            'title' => 'Database Backup',
        ]);
    }

    public function backup(): StreamedResponse
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' AND name NOT LIKE 'migrations'");
            $tableKey = 'name';
        } else {
            $tables = DB::select('SHOW TABLES');
            if (empty($tables)) {
                abort(500, 'No tables found in the database.');
            }
            $tableKey = key((array) reset($tables));
        }

        return response()->streamDownload(function () use ($tables, $tableKey, $driver) {
            echo "-- RCMAA Database Backup\n";
            echo "-- Generated: " . now()->toDateTimeString() . "\n";
            echo "-- Driver: " . $driver . "\n";
            echo "-- Database: " . config('database.connections.' . $driver . '.database') . "\n";
            echo "-- --------------------------------------------------------\n\n";

            if ($driver === 'sqlite') {
                echo "PRAGMA foreign_keys = OFF;\n\n";
            } else {
                echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
            }

            foreach ($tables as $tableRow) {
                $tableName = $tableRow->$tableKey;

                // Get Create Table statement
                if ($driver === 'sqlite') {
                    $createStatement = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$tableName]);
                    $createKey = 'sql';
                    $createSql = $createStatement[0]->$createKey;
                } else {
                    $createStatement = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $createKey = 'Create Table';
                    $createSql = $createStatement[0]->$createKey;
                }

                echo "-- --------------------------------------------------------\n";
                echo "-- Table structure for table `{$tableName}`\n";
                echo "-- --------------------------------------------------------\n";
                echo "DROP TABLE IF EXISTS `{$tableName}`;\n";
                echo $createSql . ";\n\n";

                // Get all rows
                $rows = DB::table($tableName)->get();
                if ($rows->isNotEmpty()) {
                    echo "-- Dumping data for table `{$tableName}`\n";
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);

                        $values = array_values($rowArray);
                        $escapedValues = array_map(function($val) use ($driver) {
                            if (is_null($val)) {
                                return 'NULL';
                            }
                            if ($driver === 'sqlite') {
                                // SQLite raw values escaping
                                return "'" . str_replace("'", "''", $val) . "'";
                            }
                            return DB::getPdo()->quote($val);
                        }, $values);

                        echo "INSERT INTO `{$tableName}` (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                    }
                    echo "\n";
                }
            }

            if ($driver === 'sqlite') {
                echo "PRAGMA foreign_keys = ON;\n";
            } else {
                echo "SET FOREIGN_KEY_CHECKS=1;\n";
            }
        }, 'backup-' . now()->format('Y-m-d-His') . '.sql', [
            'Content-Type' => 'application/sql',
        ]);
    }
}
