<?php
// Erstellt die Position der Knoten-Punkte die mit dem lokalen Server verbunden sind.


require_once 'jsonRPCClient.php';

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;


// Hier den Benutzer und das Passwort für die RPC-Calls des lokalen Bitcoin-Clients einfügen:
$bitcoin = new jsonRPCClient('http://bitcoinrpc:7CUFYjTDpMXGtKLiiffRbS5zCeZKtMpcemHE5QNqvkpC@127.0.0.1:18332/');
$reader = new Reader('GeoLite2DB/GeoLite2-City.mmdb');

$nodearray = ($bitcoin->getpeerinfo());

$coordinates = array();
foreach ($nodearray as $element) {
    if ($element['addr'] != null) {
        
        $nodeip = substr($element['addr'], 0, strpos($element['addr'], ":"));
        
        $record = $reader->city($nodeip);
        $lat = ($record->location->latitude); 
        $lng = ($record->location->longitude);
        
        $coordinates[] = array($lat, $lng);
    }
}

echo json_encode($coordinates);
?>