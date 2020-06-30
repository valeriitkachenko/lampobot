<?php

namespace App\Services;

use GuzzleHttp\Client;

class CatService
{
    const IMAGE_TYPE_JPG = 'jpg';
    const IMAGE_TYPE_GIF = 'gif';
    const IMAGE_TYPE_PNG = 'png';

    /**
     * @var Client
     */
    private $client;

    /**
     * CatsService constructor.
     */
    public function __construct()
    {
        $this->setClient();
    }

    /**
     * @inherit
     */
    public function setClient()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.thecatapi.com/v1/images/',
        ]);
    }

    /**
     * @return string
     */
    public function getCatGifUrl()
    {
        return $this->getCatImageUrl(self::IMAGE_TYPE_GIF);
    }

    /**
     * @return string
     */
    public function getCatPicUrl()
    {
        return $this->getCatImageUrl(self::IMAGE_TYPE_JPG);
    }

    /**
     * @param $type
     * @return string
     */
    public function getCatImageUrl($type)
    {
        $catImage = $this->getCatImage($type);

        if (empty($catImage['url'])) {
            return '';
        }

        return $catImage['url'];
    }

    /**
     * @param string $type
     * @return array|null
     */
    public function getCatImage($type)
    {
        $response = $this->client->get('search', ['query' => ['mime_types' => $type]]);
        $responseContent = $response->getBody()->getContents();
        $decodedResponseContent = collect(json_decode($responseContent, true));
        $catImage = $decodedResponseContent->first();

        return $catImage;
    }


}
