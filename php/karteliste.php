<?php
require_once 'jsonRPCClient.php';

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

$bitcoin = new jsonRPCClient('http://bitcoinrpc:7CUFYjTDpMXGtKLiiffRbS5zCeZKtMpcemHE5QNqvkpC@127.0.0.1:18332/');
$reader = new Reader('GeoLite2DB/GeoLite2-City.mmdb');


// Generiert die Liste mit denen der lokale Knotenpunkt verbunden ist
//$nodearray = ($bitcoin->getpeerinfo()); //local


// Generiert die Liste aller Knotenpunkte


$locationcounter = array();
foreach ($nodearray as $element) {
    if ($element['addr'] != null) {
        
        $nodeip = substr($element['addr'], 0, strpos($element['addr'], ":"));
        
        $record = $reader->city($nodeip);
        
        if (array_key_exists($record->country->name, $locationcounter)){
            $locationcounter[$record->country->name] += 1;
        } else {   
            $locationcounter[$record->country->name] = 1;
        }   
    }
}

asort($locationcounter, SORT_NUMERIC);
echo json_encode(array_reverse($locationcounter));
?>