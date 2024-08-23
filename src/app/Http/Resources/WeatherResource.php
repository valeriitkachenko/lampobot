<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Emoji\Emoji;

class WeatherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'location' => $this->getLocation(),
            'description' => $this->getDescription(),
            'temperature' => $this->getTemperature(),
            'humidity' => $this->getHumidity(),
            'pressure' => $this->getPressure(),
            'wind' => $this->getWind(),
            'cloudiness' => $this->getCloudiness(),
            'sunrise' => $this->getSunrise(),
            'sunset' => $this->getSunset()
        ];
    }

    public function toFormattedString(): string
    {
        return implode("\n", $this->toArray(request()));
    }

    private function getLocation(): string
    {
        return "Weather in {$this->city}, {$this->countryCode}:\n";
    }

    private function getDescription(): string
    {
        return Emoji::information() . ' ' . ucfirst($this->description);
    }

    private function getTemperature(): string
    {
        return Emoji::brightButton() . " Temperature: {$this->temperature} °C";
    }

    private function getHumidity(): string
    {
        return Emoji::waterWave() . " Humidity: {$this->humidity}%";
    }

    private function getPressure(): string
    {
        return Emoji::globeShowingAmericas() . " Pressure: {$this->pressure} Pa";
    }

    private function getWind(): string
    {
        return Emoji::DashingAway() . " Wind: {$this->windSpeed} m/s, {$this->windDirection}";
    }

    private function getCloudiness(): string
    {
        return Emoji::SunBehindSmallCloud() . " Cloudiness: {$this->cloudiness}";
    }

    private function getSunrise(): string
    {
        return Emoji::sunrise() . " Sunrise: {$this->formatDate($this->sunrise)}";
    }

    private function getSunset(): string
    {
        return Emoji::sunset() . " Sunset: {$this->formatDate($this->sunset)}";
    }

    private function formatDate(Carbon $date): string
    {
        return $date->setTimezone(seconds_to_hours($this->timezone))->format('g:i A');
    }
}
