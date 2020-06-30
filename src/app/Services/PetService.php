<?php

namespace App\Services;

use GuzzleHttp\Client;

abstract class PetService
{
    const IMAGE_TYPE_JPG = 'jpg';
    const IMAGE_TYPE_GIF = 'gif';

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
    abstract protected function setClient();

    /**
     * @return string
     */
    public function getGifUrl()
    {
        return $this->getImageUrl(self::IMAGE_TYPE_GIF);
    }

    /**
     * @return string
     */
    public function getPicUrl()
    {
        return $this->getImageUrl(self::IMAGE_TYPE_JPG);
    }

    /**
     * @param $type
     * @return string
     */
    protected function getImageUrl($type)
    {
        $petImage = $this->getImage($type);

        if (empty($petImage['url'])) {
            return '';
        }

        return $petImage['url'];
    }

    /**
     * @param string $type
     * @return array|null
     */
    protected function getImage($type)
    {
        $response = $this->client->get('search', ['query' => ['mime_types' => $type]]);
        $responseContent = $response->getBody()->getContents();
        $decodedResponseContent = collect(json_decode($responseContent, true));
        $petImage = $decodedResponseContent->first();

        return $petImage;
    }
}
