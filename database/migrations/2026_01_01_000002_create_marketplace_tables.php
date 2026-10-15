<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('company_name'); $table->string('address')->nullable(); $table->string('phone')->nullable();
            $table->text('description')->nullable(); $table->string('location')->nullable(); $table->string('food_type')->nullable();
            $table->timestamps();
        });
        Schema::create('menus', function (Blueprint $table) {
            $table->id(); $table->foreignId('merchant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); $table->text('description'); $table->string('photo')->nullable();
            $table->decimal('price', 12, 2); $table->boolean('is_available')->default(true); $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained()->cascadeOnDelete(); $table->date('delivery_date');
            $table->string('delivery_address'); $table->text('notes')->nullable();
            $table->string('status')->default('pending'); $table->decimal('total', 12, 2); $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('menu_name'); $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2); $table->decimal('subtotal', 12, 2); $table->timestamps();
        });
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique(); $table->timestamp('issued_at'); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('invoices'); Schema::dropIfExists('order_items'); Schema::dropIfExists('orders');
        Schema::dropIfExists('menus'); Schema::dropIfExists('merchants');
    }
};
