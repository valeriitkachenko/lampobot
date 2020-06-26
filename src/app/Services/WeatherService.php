<?php

namespace App\Services;

use Carbon\Carbon;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\CurrentWeather;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

class WeatherService
{
    const EMOJI_SUNRISE = "\xF0\x9F\x8C\x85";
    const EMOJI_SUNSET = "\xF0\x9F\x8C\x87";
    const EMOJI_BRIGHTNESS = "\xF0\x9F\x94\x86";
    const EMOJI_WATER_WAVE = "\xF0\x9F\x8C\x8A";
    const EMOJI_EARTH = "\xF0\x9F\x8C\x8D";
    const EMOJI_WIND = "\xF0\x9F\x92\xA8";
    const EMOJI_CLOUDS = "\xE2\x9B\x85";

    /**
     * @var OpenWeatherMap
     */
    private $openWeather;

    public function __construct()
    {
        $httpRequestFactory = new RequestFactory();
        $httpClient = GuzzleAdapter::createWithConfig([]);

        $this->openWeather = new OpenWeatherMap(config('weather.api_key'), $httpClient, $httpRequestFactory);
    }

    /**
     * @param string $city
     */
    public function getMessageWithWeatherForecast($city)
    {
        $weather = $this->getWeather($city);

        if (empty($weather)) {
            return $this->errorMessage();
        }

        return $this->successMessageWithEmojis($weather);
    }

    /**
     * @param $city
     * @return OpenWeatherMap\CurrentWeather|null
     */
    private function getWeather($city)
    {
        try {
            return $weather = $this->openWeather->getWeather($city, config('weather.units'), config('weather.lang'));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param CurrentWeather $weather
     * @return string
     */
    private function successMessageWithEmojis(CurrentWeather $weather)
    {
        $sunrise = Carbon::make($weather->sun->rise);
        $sunset = Carbon::make($weather->sun->set);

        $message = sprintf(" Погода в %s, %s:\n", $weather->city->name, $weather->city->country);
        $message .= sprintf("%s Температура: %s ℃\n", self::EMOJI_BRIGHTNESS , $weather->temperature->now->getValue());
        $message .= sprintf("%s Влажность: %s %%\n", self::EMOJI_WATER_WAVE, $weather->humidity->getValue());
        $message .= sprintf("%s Атмосферное давление: %s Па\n", self::EMOJI_EARTH, $weather->pressure->getValue());
        $message .= sprintf("%s Ветер: %s м/c\n", self::EMOJI_WIND, $weather->wind->speed->getValue());
        $message .= sprintf("%s Облачность: %s\n", self::EMOJI_CLOUDS, $weather->clouds->getDescription());
        $message .= sprintf("%s Рассвет: %s\n", self::EMOJI_SUNRISE,
            $sunrise->setTimezone(config('app.timezone'))->format('H:i')
        );
        $message .= sprintf("%s Закат: %s\n", self::EMOJI_SUNSET,
            $sunset->setTimezone(config('app.timezone'))->format('H:i')
        );

        return $message;
    }

    /**
     * @return string
     */
    private function errorMessage()
    {
        return 'Сервер не доступен или указанного города не существует!';
    }
}
