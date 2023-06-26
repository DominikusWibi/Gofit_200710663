<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('jadwal_harians', function (Blueprint $table) {
            $table->id();
            $table->string('id_instruktur');
            $table->foreignId('id_jadwal_umum')->constrained('jadwal_umums')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('status');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }
     /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_harians');
    }
};
