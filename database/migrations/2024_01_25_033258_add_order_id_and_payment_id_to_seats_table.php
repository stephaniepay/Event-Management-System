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
        Schema::table('seats', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->after('event_session_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            $table->unsignedBigInteger('payment_id')->nullable()->after('order_id');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seats', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');

            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
