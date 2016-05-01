<?php
// Liest die IP der Bitcoin-Knoten von einer externen Seite und erstellt eine Tabelle nach Ländern und Anzahl der Knoten

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

$reader = new Reader('GeoLite2DB/GeoLite2-City.mmdb');

// Generiert die Liste aller Knotenpunkte
$url = "https://bitnodes.21.co/api/v1/snapshots/latest/";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close($ch);

$arr = json_decode($output);

$corarr =  (array) $arr;
$coordinates = (array) $corarr['nodes'];

$nodearray = array_keys($coordinates);

//print_r ($nodearray);

$locationcounter = array();
foreach ($nodearray as $element) {
    if ($element != null && isip($element)) {
        
        $nodeip = substr($element, 0, strpos($element, ":"));
        
        $record = $reader->city($nodeip);
        
        if (array_key_exists($record->country->name, $locationcounter)){
            $locationcounter[$record->country->name] += 1;
        } else {   
            $locationcounter[$record->country->name] = 1;
        }   
    }
}

function isip ($stringip) {
    if (   preg_match("!^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3}\:([0-9]{1,5}))$!",$stringip)) {
        return true;
    }
    else {
        return false;
    }
}

asort($locationcounter, SORT_NUMERIC);
echo json_encode(array_reverse($locationcounter));

?>