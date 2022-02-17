<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('acc_id')->unsigned()->nullable();
            $table->string('action');
            $table->decimal('amount');
            $table->decimal('start_balance');
            $table->decimal('end_balance');
            $table->string('status');
            $table->string('method');
            $table->string('reference');
            $table->timestamps();
            $table->foreign('acc_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
