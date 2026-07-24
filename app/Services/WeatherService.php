<?php

namespace App\Services;

use App\Models\Branch;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Get live weather for a specific branch (or default branch).
     */
    public function getBranchWeather(?int $branchId = null): array
    {
        $branch = null;
        if ($branchId) {
            $branch = Branch::find($branchId);
        }

        if (!$branch) {
            $branch = Branch::first();
        }

        $branchIdKey = $branch?->id ?? 1;
        $cacheKey    = 'live_branch_weather_' . $branchIdKey;

        return Cache::remember($cacheKey, 600, function () use ($branch) {
            return $this->fetchLiveWeatherFromApi($branch);
        });
    }

    private function fetchLiveWeatherFromApi(?Branch $branch): array
    {
        $lat        = $branch?->latitude ?? -1.2621325;
        $lng        = $branch?->longitude ?? 36.774585;
        $branchName = $branch?->name ?? 'Bwibo Restaurant';
        $city       = $branch?->city ?? 'Nairobi';

        try {
            $url      = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lng}&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,rain,weather_code,wind_speed_10m";
            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $current     = $response->json('current') ?? [];
                $tempC       = round((float) ($current['temperature_2m'] ?? 20));
                $weatherCode = (int) ($current['weather_code'] ?? 0);
                $precip      = (float) ($current['precipitation'] ?? 0);
                $rainAmount  = (float) ($current['rain'] ?? 0);
                $humidity    = (int) ($current['relative_humidity_2m'] ?? 50);
                $windSpeed   = round((float) ($current['wind_speed_10m'] ?? 10));

                $info = $this->parseWeatherCode($weatherCode, $precip, $rainAmount);

                $advisory = null;
                if ($info['is_raining']) {
                    $advisory = "🌧️ Live Weather Notice: It is currently {$info['condition_text']} ({$tempC}°C) near {$branchName} ({$city}). Delivery riders are taking extra precautions, so your order may take a few extra minutes. Thank you for your patience!";
                }

                return [
                    'status'         => true,
                    'branch_id'      => $branch?->id,
                    'branch_name'    => $branchName,
                    'city'           => $city,
                    'temp_c'         => $tempC,
                    'weather_code'   => $weatherCode,
                    'condition_text' => $info['condition_text'],
                    'icon'           => $info['icon'],
                    'fa_icon'        => $info['fa_icon'],
                    'is_raining'     => $info['is_raining'],
                    'humidity'       => $humidity,
                    'wind_speed_kmh' => $windSpeed,
                    'rain_advisory'  => $advisory,
                    'fetched_at'     => now()->format('H:i'),
                ];
            }
        } catch (Exception $e) {
            Log::warning('Weather API fetch error: ' . $e->getMessage());
        }

        // Fallback default
        return [
            'status'         => true,
            'branch_id'      => $branch?->id,
            'branch_name'    => $branchName,
            'city'           => $city,
            'temp_c'         => 21,
            'weather_code'   => 0,
            'condition_text' => 'Partly Cloudy',
            'icon'           => '⛅',
            'fa_icon'        => 'fa-cloud-sun',
            'is_raining'     => false,
            'humidity'       => 60,
            'wind_speed_kmh' => 12,
            'rain_advisory'  => null,
            'fetched_at'     => now()->format('H:i'),
        ];
    }

    private function parseWeatherCode(int $code, float $precip, float $rain): array
    {
        $isRaining = ($precip > 0.1 || $rain > 0.1 || in_array($code, [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82, 95, 96, 99]));

        switch (true) {
            case in_array($code, [95, 96, 99]):
                return ['condition_text' => 'Thunderstorm', 'icon' => '⛈️', 'fa_icon' => 'fa-cloud-bolt', 'is_raining' => true];
            case in_array($code, [61, 63, 65, 66, 67]):
                return ['condition_text' => 'Rainy', 'icon' => '🌧️', 'fa_icon' => 'fa-cloud-showers-heavy', 'is_raining' => true];
            case in_array($code, [80, 81, 82]):
                return ['condition_text' => 'Rain Showers', 'icon' => '🌦️', 'fa_icon' => 'fa-cloud-sun-rain', 'is_raining' => true];
            case in_array($code, [51, 53, 55, 56, 57]):
                return ['condition_text' => 'Light Drizzle', 'icon' => '🌧️', 'fa_icon' => 'fa-cloud-rain', 'is_raining' => true];
            case in_array($code, [45, 48]):
                return ['condition_text' => 'Foggy', 'icon' => '🌫️', 'fa_icon' => 'fa-smog', 'is_raining' => false];
            case in_array($code, [1, 2, 3]):
                return ['condition_text' => 'Partly Cloudy', 'icon' => '⛅', 'fa_icon' => 'fa-cloud-sun', 'is_raining' => false];
            case $code === 0:
            default:
                return ['condition_text' => $isRaining ? 'Light Rain' : 'Clear Sky', 'icon' => $isRaining ? '🌧️' : '☀️', 'fa_icon' => $isRaining ? 'fa-cloud-rain' : 'fa-sun', 'is_raining' => $isRaining];
        }
    }
}
