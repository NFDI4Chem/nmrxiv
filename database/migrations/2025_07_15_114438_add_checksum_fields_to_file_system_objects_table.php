<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChecksumFieldsToFileSystemObjectsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->string('checksum_md5', 32)->nullable()->after('info')->comment('MD5 checksum for file integrity');
            $table->string('checksum_sha256', 64)->nullable()->after('checksum_md5')->comment('SHA-256 checksum for file integrity');
            $table->string('checksum_algorithm', 20)->default('sha256')->after('checksum_sha256')->comment('Primary checksum algorithm used');

            $table->bigInteger('file_size')->nullable()->after('checksum_algorithm')->comment('Original file size in bytes');

            $table->enum('integrity_status', ['pending', 'verified', 'failed', 'skipped'])->default('pending')->after('file_size')->comment('File integrity verification status');
            $table->timestamp('integrity_verified_at')->nullable()->after('integrity_status')->comment('When integrity was last verified');
            $table->text('integrity_error')->nullable()->after('integrity_verified_at')->comment('Error message if integrity verification failed');

            $table->integer('verification_attempts')->default(0)->after('integrity_error')->comment('Number of verification attempts');
            $table->timestamp('last_verification_attempt')->nullable()->after('verification_attempts')->comment('Last verification attempt timestamp');

            $table->index(['integrity_status', 'type'], 'idx_integrity_status_type');
            $table->index(['integrity_verified_at'], 'idx_integrity_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file_system_objects', function (Blueprint $table) {
            $table->dropIndex('idx_integrity_status_type');
            $table->dropIndex('idx_integrity_verified_at');

            $table->dropColumn([
                'checksum_md5',
                'checksum_sha256',
                'checksum_algorithm',
                'file_size',
                'integrity_status',
                'integrity_verified_at',
                'integrity_error',
                'verification_attempts',
                'last_verification_attempt',
            ]);
        });
    }
}
