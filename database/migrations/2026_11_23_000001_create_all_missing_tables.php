<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add missing columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id'))     $table->unsignedBigInteger('role_id')->default(3)->after('id');
            if (!Schema::hasColumn('users', 'avatar_url'))  $table->string('avatar_url')->nullable()->after('email');
            if (!Schema::hasColumn('users', 'bio'))         $table->text('bio')->nullable()->after('avatar_url');
        });

        // 2. categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->nullable();
            });
        }

        // 3. provinces
        if (!Schema::hasTable('provinces')) {
            Schema::create('provinces', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
            });
        }

        // 4. regions
        if (!Schema::hasTable('regions')) {
            Schema::create('regions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('province_id')->constrained('provinces')->cascadeOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
            });
        }

        // 5. places
        if (!Schema::hasTable('places')) {
            Schema::create('places', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('website')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->decimal('price_start', 15, 2)->nullable();
                $table->decimal('price_end', 15, 2)->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamps();
            });
        }

        // 6. tags
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
            });
        }

        // 7. reviews
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->tinyInteger('rating');
                $table->text('comment')->nullable();
            });
        }

        // 8. operating_hours
        if (!Schema::hasTable('operating_hours')) {
            Schema::create('operating_hours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->tinyInteger('day_of_week'); // 0=Sun … 6=Sat
                $table->time('open_time')->nullable();
                $table->time('close_time')->nullable();
                $table->boolean('is_closed')->default(false);
            });
        }

        // 9. events
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->decimal('price', 15, 2)->nullable();
                $table->string('image_url')->nullable();
            });
        }

        // 10. rooms
        if (!Schema::hasTable('rooms')) {
            Schema::create('rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price_per_night', 15, 2);
                $table->unsignedInteger('max_guests')->default(1);
                $table->string('image_url')->nullable();
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        // 11. bookings
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->date('check_in');
                $table->date('check_out');
                $table->unsignedInteger('guests')->default(1);
                $table->decimal('total_price', 15, 2);
                $table->text('special_requests')->nullable();
                $table->string('status')->default('pending');
                $table->string('booking_code')->unique();
                $table->timestamps();
            });
        }

        // 12. train_stations
        if (!Schema::hasTable('train_stations')) {
            Schema::create('train_stations', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('city');
                $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            });
        }

        // 13. train_bookings
        if (!Schema::hasTable('train_bookings')) {
            Schema::create('train_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('origin_station_id')->constrained('train_stations');
                $table->foreignId('destination_station_id')->constrained('train_stations');
                $table->dateTime('departure_at');
                $table->dateTime('arrival_at')->nullable();
                $table->string('train_name')->nullable();
                $table->string('seat_class')->nullable();
                $table->unsignedInteger('passengers')->default(1);
                $table->decimal('total_price', 15, 2)->nullable();
                $table->string('status')->default('pending');
                $table->string('booking_code')->unique()->nullable();
                $table->timestamps();
            });
        }

        // 14. flight_bookings
        if (!Schema::hasTable('flight_bookings')) {
            Schema::create('flight_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('origin');
                $table->string('destination');
                $table->dateTime('departure_at');
                $table->dateTime('arrival_at')->nullable();
                $table->string('airline')->nullable();
                $table->string('flight_number')->nullable();
                $table->string('seat_class')->nullable();
                $table->unsignedInteger('passengers')->default(1);
                $table->decimal('total_price', 15, 2)->nullable();
                $table->string('status')->default('pending');
                $table->string('booking_code')->unique()->nullable();
                $table->timestamps();
            });
        }

        // 15. itinerary_posts
        if (!Schema::hasTable('itinerary_posts')) {
            Schema::create('itinerary_posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('cover_image')->nullable();
                $table->text('summary')->nullable();
                $table->longText('body')->nullable();
                $table->foreignId('region_id')->nullable()->constrained('regions')->nullOnDelete();
                $table->unsignedInteger('duration_days')->default(1);
                $table->decimal('estimated_budget', 15, 2)->nullable();
                $table->decimal('actual_budget', 15, 2)->nullable();
                $table->string('travel_style')->nullable();
                $table->string('status')->default('draft');
                $table->unsignedInteger('views')->default(0);
                $table->boolean('is_verified')->default(false);
                $table->tinyInteger('trip_rating')->nullable();
                $table->text('trip_tips')->nullable();
                $table->timestamps();
            });
        }

        // 16. itinerary_days
        if (!Schema::hasTable('itinerary_days')) {
            Schema::create('itinerary_days', function (Blueprint $table) {
                $table->id();
                $table->foreignId('itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->unsignedInteger('day_number');
                $table->string('title')->nullable();
                $table->text('notes')->nullable();
            });
        }

        // 17. itinerary_stops
        if (!Schema::hasTable('itinerary_stops')) {
            Schema::create('itinerary_stops', function (Blueprint $table) {
                $table->id();
                $table->foreignId('day_id')->constrained('itinerary_days')->cascadeOnDelete();
                $table->string('stop_type')->default('place');
                $table->foreignId('place_id')->nullable()->constrained('places')->nullOnDelete();
                $table->foreignId('flight_booking_id')->nullable()->constrained('flight_bookings')->nullOnDelete();
                $table->foreignId('hotel_booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->foreignId('train_booking_id')->nullable()->constrained('train_bookings')->nullOnDelete();
                $table->string('custom_place_name')->nullable();
                $table->string('custom_title')->nullable();
                $table->text('custom_description')->nullable();
                $table->unsignedInteger('visit_order')->default(0);
                $table->string('visit_time')->nullable();
                $table->unsignedInteger('duration_minutes')->nullable();
                $table->text('tip')->nullable();
                $table->decimal('cost', 15, 2)->nullable();
                $table->time('time_start')->nullable();
                $table->time('time_end')->nullable();
                $table->string('transport_mode')->nullable();
                $table->decimal('transport_cost', 15, 2)->nullable();
                $table->unsignedInteger('order')->default(0);
            });
        }

        // 18. itinerary_tags (pivot)
        if (!Schema::hasTable('itinerary_tags')) {
            Schema::create('itinerary_tags', function (Blueprint $table) {
                $table->foreignId('itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
                $table->primary(['itinerary_id', 'tag_id']);
            });
        }

        // 19. itinerary_likes (pivot)
        if (!Schema::hasTable('itinerary_likes')) {
            Schema::create('itinerary_likes', function (Blueprint $table) {
                $table->foreignId('itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('created_at')->nullable();
                $table->primary(['itinerary_id', 'user_id']);
            });
        }

        // 20. itinerary_comments
        if (!Schema::hasTable('itinerary_comments')) {
            Schema::create('itinerary_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->text('body');
                $table->tinyInteger('rating')->nullable();
                $table->timestamps();
            });
        }

        // 21. saved_itineraries (pivot)
        if (!Schema::hasTable('saved_itineraries')) {
            Schema::create('saved_itineraries', function (Blueprint $table) {
                $table->foreignId('itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('saved_at')->nullable();
                $table->primary(['itinerary_id', 'user_id']);
            });
        }

        // 22. itinerary_copies
        if (!Schema::hasTable('itinerary_copies')) {
            Schema::create('itinerary_copies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('original_itinerary_id')->constrained('itinerary_posts')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('copied_at')->nullable();
            });
        }

        // 23. follows (pivot)
        if (!Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table) {
                $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('following_id')->constrained('users')->cascadeOnDelete();
                $table->primary(['follower_id', 'following_id']);
            });
        }

        // 24. place_facilities (pivot) — for Place::facilities()
        if (!Schema::hasTable('place_facilities')) {
            Schema::create('place_facilities', function (Blueprint $table) {
                $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
                $table->unsignedBigInteger('facility_id');
                $table->primary(['place_id', 'facility_id']);
            });
        }
    }

    public function down(): void
    {
        // Drop in reverse dependency order
        Schema::dropIfExists('place_facilities');
        Schema::dropIfExists('follows');
        Schema::dropIfExists('itinerary_copies');
        Schema::dropIfExists('saved_itineraries');
        Schema::dropIfExists('itinerary_comments');
        Schema::dropIfExists('itinerary_likes');
        Schema::dropIfExists('itinerary_tags');
        Schema::dropIfExists('itinerary_stops');
        Schema::dropIfExists('itinerary_days');
        Schema::dropIfExists('itinerary_posts');
        Schema::dropIfExists('flight_bookings');
        Schema::dropIfExists('train_bookings');
        Schema::dropIfExists('train_stations');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('events');
        Schema::dropIfExists('operating_hours');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('places');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('categories');
    }
};
