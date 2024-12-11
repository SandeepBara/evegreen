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
        Schema::create('roll_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('roll_no')->unique('roll_details_roll_no_key');
            $table->date('purchase_date')->default(DB::raw("now()"));
            $table->bigInteger('vendor_id');
            $table->decimal('roll_size', 18);
            $table->decimal('roll_gsm', 18);
            $table->text('roll_color')->default('White');
            $table->decimal('roll_length', 18);
            $table->decimal('net_weight', 18);
            $table->decimal('gross_weight', 18);
            $table->decimal('w', 18)->nullable();
            $table->decimal('l', 18)->nullable();
            $table->decimal('g', 18)->nullable();
            $table->bigInteger('for_client_id')->nullable();
            $table->text('printing_description')->nullable();
            $table->bigInteger('bag_type_id')->nullable();
            $table->string('bag_units', 50)->nullable();

            $table->boolean('is_schedule_for_print')->default(false);
            $table->date('schedule_date_for_print')->nullable();
            $table->bigInteger('printing_machine_id')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printing_date')->nullable();
            $table->decimal('weight_after_printing', 18)->nullable();

            $table->boolean('is_schedule_for_cutting')->default(false);
            $table->date('schedule_date_for_cutting')->nullable();
            $table->bigInteger('cutting_machine_id')->nullable();
            $table->boolean('is_roll_cut')->default(false);
            $table->date('cutting_date')->nullable();

            $table->date('estimated_despatch_date')->nullable()->comment('roll delivery date');
            $table->boolean('is_despatch')->default(false);
            $table->timestamp('despatch_date')->nullable();
            
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
        Schema::dropIfExists('roll_details');
    }
};
