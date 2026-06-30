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
        Schema::create('grocery_item', function (Blueprint $table) {
            $table->id();
            $table->String('product_name');
            $table->String('catgory_id')->nullable();
            $table->String('image');
            $table->String('price');
            $table->String('Stock_quantity');
            $table->Date('expiry_date');
            $table->String('description');
            $table->enum('status',['pending','confirmed','delivered','cancelled']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_item');
    }
};
