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
        Schema::create('seats', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('row');
            $table->integer('number');
            $table->boolean('is_booked')->default(false);
            $table->unsignedBigInteger('event_session_id');
            $table->foreign('event_session_id')->references('id')->on('event_sessions')->onDelete('cascade');
            $table->decimal('price', 8, 2);
            $table->unsignedBigInteger('cart_id')->nullable();
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('set null');
            $table->string('section')->nullable(); 
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
        Schema::dropIfExists('seats');
    }
};