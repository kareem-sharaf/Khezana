<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Phase 1.1: Add performance indexes to items table
     */
    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            // Index on created_at for sorting (if not exists)
            if (!$this->hasIndex('items', 'created_at')) {
                $table->index('created_at', 'idx_items_created_at');
            }

            // Composite index for common query pattern: (is_available, availability_status)
            // This is used in BrowseItemsQuery frequently
            if (!$this->hasCompositeIndex('items', ['is_available', 'availability_status'])) {
                $table->index(['is_available', 'availability_status'], 'idx_items_available_status');
            }

            // Full-text index for search (title, description)
            // Note: Full-text indexes require MyISAM or InnoDB with MySQL 5.6+
            // We'll add it conditionally based on MySQL version
            if ($this->supportsFullTextIndex() && !$this->hasFullTextIndex('items', ['title', 'description'])) {
                $table->fullText(['title', 'description'], 'idx_items_fulltext_search');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            // Drop indexes if they exist
            if ($this->hasIndex('items', 'created_at')) {
                $table->dropIndex('idx_items_created_at');
            }

            if ($this->hasCompositeIndex('items', ['is_available', 'availability_status'])) {
                $table->dropIndex('idx_items_available_status');
            }

            if ($this->hasFullTextIndex('items', ['title', 'description'])) {
                $table->dropIndex('idx_items_fulltext_search');
            }
        });
    }

    /**
     * Check if index exists on column
     */
    private function hasIndex(string $table, string $column): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Column_name = ?", [$column]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if composite index exists
     */
    private function hasCompositeIndex(string $table, array $columns): bool
    {
        try {
            $columnsStr = implode(',', array_map(fn($col) => "`{$col}`", $columns));
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", ['idx_items_available_status']);
            
            if (empty($indexes)) {
                return false;
            }

            // Check if all columns are in the index
            $indexColumns = array_unique(array_column($indexes, 'Column_name'));
            foreach ($columns as $column) {
                if (!in_array($column, $indexColumns)) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if full-text index exists
     */
    private function hasFullTextIndex(string $table, array $columns): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ? AND Index_type = 'FULLTEXT'", ['idx_items_fulltext_search']);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if MySQL version supports full-text indexes on InnoDB
     */
    private function supportsFullTextIndex(): bool
    {
        try {
            $version = DB::selectOne("SELECT VERSION() as version");
            if (!$version) {
                return false;
            }

            $versionStr = $version->version;
            // MySQL 5.6+ supports full-text indexes on InnoDB
            // MariaDB 10.0.5+ supports full-text indexes on InnoDB
            if (preg_match('/(\d+)\.(\d+)\.(\d+)/', $versionStr, $matches)) {
                $major = (int) $matches[1];
                $minor = (int) $matches[2];
                
                // MySQL 5.6+ or MariaDB 10.0.5+
                if ($major > 5 || ($major === 5 && $minor >= 6)) {
                    return true;
                }
                
                // MariaDB
                if (str_contains($versionStr, 'MariaDB')) {
                    if ($major > 10 || ($major === 10 && $minor >= 0)) {
                        return true;
                    }
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
};
