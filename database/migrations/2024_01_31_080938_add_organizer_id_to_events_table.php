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
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('organizer_id')->nullable()->after('id');
            $table->foreign('organizer_id')->references('user_id')->on('organizers')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organizer_id']);
            $table->dropColumn('organizer_id');
        });
    }
};
