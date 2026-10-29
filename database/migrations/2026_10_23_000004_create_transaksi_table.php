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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_transaksi', 50)->unique()->nullable();
            $table->dateTime('tanggal');
            $table->unsignedInteger('total');
            $table->enum('metode_pembayaran', ['tunai', 'debit', 'qris']);
            $table->unsignedInteger('uang_diterima')->nullable();
            $table->unsignedInteger('kembalian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
