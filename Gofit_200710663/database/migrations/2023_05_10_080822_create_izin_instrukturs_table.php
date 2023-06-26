<?php
use Illuminate\Support\Facades\DB;
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
        Schema::create('izin_instrukturs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jadwal_umum')->index()->constrained('jadwal_umums')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_instruktur_berhalangan')->constrained('instrukturs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_instruktur_pengganti')->constrained('instrukturs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('izin');
            $table->string('keterangan');
            $table->integer('konfirmasi')->default(0);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_instrukturs');
    }
};
