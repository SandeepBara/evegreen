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
        Schema::table('roll_print_colors', function (Blueprint $table) {
            $table->foreign(['roll_id'], 'roll_print_colors_roll_id_fkey')->references(['id'])->on('roll_details')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roll_print_colors', function (Blueprint $table) {
            $table->dropForeign('roll_print_colors_roll_id_fkey');
        });
    }
};
