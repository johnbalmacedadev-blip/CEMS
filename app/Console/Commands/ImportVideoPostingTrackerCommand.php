<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VideoPostingRecord;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportVideoPostingTrackerCommand extends Command
{
    protected $signature = 'import:video-posting-tracker
                            {--file= : Path to JSON export}
                            {--fresh : Truncate video_posting_records before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import Vlog and Posting Tracker Excel JSON (excludes Sheet1 helper tabs)';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/video_posting_tracker_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $this->info('Rows to process: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');
        }

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $key = $this->normalizePlate($vehicle->plate_number);
            if ($key !== '') {
                $vehicleByPlate[$key] = $vehicle->id;
            }
        }

        if ($this->option('fresh') && ! $this->option('dry-run')) {
            VideoPostingRecord::query()->delete();
            $this->warn('Cleared existing video_posting_records rows.');
        }

        $created = 0;
        $updated = 0;
        $linked = 0;
        $unlinked = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                $payload = $this->mapRow($row, $vehicleByPlate, $linked, $unlinked);

                if ($this->option('dry-run')) {
                    $created++;
                    $bar->advance();
                    continue;
                }

                // JSON is pre-deduped; always insert after --fresh to avoid overwrites.
                if ($this->option('fresh')) {
                    VideoPostingRecord::create($payload);
                    $created++;
                } else {
                    $existing = $this->findExisting($payload);
                    if ($existing) {
                        // Only fill empty fields; never wipe good data with empties.
                        $merge = $payload;
                        foreach ($merge as $key => $value) {
                            if ($value === null || $value === '') {
                                unset($merge[$key]);
                            }
                        }
                        $existing->update($merge);
                        $updated++;
                    } else {
                        VideoPostingRecord::create($payload);
                        $created++;
                    }
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error($e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Created: {$created}");
        $this->info("Updated: {$updated}");
        $this->info("Linked to vehicle profile: {$linked}");
        $this->info("No vehicle match: {$unlinked}");
        if ($errors) {
            $this->warn("Errors/skipped: {$errors}");
        }

        return self::SUCCESS;
    }

    private function mapRow(array $row, array $vehicleByPlate, int &$linked, int &$unlinked): array
    {
        $plateRaw = $this->clean($row['plate_number'] ?? null);
        $posted = $this->parseDate($row['date_posted_social'] ?? null);
        $uploaded = $this->parseDate($row['date_uploaded_gdrive'] ?? null);
        $featured = $this->clean($row['featured_car_or_client'] ?? null);
        $file = $this->clean($row['gdrive_file_name'] ?? null);
        $vlogger = $this->clean($row['vlogger'] ?? null);
        $link = $this->clean($row['link_url'] ?? null);

        $title = $featured
            ?: ($file ?: (($vlogger ? $vlogger.' post' : null) ?: 'Video Post'));
        $title = \Illuminate\Support\Str::limit($title, 250, '');

        $vehicleId = null;
        $firstPlate = $this->firstPlate($plateRaw);
        if ($firstPlate) {
            $key = $this->normalizePlate($firstPlate);
            if (isset($vehicleByPlate[$key])) {
                $vehicleId = $vehicleByPlate[$key];
                $linked++;
            } else {
                $unlinked++;
            }
        } else {
            $unlinked++;
        }

        $platform = null;
        if ($link) {
            $host = strtolower(parse_url($link, PHP_URL_HOST) ?: '');
            if (str_contains($host, 'facebook') || str_contains($host, 'fb.')) {
                $platform = 'Facebook';
            } elseif (str_contains($host, 'tiktok')) {
                $platform = 'TikTok';
            } elseif (str_contains($host, 'instagram')) {
                $platform = 'Instagram';
            } elseif (str_contains($host, 'youtube') || str_contains($host, 'youtu.be')) {
                $platform = 'YouTube';
            }
        }

        return [
            'title' => $title,
            'record_date' => ($posted ?: $uploaded)?->format('Y-m-d') ?? now()->toDateString(),
            'type' => VideoPostingRecord::TYPE_VIDEO,
            'platform' => $platform,
            'link_url' => $link ? \Illuminate\Support\Str::limit($link, 500, '') : null,
            'vehicle_id' => $vehicleId,
            'status' => $posted ? VideoPostingRecord::STATUS_POSTED : VideoPostingRecord::STATUS_PENDING,
            'notes' => null,
            'vlogger' => $vlogger,
            'category' => $this->clean($row['category'] ?? null),
            'showroom' => $this->clean($row['showroom'] ?? null),
            'featured_car_or_client' => $featured,
            'plate_number' => $plateRaw ? strtoupper($plateRaw) : null,
            'active_unit' => $this->clean($row['active_unit'] ?? null),
            'date_uploaded_gdrive' => $uploaded?->format('Y-m-d'),
            'date_posted_social' => $posted?->format('Y-m-d'),
            'gdrive_file_name' => $file,
            'source_sheet' => $this->clean($row['source_sheet'] ?? null),
        ];
    }

    private function findExisting(array $payload): ?VideoPostingRecord
    {
        if (! empty($payload['link_url'])) {
            $byLink = VideoPostingRecord::query()->where('link_url', $payload['link_url'])->first();
            if ($byLink) {
                return $byLink;
            }
        }

        if (! empty($payload['gdrive_file_name']) && ! empty($payload['date_posted_social'])) {
            return VideoPostingRecord::query()
                ->where('gdrive_file_name', $payload['gdrive_file_name'])
                ->whereDate('date_posted_social', $payload['date_posted_social'])
                ->where('vlogger', $payload['vlogger'])
                ->first();
        }

        return null;
    }

    private function firstPlate(?string $plateRaw): ?string
    {
        if (! $plateRaw) {
            return null;
        }
        // Split multi-plate values like "ABT9393 / NAX6403"
        $parts = preg_split('/\s*(?:\/|&|,)\s*/', $plateRaw) ?: [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '' && ! preg_match('/^CREATIVE/i', $part)) {
                return $part;
            }
        }

        return null;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[\s\-]+/', '', (string) $plate) ?? '');
    }

    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    private function parseDate($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
