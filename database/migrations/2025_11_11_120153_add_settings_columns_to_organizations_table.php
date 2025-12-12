<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('website')->nullable()->after('email');
            $table->text('description')->nullable()->after('website');
            $table->string('subscription_plan')->default('starter')->after('plan');
            $table->date('subscription_end_date')->nullable()->after('subscription_expires_at');
            $table->json('branding')->nullable()->after('description');
            $table->json('settings')->nullable()->after('branding');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['website', 'description', 'subscription_plan', 'subscription_end_date', 'branding', 'settings']);
        });
    }
};
