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
        Schema::create('deliveries', function (Blueprint $table) {
             $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->String('delivery_boy_name');
            $table->date('delivery_date')->nullable;
            $table->enum('status',['assigned','out_for_delivery','delivered','failed'])->default('assigned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
