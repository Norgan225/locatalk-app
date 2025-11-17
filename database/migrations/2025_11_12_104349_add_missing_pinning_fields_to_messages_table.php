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
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('is_delivered');
            }
            if (!Schema::hasColumn('messages', 'pinned_at')) {
                $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            }
            if (!Schema::hasColumn('messages', 'pinned_by')) {
                $table->foreignId('pinned_by')->nullable()->constrained('users')->onDelete('set null')->after('pinned_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'pinned_by')) {
                $table->dropForeign(['pinned_by']);
                $table->dropColumn('pinned_by');
            }
            if (Schema::hasColumn('messages', 'pinned_at')) {
                $table->dropColumn('pinned_at');
            }
            if (Schema::hasColumn('messages', 'is_pinned')) {
                $table->dropColumn('is_pinned');
            }
        });
    }
};
