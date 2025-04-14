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
        Schema::create('store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entitie_id');
            $table->string('entitie', 500);
            $table->text('token_User');
            $table->text('token_endpoint');
            $table->text('token_pass');
            $table->text('endPoint');
            $table->text('endPoint_solicitudes');
            $table->text('endPoint_online');
            $table->text('endPoint_RPD');
            $table->unsignedBigInteger('sqlConfig_id');
            $table->timestamps();

            // Definir la clave foránea
            $table->foreign('entitie_id')->references('id_entitie')->on('store_config');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store');
    }
};
