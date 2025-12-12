<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'enum pour ajouter super_admin et admin
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'owner', 'admin', 'responsable', 'employe') NOT NULL DEFAULT 'employe'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retour à l'enum original
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner', 'responsable', 'employe') NOT NULL DEFAULT 'employe'");
    }
};
