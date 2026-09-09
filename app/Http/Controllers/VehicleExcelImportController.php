<?php

namespace App\Http\Controllers;

use App\Support\VehicleUnitsExcelImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleExcelImportController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
        ]);

        @set_time_limit(300);

        try {
            $result = VehicleUnitsExcelImporter::storeUpload($request->file('file'));

            return response()->json([
                'ok' => true,
                'token' => $result['token'],
                'original_name' => $result['original_name'],
                'sheets' => $result['sheets'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Vehicle excel upload failed', ['message' => $e->getMessage()]);

            return response()->json([
                'ok' => false,
                'message' => 'Could not read Excel file: '.$e->getMessage(),
            ], 422);
        }
    }

    public function analyze(Request $request)
    {
        @set_time_limit(300);

        $request->validate([
            'token' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*' => 'string',
        ]);

        try {
            $summary = VehicleUnitsExcelImporter::analyze(
                $request->input('token'),
                $request->input('sheets', [])
            );

            return response()->json([
                'ok' => true,
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('Vehicle excel analyze failed', ['message' => $e->getMessage()]);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function confirm(Request $request)
    {
        @set_time_limit(600);

        $request->validate([
            'token' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*' => 'string',
            'confirm' => 'required|accepted',
        ]);

        try {
            $result = VehicleUnitsExcelImporter::import(
                $request->input('token'),
                $request->input('sheets', [])
            );

            return response()->json([
                'ok' => (bool) ($result['ok'] ?? false),
                'message' => ($result['ok'] ?? false)
                    ? 'Import completed successfully.'
                    : 'Import finished with one or more errors.',
                'results' => $result['results'] ?? [],
            ], ($result['ok'] ?? false) ? 200 : 500);
        } catch (\Throwable $e) {
            Log::error('Vehicle excel import failed', ['message' => $e->getMessage()]);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
