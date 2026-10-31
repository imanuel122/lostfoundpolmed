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
        Schema::table('claims', function (Blueprint $table) {
            $table->timestamp('handed_over_at')->nullable()->after('reviewed_at');
            $table->timestamp('confirmed_at')->nullable()->after('handed_over_at');
            $table->text('dispute_note')->nullable()->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn(['handed_over_at', 'confirmed_at', 'dispute_note']);
        });
    }
};
