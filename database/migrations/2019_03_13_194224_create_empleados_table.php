<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpleadosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('empleados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identificacion', 100)->unique();
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->string('celular', 15);
            $table->string('email', 100);
            $table->string('sexo', 15);
            $table->string('user_change', 50);
            $table->double('saldofavor')->default(0);
            $table->double('saldocontra')->default(0);
            $table->integer('turnos')->default(0);
            $table->timestamps();
        });
        Schema::create('category_empleado', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('empleado_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('empleado_id')->references('id')->on('empleados')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('empleados');
    }

}
