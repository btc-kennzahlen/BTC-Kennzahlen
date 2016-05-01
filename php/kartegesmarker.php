<?php
// Benutzt die API einer externen Seite um die Koordinaten der Bitcoin-Knoten auszulesen


$url = "https://bitnodes.21.co/api/v1/snapshots/latest/?field=coordinates";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);  
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$output = curl_exec($ch);
curl_close($ch);

$arr = json_decode($output);

$corarr =  (array) $arr;
$coordinates = $corarr['coordinates'];

echo json_encode($coordinates);
?>