<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovimientosempleadosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('movimientosempleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origen', 50);
            $table->string('tipo', 50);
            $table->double('monto');
            $table->timestamp('fechatransaccion');
            $table->string('user_change', 50);
            $table->bigInteger('empleado_id')->unsigned();
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('movimientosempleados');
    }

}
