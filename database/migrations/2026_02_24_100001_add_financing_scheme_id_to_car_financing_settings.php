<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaultSchemeId = DB::table('financing_schemes')->orderBy('sort_order')->value('id') ?? 1;

        if (!Schema::hasColumn('car_financing_settings', 'financing_scheme_id')) {
            Schema::table('car_financing_settings', function (Blueprint $table) use ($defaultSchemeId) {
                $table->unsignedBigInteger('financing_scheme_id')->after('id')->default($defaultSchemeId);
                $table->foreign('financing_scheme_id')->references('id')->on('financing_schemes')->cascadeOnDelete();
            });
        }

        DB::table('car_financing_settings')->whereNull('financing_scheme_id')->update(['financing_scheme_id' => $defaultSchemeId]);

        if (Schema::hasColumn('car_financing_settings', 'financing_scheme_id')) {
            try {
                Schema::table('car_financing_settings', function (Blueprint $table) {
                    $table->dropUnique(['year_model_range']);
                });
            } catch (QueryException $e) {
                if (strpos($e->getMessage(), '1091') === false) throw $e;
            }
            try {
                Schema::table('car_financing_settings', function (Blueprint $table) {
                    $table->unique(['financing_scheme_id', 'year_model_range'], 'car_fin_scheme_year_unique');
                });
            } catch (QueryException $e) {
                if (strpos($e->getMessage(), '1061') === false) throw $e; // 1061 = duplicate key name
            }
        }
    }

    public function down(): void
    {
        Schema::table('car_financing_settings', function (Blueprint $table) {
            $table->dropUnique('car_fin_scheme_year_unique');
            $table->unique('year_model_range');
        });

        Schema::table('car_financing_settings', function (Blueprint $table) {
            $table->dropForeign(['financing_scheme_id']);
        });
    }
};
