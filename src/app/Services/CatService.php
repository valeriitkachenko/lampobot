<?php

namespace App\Services;

use GuzzleHttp\Client;

class CatService extends PetService
{
    /**
     * @inherit
     */
    protected function setClient()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.thecatapi.com/v1/images/',
        ]);
    }
}
