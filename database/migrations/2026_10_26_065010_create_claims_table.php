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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('claimant_id')->constrained('users');
            $table->enum('jenis_klaim', ['klaim_pemilik', 'lapor_temuan']);
            $table->text('bukti_deskripsi');
            $table->string('lokasi_barang_sekarang', 150)->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai'])
                ->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
