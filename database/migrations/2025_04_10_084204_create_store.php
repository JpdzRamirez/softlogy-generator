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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('entities_id');                        
            $table->string('tienda');
            $table->string('id_tienda');
            $table->string('id_brand');
            $table->text('token_endpoint');
            $table->text('token_pass');
            $table->text('token_user');
            $table->text('endPoint');
            $table->text('endPoint_solicitudes');
            $table->text('endPoint_online');
            $table->text('endPoint_RPD');
            $table->timestamps();

            // Definir la clave foránea            
            $table->foreign('user_id')->references('id')->on('users');
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
