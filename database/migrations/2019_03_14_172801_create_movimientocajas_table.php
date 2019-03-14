<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientocajasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('movimientocajas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->string('descripcion');
            $table->double('monto');
            $table->string('tipo');
            $table->string('user_change', 50);
            $table->unsignedBigInteger('caja_id');
            $table->foreign('caja_id')->references('id')->on('cajas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('movimientocajas');
    }

}
