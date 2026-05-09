<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute reviewer_name et source aux reviews.
     *
     * reviewer_name : nom affiché même sans user_id (clients sans compte)
     * source        : 'site' | 'whatsapp' | 'boutique' | 'importé'
     *                 Pour tracer l'origine des avis saisis par l'admin.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Nom libre pour les avis admin (clients sans compte)
            $table->string('reviewer_name')->nullable()->after('user_id');
            // Source de l'avis
            $table->enum('source', ['site', 'whatsapp', 'boutique', 'importe'])
                  ->default('site')
                  ->after('reviewer_name');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['reviewer_name', 'source']);
        });
    }
};
