<?php

use App\Models\Document;
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
        Schema::create('document_extra_states', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Document::class);
            $table->unsignedTinyInteger('extra_state')
                ->comment('1 - Ακυρωμένο, 2 - Αντικαταστάθηκε');
            $table->string('extra_state_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_extra_states');
    }
};
