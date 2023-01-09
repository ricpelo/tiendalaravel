<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_factura', function (Blueprint $table) {
            $table->foreignId('factura_id')->constrained();
            $table->foreignId('articulo_id')->constrained();
            $table->integer('cantidad')->default(1);
            $table->primary(['factura_id', 'articulo_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_factura');
    }
};
