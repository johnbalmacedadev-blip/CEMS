<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gas_expenses', function (Blueprint $table) {
            // Allow historical plates that are not in vehicles inventory
            try {
                $table->dropForeign(['plate_number']);
            } catch (\Throwable $e) {
                // Foreign key may already be absent
            }
        });

        Schema::table('gas_expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('gas_expenses', 'po_number')) {
                $table->string('po_number', 100)->nullable()->after('date');
            }
            if (! Schema::hasColumn('gas_expenses', 'photo_po_slip')) {
                $table->boolean('photo_po_slip')->default(false)->after('has_photo_video_in_groupchat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gas_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('gas_expenses', 'photo_po_slip')) {
                $table->dropColumn('photo_po_slip');
            }
            if (Schema::hasColumn('gas_expenses', 'po_number')) {
                $table->dropColumn('po_number');
            }
        });
    }
};
