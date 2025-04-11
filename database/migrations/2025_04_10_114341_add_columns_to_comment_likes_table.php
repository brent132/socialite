<?php

use Illuminate\Database\Migrations\Migration;

// This migration is redundant as the columns are added in 2025_04_12_000000_add_missing_columns_to_comment_likes_table.php
// Keeping this file with empty methods to maintain migration history

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No operations needed - columns added in another migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No operations needed - columns handled in another migration
    }
};
