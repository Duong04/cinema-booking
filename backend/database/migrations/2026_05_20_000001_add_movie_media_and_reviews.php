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
        Schema::table('movies', function (Blueprint $table) {
            $table->string('banner_url')->nullable();
            $table->decimal('rating_score', 3, 1)->nullable();
            $table->unsignedInteger('rating_count')->default(0);
        });

        Schema::create('movie_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('movie_id')->constrained('movies')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('rating_score', 3, 1);
            $table->text('comment')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['movie_id', 'user_id']);
            $table->index(['movie_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_reviews');

        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn([
                'banner_url',
                'rating_score',
                'rating_count',
            ]);
        });
    }
};
