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
                         
    
    
    // apps_req = urllib2.Request('https://' + os.environ['CF_ENDPOINT'] + '/v2/apps')
    // apps_req.add_header('Authorization', 'Bearer ' + access_token)

    // apps_resp = urllib2.urlopen(apps_req)
    // apps_content = json.load(apps_resp)
    
    // resources = apps_content['resources']
    
    // for resource in resources:
    //     if resource['entity']['name'] == 'par-beta-production':
    //         guid = resource['metadata']['guid']
    //         break
            
    // print guid
        
    // stats_req = urllib2.Request('https://' + os.environ['CF_ENDPOINT'] + '/v2/apps/' + guid + '/stats')
    // stats_req.add_header('Authorization', 'Bearer ' + access_token)


        $client = new Client([
		    // Base URI is used with relative requests
		    'base_uri' => 'https://api.cloud.service.gov.uk',
		    // You can set any number of default request options.
		    'timeout'  => 2.0,
            'headers' => [
                'Authorization' => 'Bearer '  $accessToken,
            ],
		]);

		$summary = $client->get('/v2/apps/' . getenv('BEIS_CF_APP_KEY') . '/stats')->getBody()->getContents();

        return [
        	'summary' => json_decode($summary),
        ];
    }
}
