<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendor_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('vendor_name')->nullable();
            $table->text('vendor_gst_no')->nullable();
            $table->string('vendor_email', 200)->nullable();
            $table->string('vendor_mobile_no', 100)->nullable();
            $table->text('vendor_address')->nullable();
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_details');
    }
};
