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
        Schema::create('bag_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bag_type', 50);
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->text('formula_to_bag_by_length')->nullable();
            $table->text('formula_to_bag_by_weight')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_types');
    }
};
