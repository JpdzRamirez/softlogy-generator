<?php

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
        Schema::create('sql_config', function (Blueprint $table) {
            $table->id();            
            $table->integer('id_consecutivo' );
            $table->string('nombre', 500);
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('razonSocial', 500);
            $table->string('direccion', 500);
            $table->string('telefono', 500);
            $table->string('marca', 500);
            $table->string('numResolucion', 500);
            $table->string('prefijo', 500);
            $table->date('fechaFacIni');
            $table->date('fechaFacFin');
            $table->integer('rangoInicial');
            $table->integer('rangoFinal');
            $table->integer('Folio');
            $table->integer('Tipo');
            $table->text('NumCertificado')->nullable();
            $table->boolean('diferencias')->default(false);            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sql_config');
    }
};
