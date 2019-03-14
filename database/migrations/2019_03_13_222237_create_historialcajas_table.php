<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorialcajasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('historialcajas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('dineroCaja')->default(0);
            $table->double('egresos')->default(0);
            $table->date('fechaApertura')->nullable();
            $table->date('fechaCierre')->nullable();
            $table->double('gananciaLocal')->default(0);
            $table->boolean('inconsistencia')->default(false);
            $table->double('ingresos')->default(0);
            $table->double('montoInicial')->default(0);
            $table->double('montoAgregado')->default(0);
            $table->double('montoConfirmado')->default(0);
            $table->string('user_change', 50);
            $table->string('anterior', 10)->default('SI');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('historialcajas');
    }

}
