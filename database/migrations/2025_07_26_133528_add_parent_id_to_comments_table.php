<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table): void {

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->onDelete('cascade')
                ->after('idea_id');

            // Performance için index
            $table->index(['idea_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['idea_id', 'parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
