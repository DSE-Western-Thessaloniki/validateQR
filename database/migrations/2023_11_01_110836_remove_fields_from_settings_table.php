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
        Schema::table('settings', function (Blueprint $table) {
            // Αφαίρεση περιττών πεδίων
            $table->dropColumn([
                'qr_side',
                'qr_top_margin',
                'qr_side_margin',
                'qr_scale',
                'img_side',
                'img_top_margin',
                'img_side_margin',
                'img_filename',
            ]);

            // Πρόσθεσε πεδία για τη περιγραφή της θέσης της εικόνας
            $table->float("img_x")->default(0);
            $table->float("img_y")->default(100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->tinyInteger("qr_side")->default(0);
            $table->float("qr_top_margin")->default(0);
            $table->float("qr_side_margin")->default(0);
            $table->float("qr_scale")->default(1);
            $table->tinyInteger("img_side")->default(0);
            $table->float("img_top_margin")->default(0);
            $table->float("img_side_margin")->default(0);
            $table->string("img_filename")->default("");
        });
    }
};
