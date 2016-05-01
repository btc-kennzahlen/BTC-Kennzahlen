<?php 
// Parst Informationen Für die Graphen. Es wird die gesammte Blockchain bis zum Aktuellen Block gelesen. 
// Der Vorgang dauert einige Minuten.

// Start Werte für das Test-Net
$height = 0;
$hash = "000000000933ea01ad0ee984209779baaec3ced90fa3f408719526f8d77f4943"; 
$trx = 0; 
$blk = 0; 
$ts = 1296688602; 
$timestamp = strtotime('+1 day', $ts);
    
// Latest Blockhash
$latestblockhashurl = "http://localhost:3001/insight-api/status?q=getLastBlockHash";
$ch = curl_init($latestblockhashurl);
curl_setopt($ch, CURLOPT_URL, $latestblockhashurl);  
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$output = curl_exec($ch);
curl_close($ch);

$startInfo = json_decode($output);
$latestblockhash =  $startInfo->lastblockhash;

// Latest Blockheight
$latestblockurl="http://localhost:3001/insight-api/block/".$latestblockhash;
$ch = curl_init($latestblockurl);
curl_setopt($ch, CURLOPT_URL, $latestblockurl);  
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$output = curl_exec($ch);
curl_close($ch);

$endHeight = json_decode($output);
$latestblockheight = $endHeight->height;


$trxarray = array();
$trxrow = array();
$blkarray = array();
$blkrow = array();
    


// Geht die Blockchain solange durch, bis der aktuelle Block erreicht wurde
while ($height <= $latestblockheight) {
        
    $blockurl="http://localhost:3001/insight-api/block/".$hash;
    $ch = curl_init($blockurl);
    curl_setopt($ch, CURLOPT_URL, $blockurl);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $output = curl_exec($ch);
    curl_close($ch);

    $blockInfo = json_decode($output);
    $height = $blockInfo->height;
    $hash = $blockInfo->nextblockhash;
    $ts = $blockInfo->time;
        
    if ($ts <= $timestamp) {
        $trx += count($blockInfo->tx);
        $blk += 1;
    }else {
        $trxrow['date'] = gmdate("d-m-Y",$timestamp);
        $trxrow['trx']= $trx;
        $blkrow['date'] = gmdate("d-m-Y",$timestamp);
        $blkrow['blk']= $blk;
        $trxarray[] = $trxrow;
        $blkarray[] = $blkrow;
        //$blkarray[gmdate("d-m-Y",$    timestamp)] = $blk;
        echo "Zeit:  ".gmdate("d-m-Y",$timestamp)."\r\n";
        echo "Trx :  ".$trx."\r\n";
        echo "Block: ".$blk."\r\n";
        echo "\r\n";
        $timestamp = strtotime('+1 day', $timestamp);
        
        if ($ts > $timestamp) {
            while ($ts > $timestamp) {
                $trx = 0;
                $blk = 0;
                $trxrow['date'] = gmdate("d-m-Y",$timestamp);
                $trxrow['trx']= $trx;
                $blkrow['date'] = gmdate("d-m-Y",$timestamp);
                $blkrow['blk']= $blk;
                $trxarray[] = $trxrow;
                $blkarray[] = $blkrow;
                //$blkarray[gmdate("d-m-Y",$timestamp)] = $blk;
                echo "Zeit:  ".gmdate("d-m-Y",$timestamp)."\r\n";
                echo "Trx :  ".$trx."\r\n";
                echo "Block: ".$blk."\r\n";
                echo "\r\n";
                $timestamp = strtotime('+1 day', $timestamp);
            }
        }
        $trx = count($blockInfo->tx);
        $blk = 1;
    }

}

//Speichert als json
file_put_contents('data.json', json_encode($trxarray));
file_put_contents('blk.json', json_encode($blkarray));

//Speichert Transaktionen als tsv
$trxarrayheader = array ("date" , "trx");
$fp = fopen('trxdata.tsv', 'w');
fputcsv($fp, $trxarrayheader, "\t");
foreach ($trxarray as $fprow) {
    fputcsv($fp, $fprow, "\t");
}
fclose($fp);

//Speichert Blöcke als tsv
$blkarrayheader = array ("date" , "blk");
$fp = fopen('blkdata.tsv', 'w');
fputcsv($fp, $blkarrayheader, "\t");
foreach ($blkarray as $fprow) {
    fputcsv($fp, $fprow, "\t");
}
fclose($fp);


?>