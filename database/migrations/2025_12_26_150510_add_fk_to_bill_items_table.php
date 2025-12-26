<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->foreign('bill_id')
                ->references('id')
                ->on('bills')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropForeign(['bill_id']);
        });
    }
};
