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
     * Performance fix #18: Add missing indexes for better query performance
     */
    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            // Index on category_id (used in filters and joins)
            if (!$this->hasIndex('items', 'category_id')) {
                $table->index('category_id', 'idx_items_category_id');
            }

            // Index on operation_type (used in filters)
            if (!$this->hasIndex('items', 'operation_type')) {
                $table->index('operation_type', 'idx_items_operation_type');
            }

            // Index on condition (used in filters)
            if (!$this->hasIndex('items', 'condition')) {
                $table->index('condition', 'idx_items_condition');
            }

            // Index on price (used in filters and sorting)
            if (!$this->hasIndex('items', 'price')) {
                $table->index('price', 'idx_items_price');
            }

            // Index on user_id (used in user's own items query)
            if (!$this->hasIndex('items', 'user_id')) {
                $table->index('user_id', 'idx_items_user_id');
            }

            // Composite index for deleted_at and archived_at (used together frequently)
            if (!$this->hasCompositeIndex('items', ['deleted_at', 'archived_at'])) {
                $table->index(['deleted_at', 'archived_at'], 'idx_items_deleted_archived');
            }
        });

        // Add indexes to approvals table
        if (Schema::hasTable('approvals')) {
            Schema::table('approvals', function (Blueprint $table) {
                // Composite index for polymorphic relationship queries
                if (!$this->hasCompositeIndex('approvals', ['approvable_type', 'approvable_id', 'status'])) {
                    $table->index(['approvable_type', 'approvable_id', 'status'], 'idx_approvals_polymorphic_status');
                }

                // Index on status alone (used in filters)
                if (!$this->hasIndex('approvals', 'status')) {
                    $table->index('status', 'idx_approvals_status');
                }
            });
        }

        // Add index to categories table
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                // Index on is_active (used in filters)
                if (!$this->hasIndex('categories', 'is_active')) {
                    $table->index('is_active', 'idx_categories_is_active');
                }
            });
        }
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
            if ($this->hasIndex('items', 'category_id')) {
                $table->dropIndex('idx_items_category_id');
            }
            if ($this->hasIndex('items', 'operation_type')) {
                $table->dropIndex('idx_items_operation_type');
            }
            if ($this->hasIndex('items', 'condition')) {
                $table->dropIndex('idx_items_condition');
            }
            if ($this->hasIndex('items', 'price')) {
                $table->dropIndex('idx_items_price');
            }
            if ($this->hasIndex('items', 'user_id')) {
                $table->dropIndex('idx_items_user_id');
            }
            if ($this->hasCompositeIndex('items', ['deleted_at', 'archived_at'])) {
                $table->dropIndex('idx_items_deleted_archived');
            }
        });

        if (Schema::hasTable('approvals')) {
            Schema::table('approvals', function (Blueprint $table) {
                if ($this->hasCompositeIndex('approvals', ['approvable_type', 'approvable_id', 'status'])) {
                    $table->dropIndex('idx_approvals_polymorphic_status');
                }
                if ($this->hasIndex('approvals', 'status')) {
                    $table->dropIndex('idx_approvals_status');
                }
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if ($this->hasIndex('categories', 'is_active')) {
                    $table->dropIndex('idx_categories_is_active');
                }
            });
        }
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
            // Try to find index by checking all columns
            $indexes = DB::select("SHOW INDEX FROM `{$table}`");
            $indexGroups = [];
            
            foreach ($indexes as $index) {
                $keyName = $index->Key_name;
                if (!isset($indexGroups[$keyName])) {
                    $indexGroups[$keyName] = [];
                }
                $indexGroups[$keyName][] = $index->Column_name;
            }
            
            foreach ($indexGroups as $keyName => $indexColumns) {
                if (count($indexColumns) === count($columns)) {
                    $allMatch = true;
                    foreach ($columns as $col) {
                        if (!in_array($col, $indexColumns)) {
                            $allMatch = false;
                            break;
                        }
                    }
                    if ($allMatch) {
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
