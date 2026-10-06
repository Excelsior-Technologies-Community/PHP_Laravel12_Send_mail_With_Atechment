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
            $table->string('tracking_token')->nullable()->unique()->after('status');
            $table->timestamp('opened_at')->nullable()->after('tracking_token');
            $table->integer('open_count')->default(0)->after('opened_at');
            $table->timestamp('last_opened_at')->nullable()->after('open_count');
            $table->integer('attachment_downloads')->default(0)->after('last_opened_at');
            $table->text('multiple_attachments')->nullable()->after('attachment_downloads');
            $table->boolean('is_zipped')->default(false)->after('multiple_attachments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_histories', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_token',
                'opened_at',
                'open_count',
                'last_opened_at',
                'attachment_downloads',
                'multiple_attachments',
                'is_zipped',
            ]);
        });
    }
};
