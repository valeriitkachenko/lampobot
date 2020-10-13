<?php

namespace App\Services;

use GuzzleHttp\Client;

class AirQualityService
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * PetService constructor.
     */
    public function __construct()
    {
        $this->setClient();
    }

    /**
     * @inherit
     */
    protected function setClient()
    {
        $this->client = new Client([
            'base_uri' => 'http://api.airvisual.com/v2/',
        ]);
    }

    /**
     * @param string $city
     */
    public function getMessageWithAirQualityIndex(string $city, string $state, string $country)
    {
        $airQuality = $this->getAirQualityIndex($city, $state, $country);

        if (empty($airQuality)) {
            return $this->errorMessage();
        }

        return $this->successMessage($airQuality);
    }

    /**
     * @param string $city
     * @param string $state
     * @param string $country
     * @return array|null
     */
    public function getAirQualityIndex(string $city, string $state, string $country)
    {
        return $this->getAQIByCity($city, $state, $country);
    }

    /**
     * @param string $city
     * @param string $state
     * @param string $country
     * @return array
     */
    protected function getAQIByCity(string $city, string $state, string $country)
    {
        $response = $this->sendGetRequest('city', compact(['city', 'state', 'country']));

        return $response['data'] ?? [];
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array
     */
    protected function sendGetRequest(string $uri, array $params)
    {
        $response = $this->client->get($uri, ['query' => $this->addApiKeyToParams($params)]);
        $responseContent = $response->getBody()->getContents();

        return $this->decodeResponse($responseContent);
    }

    /**
     * @param $params
     * @return array
     */
    private function addApiKeyToParams(array $params)
    {
        return array_merge($params, ['key' => env('AIRVISUAL_API_KEY')]);
    }

    /**
     * @param string $response
     * @return array
     */
    private function decodeResponse(string $response)
    {
        return json_decode($response, true);
    }

    /**
     * @return string
     */
    private function errorMessage()
    {
        return 'Не удалось получить информацию о уровне загрязнения воздуха';
    }

    /**
     * @param array $airQuality
     * @return string
     */
    private function successMessage($airQuality)
    {
        $airQualityIndex = $airQuality['current']['pollution']['aqius'];
        $mainPollutant = $airQuality['current']['pollution']['mainus'];
        $recommendations = $this->getRecommendationsAccordingToAQI($airQualityIndex);
        $city = $airQuality['city'];
        $country = $airQuality['country'];

        $message = sprintf("Индекс качества воздуха в %s, %s: %s.\n", $city, $country, $airQualityIndex);
        $message .= sprintf("%s\n", $recommendations);
        $message .= sprintf("Основной загрязнитель: %s", $mainPollutant);

        return $message;
    }

    private function getRecommendationsAccordingToAQI(int $aqi)
    {
        if ($aqi <= 50) {
            return 'Воздух отличный! Самое время погулять!';
        } elseif ($aqi <= 100) {
            return 'Воздух умеренно грязный.';
        } elseif ($aqi <= 150) {
            return 'Вредный уровень для чувствительных групп.';
        } else {
            return 'Качество воздуха ужасное, лучше остаться дома.';
        }
    }
}
