<?php

namespace DigitalEquation\TeamworkDesk;

use GuzzleHttp\Client;

trait HttpClient
{
    public Client $client;

    public function client()
    {
        $this->client = new Client([
            'base_uri' => sprintf('https://%s.teamwork.com/desk/v1/', config('teamwork-desk.domain')),
            'auth'     => [config('teamwork-desk.key'), ''],
        ]);
    }
}