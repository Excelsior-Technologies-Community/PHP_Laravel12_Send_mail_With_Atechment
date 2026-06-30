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
        Schema::table('email_histories', function (Blueprint $table) {

            $table->enum('type', [
                'instant',
                'scheduled'
            ])
            ->default('instant')
            ->after('status');


            $table->timestamp('scheduled_at')
            ->nullable()
            ->after('type');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_histories', function (Blueprint $table) {

            $table->dropColumn([
                'type',
                'scheduled_at'
            ]);

        });
    }
};