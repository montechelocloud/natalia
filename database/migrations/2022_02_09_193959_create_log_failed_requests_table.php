<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogFailedRequestsTable extends Migration
{
    /**
     * Crea la tabla para el registro de las peticiones fallidas.
     * @author Edwin David Sanchez Balbin
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_failed_requests', function (Blueprint $table) {
            $table->id();
            $table->text('request_url')->comment('Url de donde se realia la petición.');
            $table->longText('request_data')->comment('Datos de la petición.');
            $table->integer('status_code')->comment('Codigo de error.');
            $table->text('messages')->nullable()->comment('Mensaje del error.');
            $table->string('detail')->nullable()->comment('Detalle del error.');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_failed_requests');
    }
}
