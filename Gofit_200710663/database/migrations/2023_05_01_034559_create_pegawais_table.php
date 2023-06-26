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
        CREATE TRIGGER pegawais_id_trigger
        BEFORE INSERT ON pegawais
        FOR EACH ROW
        BEGIN
            SET @next_id = (SELECT IFNULL(MAX(RIGHT(id, LOCATE('P', REVERSE(id)) - 1)), 0) + 1 FROM pegawais);
            IF( @next_id < 10 ) THEN
                SET NEW.id = CONCAT('P', LPAD(@next_id, 2, '0'));
            ELSE
                SET NEW.id = CONCAT('P', @next_id);
            END IF;
        END
    ";
        Schema::create('pegawais', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            $table->string('nama');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('role');
            $table->date('tanggal_kelahiran');
            $table->string('password');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('deleted_at')->nullable()->default(null);
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
        Schema::dropIfExists('pegawais');
    }
};
