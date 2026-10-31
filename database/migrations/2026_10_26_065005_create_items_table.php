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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('jenis', ['temuan', 'hilang']);
            $table->string('judul', 150);
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('location_id')->constrained('locations');
            $table->string('detail_lokasi', 200)->nullable();
            $table->date('tanggal_kejadian');
            $table->text('deskripsi')->nullable();
            $table->text('ciri_rahasia')->nullable();
            $table->string('lokasi_penyimpanan', 100)->nullable();
            $table->enum('status', ['aktif', 'verifikasi', 'dikembalikan', 'kedaluwarsa', 'dihapus'])
                ->default('aktif');
            $table->date('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_claim_id')->nullable(); // FK ditambah nanti
            $table->timestamps();

            $table->index('jenis');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
