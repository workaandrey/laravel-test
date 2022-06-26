<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /**
    # Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different locations

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('movie_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id')->index();
            $table->unsignedBigInteger('price_in_cents');
            $table->enum('seat_type', ['vip', 'couple', 'super_vip', 'base'])->default('base');
            $table->timestamps();

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('restrict');
        });

        Schema::create('cinema_halls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('movie_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id')->index();
            $table->unsignedInteger('cinema_hall_id')->index();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->unsignedInteger('seats_available'); //de normalization
            $table->timestamps();

            $table->foreign('movie_id')
                ->references('id')
                ->on('movies')
                ->onDelete('restrict');

            $table->foreign('cinema_hall_id')
                ->references('id')
                ->on('cinema_halls')
                ->onDelete('restrict');
        });

        Schema::create('cinema_hall_seats', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('type', ['vip', 'couple', 'super_vip', 'base'])->default('base');
            $table->unsignedInteger('cinema_hall_id')->index();

            $table->foreign('cinema_hall_id')
                ->references('id')
                ->on('cinema_halls')
                ->onDelete('restrict');
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->unsignedInteger('movie_session_id');
            $table->unsignedInteger('movie_price_id');
            $table->unsignedInteger('cinema_hall_seat_id');

            $table->foreign('movie_session_id')
                ->references('id')
                ->on('movie_sessions')
                ->onDelete('restrict');

            $table->foreign('movie_price_id')
                ->references('id')
                ->on('movie_prices')
                ->onDelete('restrict');

            $table->foreign('cinema_hall_seat_id')
                ->references('id')
                ->on('cinema_hall_seats')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
