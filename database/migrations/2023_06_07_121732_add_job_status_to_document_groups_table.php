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
        Schema::table('document_groups', function (Blueprint $table) {
            $table->integer('job_status')->default(0)->comment('0 - Not Started, 1 - Started, 2 - Success, 3 - Failed, 4 - Canceled');
            $table->string('job_status_text')->default('Not Started');
            $table->date('job_start_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_groups', function (Blueprint $table) {
            $table->dropColumn(['job_status', 'job_status_text', 'job_start_date']);
        });
    }
};
