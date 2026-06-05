<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include CONSENT_FORM
        DB::statement("ALTER TABLE vehicle_documents MODIFY COLUMN document_type ENUM(
            'OR',
            'CR',
            'AR',
            'IDS',
            'PROMISSORY',
            'CHATTEL',
            'REGISTRY_OF_DEEDS',
            'SEC_CERT',
            'DEED_OF_SALE',
            'VOLUNTARY_SURRENDER',
            'SHERRIF_LETTER',
            'DEED_OF_SALE_BANK',
            'CONSENT_FORM'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum (without CONSENT_FORM)
        DB::statement("ALTER TABLE vehicle_documents MODIFY COLUMN document_type ENUM(
            'OR',
            'CR',
            'AR',
            'IDS',
            'PROMISSORY',
            'CHATTEL',
            'REGISTRY_OF_DEEDS',
            'SEC_CERT',
            'DEED_OF_SALE',
            'VOLUNTARY_SURRENDER',
            'SHERRIF_LETTER',
            'DEED_OF_SALE_BANK'
        )");
    }
};
