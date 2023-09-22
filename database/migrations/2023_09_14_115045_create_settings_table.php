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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Ρυθμίσεις για το QR Code
            $table->tinyInteger("qr_side")->default(0);
            $table->float("qr_top_margin")->default(0);
            $table->float("qr_side_margin")->default(0);
            $table->float("qr_scale")->default(1);

            // Ρυθμίσεις για την εικόνα που θα εμφανίζεται μαζί με το QR
            $table->tinyInteger("img_side")->default(0);
            $table->float("img_top_margin")->default(0);
            $table->float("img_side_margin")->default(0);
            $table->float("img_scale")->default(1);
            $table->string("img_filename")->default("");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
