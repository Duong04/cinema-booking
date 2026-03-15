<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('role_id')->constrained('roles');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->string('email_verify_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('token_expired_at')->nullable();
            $table->string('avatar')->default('https://diskimageshq.nyc3.digitaloceanspaces.com/laravel-app/avatar/mwWv7ME6Lho1dBcmI0vS4hLwyj1xdkXxPspQU9Av.png')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->boolean('is_active')->default(false);
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('provider'); 
            $table->string('provider_id');

            $table->timestamps();

            $table->unique(['provider','provider_id']);
        });

        Schema::create('ip_details', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('loc')->nullable();
            $table->string('org')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ip_id');
            $table->timestamp('login_at')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreign('ip_id')->references('id')->on('ip_details')->onDelete('cascade');
        });


        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS
        |--------------------------------------------------------------------------
        */
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('key')->unique();
            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | ACTIONS
        |--------------------------------------------------------------------------
        */
        Schema::create('actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('key')->unique();
            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | PERMISSION ACTIONS
        |--------------------------------------------------------------------------
        */
        Schema::create('permission_actions', function (Blueprint $table) {
            $table->uuid('permission_id');
            $table->uuid('action_id');

            $table->primary(['permission_id', 'action_id']);

            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('action_id')->references('id')->on('actions')->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | ROLE PERMISSIONS
        |--------------------------------------------------------------------------
        */
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('role_id');
            $table->uuid('permission_id');
            $table->uuid('action_id');

            $table->primary(['role_id', 'permission_id', 'action_id']);

            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
            $table->foreign('action_id')->references('id')->on('actions')->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | LOCATION
        |--------------------------------------------------------------------------
        */

        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cinema_chains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('cinemas', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('city_id')->constrained('cities');
            $table->foreignUuid('cinema_chain_id')->constrained('cinema_chains');

            $table->string('name');
            $table->string('address');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rooms', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->foreignUuid('cinema_id')->constrained('cinemas');

            $table->string('name');
            $table->string('type');

            $table->timestamps();
            $table->softDeletes();
        });


        /*
        |--------------------------------------------------------------------------
        | SEATS
        |--------------------------------------------------------------------------
        */

        Schema::create('seat_types', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('base_multiplier', 5, 2);
            $table->timestamps();
        });

        Schema::create('seats', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('room_id')->constrained('rooms');
            $table->foreignUuid('seat_type_id')->constrained('seat_types');

            $table->string('row_label');
            $table->integer('seat_number');

            $table->timestamps();

            $table->unique(['room_id', 'row_label', 'seat_number']);
        });


        /*
        |--------------------------------------------------------------------------
        | MOVIES
        |--------------------------------------------------------------------------
        */

        Schema::create('movies', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('title');
            $table->string('slug')->unique();

            $table->integer('duration_minutes');

            $table->string('poster_url')->nullable();
            $table->string('trailer_url')->nullable();

            $table->text('description')->nullable();
            $table->mediumText('content')->nullable();

            $table->date('release_date')->nullable();

            $table->string('rating')->nullable();
            $table->string('language')->nullable();

            $table->string('status');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('genres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
        });

        Schema::create('movie_genres', function (Blueprint $table) {

            $table->uuid('movie_id');
            $table->uuid('genre_id');

            $table->unique(['movie_id', 'genre_id']);

            $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
            $table->foreign('genre_id')->references('id')->on('genres')->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | SHOWTIMES
        |--------------------------------------------------------------------------
        */

        Schema::create('showtimes', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('movie_id')->constrained('movies');
            $table->foreignUuid('room_id')->constrained('rooms');

            $table->date('show_date');

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->decimal('base_price', 10, 2);

            $table->string('status');

            $table->text('cancelled_reason')->nullable();
            $table->foreignUuid('cancelled_by')->nullable()->constrained('users');
            $table->dateTime('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['room_id', 'show_date', 'start_time']);
        });


        Schema::create('showtime_prices', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('showtime_id')->constrained('showtimes');
            $table->foreignUuid('seat_type_id')->constrained('seat_types');

            $table->decimal('price', 10, 2);

            $table->unique(['showtime_id', 'seat_type_id']);
        });


        /*
        |--------------------------------------------------------------------------
        | BOOKINGS
        |--------------------------------------------------------------------------
        */

        Schema::create('bookings', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('showtime_id')->constrained('showtimes');

            $table->string('booking_code')->unique();

            $table->decimal('total_amount', 10, 2);

            $table->string('status');

            $table->text('cancellation_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();

            $table->dateTime('expired_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();

            $table->timestamps();
        });


        Schema::create('booking_items', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('booking_id')->constrained('bookings');
            $table->foreignUuid('showtime_id')->constrained('showtimes');
            $table->foreignUuid('seat_id')->constrained('seats');

            $table->decimal('price', 10, 2);

            $table->string('movie_title');
            $table->string('room_name');
            $table->string('seat_label');

            $table->timestamp('created_at')->nullable();

            $table->unique(['booking_id', 'seat_id']);
            $table->unique(['seat_id', 'showtime_id']);
        });

        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('booking_id');

            $table->string('old_status')->nullable();
            $table->string('new_status');

            $table->timestamp('changed_at');

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->cascadeOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | SEAT HOLDS
        |--------------------------------------------------------------------------
        */

        Schema::create('seat_holds', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('showtime_id')->constrained('showtimes');
            $table->foreignUuid('seat_id')->constrained('seats');

            $table->dateTime('expired_at');

            $table->timestamp('created_at')->nullable();

            $table->unique(['seat_id', 'showtime_id']);
        });


        /*
        |--------------------------------------------------------------------------
        | PAYMENTS
        |--------------------------------------------------------------------------
        */

        Schema::create('payments', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('booking_id')->constrained('bookings');

            $table->string('provider');
            $table->string('transaction_code');

            $table->decimal('amount', 10, 2);

            $table->string('status');

            $table->dateTime('paid_at')->nullable();

            $table->string('idempotency_key')->nullable();

            $table->decimal('refunded_amount', 10, 2)->nullable();
            $table->string('refund_status')->nullable();

            $table->timestamp('created_at')->nullable();

            $table->unique(['provider', 'transaction_code']);
        });


        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('booking_id');

            $table->string('provider');

            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();

            $table->string('status');

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('booking_id')
                ->references('id')
                ->on('bookings')
                ->cascadeOnDelete();
        });
        /*
        |--------------------------------------------------------------------------
        | COMBOS
        |--------------------------------------------------------------------------
        */

        Schema::create('combos', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);

            $table->string('status');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('booking_combos', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('booking_id')->constrained('bookings');
            $table->foreignUuid('combo_id')->constrained('combos');

            $table->string('combo_name');

            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->timestamp('created_at')->nullable();

            $table->unique(['booking_id', 'combo_id']);
        });


        /*
        |--------------------------------------------------------------------------
        | PROMOTIONS
        |--------------------------------------------------------------------------
        */

        Schema::create('promotions', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->string('code')->unique();

            $table->string('discount_type');
            $table->decimal('discount_value', 10, 2);

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->integer('usage_limit')->nullable();
            $table->integer('per_user_limit')->nullable();

            $table->timestamps();
        });


        Schema::create('booking_promotions', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('booking_id')->constrained('bookings');
            $table->foreignUuid('promotion_id')->constrained('promotions');

            $table->decimal('discount_amount', 10, 2);
        });


        Schema::create('promotion_usages', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('promotion_id')->constrained('promotions');
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('booking_id')->constrained('bookings');

            $table->timestamp('used_at')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | MEMBERSHIPS
        |--------------------------------------------------------------------------
        */

        Schema::create('memberships', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users');

            $table->string('tier');
            $table->integer('points');

            $table->timestamp('updated_at')->nullable();
        });


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOGS
        |--------------------------------------------------------------------------
        */

        Schema::create('activity_logs', function (Blueprint $table) {

            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users');

            $table->string('action');
            $table->string('entity_type');

            $table->uuid('entity_id');

            $table->text('metadata')->nullable();

            $table->timestamp('created_at')->nullable();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('booking_promotions');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('booking_combos');
        Schema::dropIfExists('combos');
        Schema::dropIfExists('payment_attempts ');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('seat_holds');
        Schema::dropIfExists('booking_status_logs');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('showtime_prices');
        Schema::dropIfExists('showtimes');
        Schema::dropIfExists('movie_genres');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('seats');
        Schema::dropIfExists('seat_types');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('cinemas');
        Schema::dropIfExists('cinema_chains');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permission_actions');
        Schema::dropIfExists('actions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
    }
};
