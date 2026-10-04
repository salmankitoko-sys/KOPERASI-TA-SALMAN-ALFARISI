<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdo = DB::connection()->getPdo();
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

// Check which orphan plural tables exist
$orphans = ['pembiayaans', 'tokos'];
foreach ($orphans as $table) {
    $exists = DB::select("SHOW TABLES LIKE ?", [$table]);
    if (!empty($exists)) {
        // Check if empty
        $count = DB::table($table)->count();
        echo "Table `$table` exists with $count rows. ";
        if ($count == 0) {
            $pdo->exec("DROP TABLE `$table`");
            echo "DROPPED.\n";
        } else {
            echo "SKIPPED (has data).\n";
        }
    } else {
        echo "Table `$table` does not exist.\n";
    }
}

// Check notifikasi_angsurans - might be intentional but let's check
$exists = DB::select("SHOW TABLES LIKE 'notifikasi_angsurans'");
if (!empty($exists)) {
    echo "Table `notifikasi_angsurans` exists.\n";
    $fks = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'notifikasi_angsurans' AND REFERENCED_TABLE_NAME IS NOT NULL
    ", [DB::connection()->getDatabaseName()]);
    foreach ($fks as $fk) {
        echo "  FK: {$fk->CONSTRAINT_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
echo "\nCleanup done!\n";

