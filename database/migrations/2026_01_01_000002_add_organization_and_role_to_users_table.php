<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable: a BrijRaj.Tech platform admin has no organization_id.
            // A client user (owner/team_member) always has one.
            $table->foreignId('organization_id')->nullable()->after('id')
                ->constrained('organizations')->nullOnDelete();

            // Flat role for Phase 1 — no granular permissions yet, per approved scope.
            // Values: owner | team_member | platform_admin
            $table->string('role')->default('owner')->after('organization_id');

            $table->index(['organization_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropColumn('role');
        });
    }
};
