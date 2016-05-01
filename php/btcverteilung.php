<?php
// Erstellt BTC-Verteilung mit Hilfe der "allbalances" Rückgabe, die von dem parser erstellt wurde.
// Zum erstellen der allbalances Datei Wir folgt vorgehen:
// cd parser/
// cd blockparser/
// /parser allbalances  > /home/sven/webserver/parser/allbalances
// Der Vorgang dauert etwas, weil die gesammte Blockchain ausgelesen wird.


$file = fopen("../parser/allbalances","r");

$top100 = 0;        //>100.000
$top100coins = 0;
$top10 = 0;         //100.000-10.000
$top10coins = 0;
$top1 = 0;          //10.000-1.000
$top1coins = 0;

$mid100 = 0;        //1.000-100
$mid100coins = 0;
$mid10 = 0;         //100-10
$mid10coins = 0;
$mid1 = 0;          //10-1 
$mid1coins = 0;

$low100 = 0;        //1-0.1
$low100coins = 0;
$low10 = 0;         //0.1-0.01
$low10coins = 0; 
$low1 = 0;          //0.01-0.001
$low1coins = 0;
$low = 0;           //0.001-0.0001
$lowcoins = 0;

$rest = 0;          //<0.001
$restcoins = 0;

$totalcoins = 0;
$totaladdr = 0;

while(!feof($file)) {
    $row = fgets($file,1024);
    if (preg_match("^([0-9]+).([0-9]{8})^",$row, $matches)) {
        if($matches[0] >= 1000000) {
            $top100 += 1;
            $top100coins += $matches[0];
        } else if($matches[0] >= 100000) {
            $top10 += 1;
            $top10coins += $matches[0];
        } else if($matches[0] >= 10000) {
            $top1 += 1;
            $top1coins += $matches[0];
        } else if($matches[0] >= 1000) {
            $mid100 += 1;
            $mid100coins += $matches[0];
        } else if($matches[0] >= 100) {
            $mid10 += 1;
            $mid10coins += $matches[0];
        } else if($matches[0] >= 10) {
            $mid1 += 1;
            $mid1coins += $matches[0];
        } else if($matches[0] >= 1) {
            $low100 += 1;
            $low100coins += $matches[0];
        } else if($matches[0] >= 0.1) {
            $low10 += 1;
            $low10coins += $matches[0];
        } else if($matches[0] >= 0.01) {
            $low1 += 1;
            $low1coins += $matches[0];
        } else if($matches[0] >= 0.001) {
            $low += 1;
            $lowcoins += $matches[0];
        } else if($matches[0] > -1) {
            $rest += 1;
            $restcoins += $matches[0];
        }
        $totaladdr += 1;
    }
}
fclose($file);

$totalcoins = $top100coins + $top10coins + $top1coins + $mid100coins + $mid10coins +$mid1coins + $low100coins + $low10coins + $low1coins + $restcoins;


$result = fopen("../parser/result.txt","w");

echo fwrite($result, "> 1.000.000: ".$top100.":" ,1000);
echo fwrite($result, "> 1.000.000 %: ".$top100/$totaladdr*100 .":" ,1000);
echo fwrite($result, "> 1.000.000 coins: ".$top100coins.":" ,1000);
echo fwrite($result, "> 1.000.000 % coins: ".$top100coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "1.000.000-100.000: ".$top10.":",1000);
echo fwrite($result, "1.000.000-100.000 %: ".$top10/$totaladdr*100 .":" ,1000);
echo fwrite($result, "1.000.000-100.000 coins: ".$top10coins.":",1000);
echo fwrite($result, "1.000.000-100.000 % coins: ".$top10coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "100.000-10.000: ".$top1.":",1000);
echo fwrite($result, "100.000-10.000 %: ".$top1/$totaladdr*100 .":" ,1000);
echo fwrite($result, "100.000-10.000 coins: ".$top1coins.":",1000);
echo fwrite($result, "100.000-10.000 % coins: ".$top1coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "10.000-1000: ".$mid100.":",1000);
echo fwrite($result, "10.000-1000 %: ".$mid100/$totaladdr*100 .":",1000);
echo fwrite($result, "10.000-1000 coins: ".$mid100coins.":",1000);
echo fwrite($result, "10.000-1000 % coins: ".$mid100coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "1000-100: ".$mid10.":",1000);
echo fwrite($result, "1000-100 %: ".$mid10/$totaladdr*100 .":",1000);
echo fwrite($result, "1000-100 coins: ".$mid10coins.":",1000);
echo fwrite($result, "1000-100 % coins: ".$mid10coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "100-10: ".$mid1.":",1000);
echo fwrite($result, "100-10 %: ".$mid1/$totaladdr*100 .":",1000);
echo fwrite($result, "100-10 coins: ".$mid1coins.":",1000);
echo fwrite($result, "100-10 % coins: ".$mid1coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "10-1: ".$low100.":",1000);
echo fwrite($result, "10-1 %: ".$low100/$totaladdr*100 .":",1000);
echo fwrite($result, "10-1 coins: ".$low100coins.":",1000);
echo fwrite($result, "10-1 % coins: ".$low100coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "1-0,1: ".$low10.":",1000);
echo fwrite($result, "1-0,1 %: ".$low10/$totaladdr*100 .":",1000);
echo fwrite($result, "1-0,1 coins: ".$low10coins.":",1000);
echo fwrite($result, "1-0,1 % coins: ".$low10coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "0,1-0,01: ".$low1.":",1000);
echo fwrite($result, "0,1-0,01 %: ".$low1/$totaladdr*100 .":",1000);
echo fwrite($result, "0,1-0,01 coins: ".$low1coins.":",1000);
echo fwrite($result, "0,1-0,01 % coins: ".$low1coins/$totalcoins*100 ."\n",1000);

echo fwrite($result, "0,01-0,001: ".$low.":",1000);
echo fwrite($result, "0,01-0,001 %: ".$low/$totaladdr*100 .":",1000);
echo fwrite($result, "0,01-0,001 coins: ".$lowcoins.":",1000);
echo fwrite($result, "0,01-0,001 % coins: ".$lowcoins/$totalcoins*100 ."\n",1000);


echo fwrite($result, "< 0,001 : ".$rest.":",1000);
echo fwrite($result, "< 0,001 %: ".$rest/$totaladdr*100 .":",1000);
echo fwrite($result, "< 0,001 coins: ".$restcoins.":",1000);
echo fwrite($result, "< 0,001 % coins: ".$restcoins/$totalcoins*100 ."\n",1000);

fclose($result);
?>