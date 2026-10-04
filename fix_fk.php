<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pdo = DB::connection()->getPdo();
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

try {
    $pdo->exec('ALTER TABLE angsuran DROP FOREIGN KEY angsuran_pembiayaan_id_foreign');
    echo "Dropped old FK\n";
} catch (Exception $e) {
    echo "Drop failed (may not exist): " . $e->getMessage() . "\n";
}

$pdo->exec('ALTER TABLE angsuran ADD CONSTRAINT angsuran_pembiayaan_id_foreign FOREIGN KEY (pembiayaan_id) REFERENCES pembiayaan(id) ON DELETE CASCADE');
$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
echo "Foreign key fixed!\n";

