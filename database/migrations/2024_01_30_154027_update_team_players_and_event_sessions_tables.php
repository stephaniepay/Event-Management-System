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
        Schema::table('team_players', function (Blueprint $table) {
            $table->boolean('is_winner')->default(false);
        });

        Schema::table('event_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('winner_player_id')->nullable();
            $table->foreign('winner_player_id')->references('id')->on('team_players');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('team_players', function (Blueprint $table) {
            $table->dropColumn('is_winner');
        });

        Schema::table('event_sessions', function (Blueprint $table) {
            $table->dropForeign(['winner_player_id']);
            $table->dropColumn('winner_player_id');
        });
    }
};
