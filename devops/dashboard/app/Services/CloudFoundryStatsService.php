<?php

namespace App\Services;
use GuzzleHttp\Client;

class CloudFoundryStatsService
{
    public function stats() {

        $tokenData = 'username=' . getenv('CF_LOGIN_EMAIL') . '&password=' . getenv('CF_LOGIN_PASSWORD') . '&grant_type=password&scopes=*';

        $client = new Client([
            'base_uri' => 'https://' . getenv('CF_LOGIN_ENDPOINT'),
            'timeout'  => 2.0,
            'headers' => ['Authorization' => 'Basic Y2Y6'],
        ]);

        $tokenResp = $client->get('/oauth/token?' . $tokenData)->getBody()->getContents();
        $accessToken = json_decode($tokenResp)->access_token;
                         
        $client = new Client([
            'base_uri' => 'https://' . getenv('CF_ENDPOINT'),
            'timeout'  => 2.0,
            'headers' => ['Authorization' => 'Bearer ' . $accessToken],
        ]);
    
        $appsResp = $client->get('/v2/apps')->getBody()->getContents();
        $resources = json_decode($appsResp, true)['resources'];

        $stats = [];
        foreach ($resources as $resource) {
            if ($resource['entity']['name'] == 'par-beta-production') {
                $guid = $resource['metadata']['guid'];
                $statsResp = $client->get('/v2/apps/' . $guid . '/stats')->getBody()->getContents();
                $stats = json_decode($statsResp, true);
            }
        }

        return ['message' => $stats];
    }
}
