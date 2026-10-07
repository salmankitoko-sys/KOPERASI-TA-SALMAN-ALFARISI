<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $foreignKey = collect(Schema::getForeignKeys('angsuran'))
            ->first(fn (array $key) => in_array('pembiayaan_id', $key['columns'], true));

        if (
            $foreignKey !== null
            && $foreignKey['foreign_table'] === 'pembiayaan'
            && $foreignKey['foreign_columns'] === ['id']
            && strtolower($foreignKey['on_delete']) === 'cascade'
        ) {
            return;
        }

        Schema::table('angsuran', function (Blueprint $table) use ($foreignKey): void {
            if ($foreignKey !== null) {
                $table->dropForeign($foreignKey['name']);
            }

            $table->foreign('pembiayaan_id')
                ->references('id')
                ->on('pembiayaan')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
