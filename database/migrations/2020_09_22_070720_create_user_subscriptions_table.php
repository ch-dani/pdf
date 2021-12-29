<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subscription_plan_id');
            $table->unsignedInteger('user_id');
            $table->enum('status', ['active', 'inactive', 'stopped'])->default('active');
            $table->timestamp('next_payment_at')->nullable();
            $table->timestamps();

            $table->foreign('subscription_plan_id')
                ->on('subscription_plans')
                ->references('id')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}
