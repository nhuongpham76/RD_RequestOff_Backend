<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignToRequestReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_review', function (Blueprint $table) {
            $table->foreign('request_id', 'request_review_ibfk_1')
                ->references('id')
                ->on('requests')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
            $table->foreign('user_id', 'request_review_ibfk_2')
                ->references('id')
                ->on('users')
                ->onUpdate('RESTRICT')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_review', function (Blueprint $table) {
            $table->dropForeign('request_review_ibfk_1');
            $table->dropForeign('request_review_ibfk_2');
        });
    }
}
