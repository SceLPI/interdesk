<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_id')->unsigned();
            $table->string('small_title', 60);
            $table->text('title', 400)->nullable();
            $table->bigInteger('prior_id')->unsigned();
            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->date('limit_date')->nullable();
            $table->string('estimated_time')->nullable();
            $table->longText('content');
            $table->bigInteger('status_id')->unsigned();
            $table->bigInteger('agent_user_id')->nullable()->unsigned();
            $table->smallInteger('rating')->nullable()->unsigned();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id', 'ticket_fk_user')->references('id')->on('users');
            $table->foreign('prior_id', 'ticket_fk_prior')->references('id')->on('priors');
            $table->foreign('department_id', 'ticket_fk_department')->references('id')->on('departments');
            $table->foreign('status_id', 'ticket_fk_status')->references('id')->on('status');
            $table->foreign('agent_user_id', 'ticket_fk_user__agent')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
