<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Stores the seller's starting capital / cash account
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('opening_balance', 15, 2)->default(0); // starting cash
            $table->decimal('current_balance', 15, 2)->default(0); // live balance
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Every financial movement recorded here
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', [
                'opening_balance', // initial cash deposited
                'inventory_purchase', // cash spent buying stock
                'sale_revenue',      // cash earned from selling
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2); // running balance snapshot
            $table->string('description');
            $table->date('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('accounts');
    }
};
