<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('avatars')) {
            Schema::create('avatars', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->string('name');
                $table->boolean('is_public')->default(false);
                $table->string('profile_image')->nullable();
                $table->json('categories')->nullable();
                $table->json('first_message')->nullable();
                $table->text('profile_intro')->nullable();
                $table->json('hashtags')->nullable();
                $table->text('profile_details')->nullable();
                $table->json('fine_tuning_data')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('avatars', function (Blueprint $table) {
                if (!Schema::hasColumn('avatars', 'uuid')) {
                    $table->uuid('uuid')->unique()->after('id');
                }
                $table->json('categories')->nullable()->change();
                $table->json('first_message')->nullable()->change();
                $table->json('hashtags')->nullable()->change();
                $table->json('fine_tuning_data')->nullable()->change();
            });
        }

        if (!Schema::hasTable('avatar_chats')) {
            Schema::create('avatar_chats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('avatar_id')->constrained('avatars')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('message');
                $table->boolean('is_from_avatar');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('avatar_chats');
        if (Schema::hasTable('avatars')) {
            Schema::table('avatars', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};