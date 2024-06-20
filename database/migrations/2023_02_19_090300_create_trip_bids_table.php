<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_bids', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('request_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('driver_id');
            $table->double('default_price', 10, 2)->default(0);
            $table->double('bid_price', 10, 2)->default(0);
            $table->boolean('is_accepted')->default(0);
            $table->timestamps();

            $table->foreign('request_id')
                    ->references('id')
                    ->on('requests')
                    ->onDelete('cascade');
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('driver_id')
                    ->references('id')
                    ->on('drivers')
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
        Schema::dropIfExists('trip_bids');
    }
}
