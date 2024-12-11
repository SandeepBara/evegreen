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
        Schema::table('roll_details', function (Blueprint $table) {
            $table->foreign(['bag_type_id'], 'roll_details_bag_type_id_fkey')->references(['id'])->on('bag_types')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['for_client_id'], 'roll_details_for_client_id_fkey')->references(['id'])->on('client_details')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['vendor_id'], 'roll_details_vendor_id_fkey')->references(['id'])->on('vendor_details')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roll_details', function (Blueprint $table) {
            $table->dropForeign('roll_details_bag_type_id_fkey');
            $table->dropForeign('roll_details_for_client_id_fkey');
            $table->dropForeign('roll_details_vendor_id_fkey');
        });
    }
};
