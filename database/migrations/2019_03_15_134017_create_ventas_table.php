<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('ventas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('base')->default(0);
            $table->double('descuentoAplicado')->default(0);
            $table->timestamp('fecha');
            $table->double('gananciaNegocio')->default(0);
            $table->double('gananciaEmpleado')->default(0);
            $table->double('valorAgregado')->default(0);
            $table->string('cliente', 150);
            $table->double('total')->default(0);
            $table->string('user_change', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('ventas');
    }

}
