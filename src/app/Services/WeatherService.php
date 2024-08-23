<?php

namespace App\Services;

use App\Http\Resources\WeatherResource;
use SolgenPower\LaravelOpenWeather\DataTransferObjects\Weather;
use SolgenPower\LaravelOpenWeather\Facades\OpenWeather;

class WeatherService
{
    public function getMessageWithWeatherForecast(string $city): string
    {
        $weather = $this->getWeather($city);

        return WeatherResource::make($weather)->toFormattedString();
    }

    private function getWeather(string $city): Weather
    {
        $weather = OpenWeather::city($city);
        $weather->city = $city;

        return $weather;
    }
}
