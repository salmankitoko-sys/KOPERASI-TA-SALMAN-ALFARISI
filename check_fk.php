<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$rows = DB::select("
    SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'angsuran' AND REFERENCED_TABLE_NAME IS NOT NULL
", [DB::connection()->getDatabaseName()]);

echo "Database: " . DB::connection()->getDatabaseName() . "\n\n";
foreach ($rows as $r) {
    echo "Constraint: {$r->CONSTRAINT_NAME}\n";
    echo "  {$r->TABLE_NAME}.{$r->COLUMN_NAME} -> {$r->REFERENCED_TABLE_NAME}.{$r->REFERENCED_COLUMN_NAME}\n\n";
}

// Also check if pembiayaans table exists
$tables = DB::select("SHOW TABLES");
echo "Tables in database:\n";
foreach ($tables as $t) {
    echo "  - " . reset($t) . "\n";
}

