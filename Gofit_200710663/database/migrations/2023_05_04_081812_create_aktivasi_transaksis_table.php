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
    public function up()
    {
        $triggerSQL = "
        CREATE TRIGGER transaksi_no_struk_trigger
        BEFORE INSERT ON aktivasi_transaksis
        FOR EACH ROW
        BEGIN
            DECLARE year_prefix VARCHAR(2);
            DECLARE month_prefix VARCHAR(2);
            SET @next_id = (SELECT IFNULL(MAX(RIGHT(id, LOCATE('.', REVERSE(id)) - 1)), 0) + 1 FROM aktivasi_transaksis);
            SET year_prefix = DATE_FORMAT(NEW.created_at, '%y');
            SET month_prefix = DATE_FORMAT(NEW.created_at, '%m');
            IF( @next_id < 10 ) THEN
                SET NEW.id = CONCAT(year_prefix, '.', month_prefix, '.', LPAD(@next_id, 2, '0'));
            ELSE
                SET NEW.id = CONCAT(year_prefix, '.', month_prefix, '.', @next_id);
            END IF;
        END
    ";
        Schema::create('aktivasi_transaksis', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            $table->string('id_pegawai')->nullable()->default(null);
            $table->foreign('id_pegawai')->references('id')->on('pegawais')->onDelete('cascade');
            $table->foreignId('id_kelas')->constrained('kelas')->cascadeOnUpdate()->cascadeOnDelete()->nullable()->default(null); 
            $table->string('id_member')->nullable()->default(null);
            $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
            $table->integer('id_jenis_transaksi');
            $table->foreignId('id_promo')->constrained('promos')->cascadeOnUpdate()->cascadeOnDelete()->nullable()->default(null);  
            $table->integer('nominal_transaksi');
            $table->integer('total_transaksi')->nullable()->default(null);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
        DB::statement($triggerSQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivasi_transaksis');
    }
};
