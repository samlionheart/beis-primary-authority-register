<?php

namespace App\Services;
use GuzzleHttp\Client;

class TestResultsService
{
    public function stats() {
        $client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'https://api.cloud.service.gov.uk/v2',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
		]);

		$summary = $client->get('rapps/' + getenv('BEIS_CF_APP_KEY') + '/stats')->getBody()->getContents();

        return [
        	'summary' => json_decode($summary),
        ];
    }
}
