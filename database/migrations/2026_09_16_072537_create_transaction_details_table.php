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
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->unsignedInteger('qty');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });
    }

public function down(): void
{
    Schema::table('transaction_details', function (Blueprint $table) {
        $table->dropIndex(['product_id']); // <-- Tambahkan baris ini
    });
}
};
