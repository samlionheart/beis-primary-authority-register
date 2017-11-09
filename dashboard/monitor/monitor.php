<?php
require 'vendor/autoload.php';

use PubNub\PubNub;
use PubNub\PNConfiguration;

$pnconf = new PNConfiguration();
$pnconf->setPublishKey(getenv('GOVUK_PUBNUB_PUBLISH_KEY'));
$pnconf->setSubscribeKey(getenv('GOVUK_PUBNUB_SUBSCRIBE_KEY'));
$pubnub = new PubNub($pnconf);

while (true) {
    
    try {
        $lines = [];
        exec('cf login -a $GOVUK_CF_ENDPOINT -u $GOVUK_CF_USER -p $GOVUK_CF_PASSWORD');
        exec('cf target -o beis-nmo-trial -s sandbox');
        exec('cf app par-beta-production | grep "#"', $lines);
        
        $details['production'] = [];
        
        foreach ($lines as $line) {
            $line = str_replace('    ', '  ', $line);
            $items = explode('  ', $line);
            $mem = explode(' ', trim($items[4]));
            $disk = explode(' ', trim($items[5]));
            
            $details['production'][] = [
                'instance' => trim($items[0]),
                'state' => trim($items[1]),
                'since' => trim($items[2]),
                'cpu' => str_replace('%', '', trim($items[3])),
                'memory' => [
                    'used' => $mem[0],
                    'total' => $mem[2],
                 ],
                'disk' => [
                    'used' => $disk[0],
                    'total' => $disk[2],
                ],
            ];
        }
        
        $result = $pubnub->publish()
            ->channel('cloud_foundry_5')
            ->message($details)
            ->ttl(5)
            ->sync();
        
        $details = [];
        
        sleep(5);
    } catch (\Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
