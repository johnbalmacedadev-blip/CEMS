<?php

namespace App\Console\Commands;

use App\Models\RecommendationTracker;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportRecommendationTrackerCommand extends Command
{
    protected $signature = 'import:recommendation-tracker
                            {--file= : Path to JSON export}
                            {--fresh : Truncate recommendation_trackers before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import Recommendation Tracker Excel JSON and link plates to vehicles';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/recommendation_tracker_import.json');
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
            $key = RecommendationTracker::normalizePlate($vehicle->plate_number);
            if ($key !== '') {
                $vehicleByPlate[$key] = $vehicle->id;
            }
        }

        if ($this->option('fresh') && ! $this->option('dry-run')) {
            RecommendationTracker::query()->delete();
            $this->warn('Cleared existing recommendation_trackers rows.');
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
                $plate = isset($row['plate_number']) ? strtoupper(trim((string) $row['plate_number'])) : null;
                if ($plate === '') {
                    $plate = null;
                }

                $payload = $this->mapRow($row, $plate);

                if ($plate) {
                    $key = RecommendationTracker::normalizePlate($plate);
                    if (isset($vehicleByPlate[$key])) {
                        $payload['vehicle_id'] = $vehicleByPlate[$key];
                        $linked++;
                    } else {
                        $unlinked++;
                    }
                } else {
                    $unlinked++;
                }

                if ($this->option('dry-run')) {
                    $created++;
                    $bar->advance();
                    continue;
                }

                $existing = null;
                if ($plate) {
                    $existing = RecommendationTracker::query()
                        ->whereRaw("UPPER(REPLACE(REPLACE(COALESCE(plate_number, ''), ' ', ''), '-', '')) = ?", [
                            RecommendationTracker::normalizePlate($plate),
                        ])
                        ->first();
                }

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    RecommendationTracker::create($payload);
                    $created++;
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

    private function mapRow(array $row, ?string $plate): array
    {
        $purchaseDate = $this->parseDate($row['purchase_date'] ?? null);
        $paintRec = $this->cleanText($row['paint_recommendation'] ?? null);
        $tiresRec = $this->cleanText($row['tires_recommendation'] ?? null);
        $mechRec = $this->cleanText($row['mechanical_recommendation'] ?? null);

        $payload = [
            'date' => $purchaseDate?->format('Y-m-d') ?? now()->toDateString(),
            'year' => $this->cleanText($row['year'] ?? null, 20),
            'customer' => $this->cleanText($row['purchased_from'] ?? null),
            'make' => $this->cleanText($row['make'] ?? null),
            'model' => $this->cleanText($row['model'] ?? null),
            'paint' => $paintRec ? \Illuminate\Support\Str::limit(str_replace(["\r\n", "\n"], '; ', $paintRec), 250, '') : null,
            'plate_number' => $plate,
            'variant' => $this->cleanText($row['variant'] ?? null),
            'transmission' => $this->cleanText($row['transmission'] ?? null),
            'fuel_type' => $this->cleanText($row['fuel_type'] ?? null),
            'color' => $this->cleanText($row['color'] ?? null),
            'purchase_price' => $this->toDecimal($row['purchase_price'] ?? null),
            'purchased_from' => $this->cleanText($row['purchased_from'] ?? null),
            'purchase_date' => $purchaseDate?->format('Y-m-d'),
            'final_status' => $this->cleanText($row['final_status'] ?? null),
            'paint_recommendation' => $paintRec,
            'paint_completion' => $this->cleanText($row['paint_completion'] ?? null),
            'mechanical_recommendation' => $mechRec,
            'mechanical_completion' => $this->cleanText($row['mechanical_completion'] ?? null),
            'electrical_recommendation' => $this->cleanText($row['electrical_recommendation'] ?? null),
            'electrical_completion' => $this->cleanText($row['electrical_completion'] ?? null),
            'ecu_cluster_recommendation' => $this->cleanText($row['ecu_cluster_recommendation'] ?? null),
            'ecu_cluster_completion' => $this->cleanText($row['ecu_cluster_completion'] ?? null),
            'aircon_recommendation' => $this->cleanText($row['aircon_recommendation'] ?? null),
            'aircon_completion' => $this->cleanText($row['aircon_completion'] ?? null),
            'interior_recommendation' => $this->cleanText($row['interior_recommendation'] ?? null),
            'interior_completion' => $this->cleanText($row['interior_completion'] ?? null),
            'tires_recommendation' => $tiresRec,
            'tires_completion' => $this->cleanText($row['tires_completion'] ?? null),
            'battery_recommendation' => $this->cleanText($row['battery_recommendation'] ?? null),
            'battery_completion' => $this->cleanText($row['battery_completion'] ?? null),
            'misc_recommendation' => $this->cleanText($row['misc_recommendation'] ?? null),
            'misc_completion' => $this->cleanText($row['misc_completion'] ?? null),
            'odometers' => isset($row['kilometers']) && $row['kilometers'] !== null
                ? (string) (int) round((float) $row['kilometers'])
                : null,
            'with_tools' => $this->isYes($row['with_tools'] ?? null),
            'with_matting_complete' => $this->isYes($row['with_matting'] ?? null),
            'with_spare_tire' => $this->isYes($row['with_spare_tire'] ?? null),
            'with_spare_key' => $this->isYes($row['spare_key'] ?? null),
            'vehicle_id' => null,
        ];

        foreach ($this->parseChecklistFlags(($paintRec ?? '')."\n".($tiresRec ?? '')."\n".($mechRec ?? '')) as $key => $val) {
            $payload[$key] = $val;
        }

        return $payload;
    }

    private function parseChecklistFlags(string $text): array
    {
        $u = strtoupper($text);
        $flags = [];

        $map = [
            'hood' => ['HOOD'],
            'front_bumper' => ['FRONT BUMPER'],
            'grille' => ['GRILLE', 'GRILL'],
            'fender_right' => ['FENDER RIGHT', 'PENDER RIGHT', 'RIGHT FENDER'],
            'fender_left' => ['FENDER LEFT', 'LEFT FENDER'],
            'step_board_left' => ['STEP BOARD LEFT'],
            'step_board_right' => ['STEP BOARD RIGHT'],
            'trunk_lid' => ['TRUNK LID', 'TRUNK LEED'],
            'rear_bumper' => ['REAR BUMPER'],
            'quarter_panel_right' => ['QUARTER PANEL RIGHT'],
            'quarter_panels_left' => ['QUARTER PANEL LEFT', 'QUARTER PANELS LEFT'],
            'passenger_door_right_rear' => ['PASSENGER DOOR RIGHT REAR', 'PASSENGER DOOR RIGHT REAR SIDE'],
            'passenger_door_right_front' => ['PASSENGER DOOR RIGHT FRONT', 'PASSENGER DOOR RIGHT FRONT SIDE', 'PASSENGER DOOR'],
            'roof' => ['ROOF'],
            'spoiler' => ['SPOILER'],
            'side_mirror_left' => ['SIDE MIRROR LEFT'],
            'side_mirror_right' => ['SIDE MIRROR RIGHT'],
            'tire_1' => ['TIRE 1'],
            'tire_2' => ['TIRE 2'],
            'tire_3' => ['TIRE 3'],
            'tire_4' => ['TIRE 4'],
        ];

        foreach ($map as $key => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($u, $needle)) {
                    $flags[$key] = true;
                    break;
                }
            }
        }

        return $flags;
    }

    private function cleanText($value, ?int $max = null): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);
        if ($s === '' || strtoupper($s) === 'N/A' || $s === '-' || $s === '—') {
            return null;
        }
        if ($max !== null) {
            $s = mb_substr($s, 0, $max);
        }

        return $s;
    }

    private function isYes($value): bool
    {
        if ($value === null) {
            return false;
        }
        $s = strtoupper(trim((string) $value));

        return in_array($s, ['YES', 'Y', '1', 'TRUE'], true);
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

    private function toDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }
        $clean = preg_replace('/[^\d.]/', '', (string) $value);

        return $clean === '' ? null : round((float) $clean, 2);
    }
}
