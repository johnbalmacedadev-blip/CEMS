<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeployController extends Controller
{
    /**
     * Post-deploy tasks for hosts without SSH (e.g. cPanel Git pull).
     * Requires DEPLOY_TOKEN in .env. Call once after updating code, then keep the token secret.
     */
    public function run(Request $request)
    {
        $configured = (string) config('app.deploy_token', env('DEPLOY_TOKEN', ''));
        $provided = (string) $request->query('token', $request->input('token', ''));

        if ($configured === '' || ! hash_equals($configured, $provided)) {
            abort(403, 'Invalid deploy token.');
        }

        $steps = [];
        $failed = false;

        $commands = [
            'migrate' => ['--force' => true],
            'storage:link' => [],
            'config:cache' => [],
            'route:cache' => [],
            'view:cache' => [],
        ];

        foreach ($commands as $command => $params) {
            try {
                $exit = Artisan::call($command, $params);
                $steps[] = [
                    'command' => $command,
                    'exit' => $exit,
                    'output' => trim(Artisan::output()),
                ];
                if ($exit !== 0) {
                    $failed = true;
                }
            } catch (\Throwable $e) {
                $failed = true;
                $steps[] = [
                    'command' => $command,
                    'exit' => 1,
                    'output' => $e->getMessage(),
                ];
                Log::error('Deploy step failed', [
                    'command' => $command,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => ! $failed,
            'message' => $failed
                ? 'Deploy finished with errors. Check steps/output.'
                : 'Deploy tasks completed.',
            'steps' => $steps,
        ], $failed ? 500 : 200);
    }
}
