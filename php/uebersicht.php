<?php 

// Erstellt die Übersichts-Seite mithilfe der lokalen Blockchain, der API für die Position der Bitcoin-Knoten und des Blockparsers.
// Vor der Ausführung muss einmal der Parser ausgeführt worden sein mit dem Befehl:
// cd parser/
// cd blockparser/
// /parser simple  > /home/sven/webserver/parser/simplestats
// Der Vorgang dauert etwas, weil die gesammte Blockchain ausgelesen wird.
//

require_once 'jsonRPCClient.php';
// Hier die RPC-Call Daten für die API eingeben:
$bitcoin = new jsonRPCClient('http://bitcoinrpc:7CUFYjTDpMXGtKLiiffRbS5zCeZKtMpcemHE5QNqvkpC@127.0.0.1:18332/');


$blockchaininfo = ($bitcoin->getblockchaininfo());
$estimatefee = ($bitcoin->estimatefee(1));

$numberofblocks = $blockchaininfo['blocks']."<br/>";
$totalbtc = 0;
$btcforblock = 50;


while ($numberofblocks > 0) {
    
    if ($numberofblocks > 210000) {
        $totalbtc += (210000 * $btcforblock);
        $btcforblock = $btcforblock / 2;
    }
    else {
        $totalbtc += ($numberofblocks * $btcforblock);
    }
    $numberofblocks = $numberofblocks - 210000;
}

$blockcount = ($bitcoin->getblockcount());
$blockhash = ($bitcoin->getblockhash($blockcount));
$letzerblock = ($bitcoin->getblock($blockhash));
$blocktime = $letzerblock['time']; 

$timearray = array();
$durchschnittarray = array();

for ($i=0; $i<10; $i++) {
    $tempcount = $blockcount - $i;
    
    $temphash = ($bitcoin->getblockhash($tempcount));
    $tempblock = ($bitcoin->getblock($temphash));
    array_push($timearray, $tempblock['time']); 
    
}

for ($i=9 ; $i>0; $i--) {
    if ($timearray[$i-1] > $timearray[$i]){
    array_push($durchschnittarray, $timearray[$i-1] - $timearray[$i]);
    }
}

$durchschnitt = 0;

if(count($durchschnittarray) > 3 ) {
    $summe = array_sum($durchschnittarray);
    $anzahl = count($durchschnittarray);
    $durchschnitt = $summe / $anzahl;
}


date_default_timezone_set("Europa/Berlin");
    
$inmin = ($bitcoin->getnetworkhashps(-1, -1))/60;


// Anzahl der Knoten parsen
$url = "https://bitnodes.21.co/api/v1/snapshots/latest/?field=coordinates";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);  
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$output = curl_exec($ch);
curl_close($ch);

$gesKnoten = json_decode($output);

$activenodes = $gesKnoten->total_nodes;

// Parst Informationen aus der "simple" Rückgabe
$anztrx = 0;
$avgtrx = 0;
$vol = 0;

$file = fopen("../parser/simplestats","r");
while(!feof($file)) {
    $row = fgets($file,1024);
    if (preg_match("^nbTransactions = ([0-9]+)^",$row, $matches)) {
        $anztrx = $matches[1];
    }
    if (preg_match("^avg tx per block = ([0-9]+)^",$row, $matches)) {
        $avgtrx = $matches[1];
    }
    if (preg_match("^volume = ([0-9]+)^",$row, $matches)) {
        $vol = $matches[1];
    }
}
fclose($file);


$table = '
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th"><h3>Allgemeine Informationen </h3></th>
        </tr>
        <tr>
            <td>Bitcoins im Umlauf</td>
            <td class="text-right">'.number_format($totalbtc,8,',','.').'</td> 
        </tr>
        <tr>
            <td>Transaktionen</td>
            <td class="text-right">'.number_format($anztrx,0,',','.').'</td> 
        </tr>
        <tr>
            <td>Gehandeltes Volumen</td>
            <td class="text-right">'.number_format($vol,0,',','.').'</td> 
        </tr>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th"><h3>Block Informationen </h3></th>
        </tr>
        <tr>
            <td>Anzahl der Blöcke</td>
            <td class="text-right">'.number_format($bitcoin->getblockcount(),0,',','.').'</td> 
        </tr>
        <tr>
            <td>Durchschnittliche Transaktionen pro Block</td>
            <td class="text-right">'.number_format($avgtrx,0,',','.').'</td> 
        </tr>
        <tr>
            <td>Zeitstempel des letzten Blocks</td>
            <td class="text-right">'.date("H:i:s", $blocktime).'</td> 
        </tr>
        <tr>
            <td>Geschätze Zeit zwischen zwei Blöcken</td>
            <td class="text-right">'.(($durchschnitt/60)%60).' min</td> 
        </tr>
        <tr>
            <td>Aktuellester Blockhash</td>
            <td class="text-right">'.$bitcoin->getbestblockhash() .'</td> 
        </tr>
    </table>
</div>
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th"><h3>Netzwerk Informationen </h3></th>
        </tr>
        <tr>
            <td>Anzahl der aktiven Bitcoin-Knoten</td>
            <td class="text-right">'.$activenodes.'</td> 
        </tr>
        <tr>
            <td>Bitcoin-Netzwerkhashrate pro Sekunde</td>
            <td class="text-right">'.$bitcoin->getnetworkhashps(-1, -1).'</td> 
        </tr>

    </table>
</div>';

echo $table;

?>
