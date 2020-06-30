<?php

namespace App\Services;

use GuzzleHttp\Client;

class DogService extends PetService
{
    /**
     * @inherit
     */
    protected function setClient()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.thedogapi.com/v1/images/',
        ]);
    }
}
