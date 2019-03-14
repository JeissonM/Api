<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientohistorialcajasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('movimientohistorialcajas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->string('descripcion');
            $table->double('monto');
            $table->string('tipo');
            $table->string('user_change', 50);
            $table->unsignedBigInteger('historialcaja_id');
            $table->foreign('historialcaja_id')->references('id')->on('historialcajas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('movimientohistorialcajas');
    }

}
