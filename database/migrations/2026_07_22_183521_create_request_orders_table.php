<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_order', function (Blueprint $table) {
            $table->id('id_ro');
            $table->unsignedBigInteger('id_barang');
            $table->integer('qty_request');
            $table->date('tgl_request');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_order');
    }
};
