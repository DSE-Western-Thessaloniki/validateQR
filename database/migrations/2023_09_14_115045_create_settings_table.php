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
            $table->tinyInteger("qr_side");
            $table->float("qr_top_margin");
            $table->float("qr_side_margin");
            $table->float("qr_scale");

            // Ρυθμίσεις για την εικόνα που θα εμφανίζεται μαζί με το QR
            $table->tinyInteger("img_side");
            $table->float("img_top_margin");
            $table->float("img_side_margin");
            $table->float("img_scale");
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
