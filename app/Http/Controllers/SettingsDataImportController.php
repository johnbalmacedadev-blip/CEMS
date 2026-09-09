<?php

namespace App\Http\Controllers;

use App\Support\DataImport\DataImportManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsDataImportController extends Controller
{
    public function index()
    {
        return view('settings.import-data', [
            'catalog' => DataImportManager::supportedCatalog(),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
        ]);

        @set_time_limit(300);

        try {
            $result = DataImportManager::storeUpload($request->file('file'));

            return response()->json(array_merge(['ok' => true], $result));
        } catch (\Throwable $e) {
            Log::error('Settings data import upload failed', ['message' => $e->getMessage()]);

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
            'workbook_type' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*' => 'string',
        ]);

        try {
            $summary = DataImportManager::analyze(
                $request->input('token'),
                $request->input('workbook_type'),
                $request->input('sheets', [])
            );

            return response()->json([
                'ok' => true,
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            Log::error('Settings data import analyze failed', ['message' => $e->getMessage()]);

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
            'workbook_type' => 'required|string',
            'sheets' => 'required|array|min:1',
            'sheets.*' => 'string',
            'confirm' => 'required|accepted',
        ]);

        try {
            $result = DataImportManager::import(
                $request->input('token'),
                $request->input('workbook_type'),
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
            Log::error('Settings data import confirm failed', ['message' => $e->getMessage()]);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
