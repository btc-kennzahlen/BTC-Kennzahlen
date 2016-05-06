<?php
// Suche nach Blöcken, Transaktionen und Addressen mithilfe der insight-API
$postval = $_GET['searchValue'];
date_default_timezone_set("UTC");
$return = "Ungültige Suche";
$found = false;


/*
 * Transaktionssuche
 *
 */
$url = "http://localhost:3001/insight-api/tx/".$postval;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_URL, $url);  
curl_setopt($ch, CURLOPT_HEADER, 0);  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
$output = curl_exec($ch);
curl_close($ch);
if (!$found) {
    if ($output != "Not found") {
        $tx = json_decode($output);
        
        if(!isset($tx->txid)) {
            $return = "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";   
        }else if($tx->txid != $postval) {
            return "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";
        }else {

        
        $tmparr = $tx->vin;
        $senderaddr = "";

            foreach ($tmparr as $element) {
                if (isset($element->addrif)) {
                    $senderaddr = $senderaddr.'<tr>
                                                    <td>'.$element->addr.'</td>
                                                    <td class="text-right"> '.$element->value.' BTC</td> 
                                                    </tr>';
                }
            }

        $tmparr = $tx->vout;
        $empfaddr = "";

        foreach ($tmparr as $element) {
            if ($element->scriptPubKey != null) {
                $empfaddr = $empfaddr.'<tr>
                                                <td>'.$element->scriptPubKey->addresses[0].'</td>
                                                <td class="text-right"> '.$element->value.' BTC</td> 
                                                </tr>';
            }
        }

        if(!isset($tx->valueIn)) {
            $valueIn = '<span class="text-right">Coinbase-Transaktion';   
        }
        else {
            $valueIn = 'Sender von <span class="text-right">'.$tx->valueIn.' BTC';   
        }
        
        if(!isset($tx->fees)) {
            $fees = 'Coinbase-Transaktion, daher keine Gebühren';   
        }
        else {
            $fees = $tx->fees.' BTC';   
        }
        

        $return = '
        <div class="table-responsive">
            <h3>Transaktion: '.$tx->txid.'</h3>
            <table class="table table-striped">
                <tr>
                    <th">'.$valueIn.'</span></h3>
                    </th>
                </tr>'.$senderaddr.'
            </table>
            <br></br>
            <table class="table table-striped">
                <tr><th">Empfänger von '.$tx->valueOut.' BTC</span></h3></th>
                </tr>'.$empfaddr.'
            </table>
            <br></br>
            <table class="table table-striped">
                <tr>
                    <td>Zeitpunkt</td>
                    <td class="text-right">'.date("H:i:s d.m.Y", $tx->time).' UTC</td> 
                </tr>
                <tr>
                    <td>Bestätigungen:</td>
                    <td class="text-right">'.$tx->confirmations.'</td> 
                </tr>
                <tr>
                    <td>Gesammtes Volumen:</td>
                    <td class="text-right">'.$valueIn.'</td> 
                </tr>
                <tr>
                    <td>Gebühr:</td>
                    <td class="text-right">'.$fees.'</td> 
                </tr>
                <tr>
                    <td>Im Blockhash</td>
                    <td class="text-right">'.$tx->blockhash.'</td> 
                </tr>
            </table>
        </div>';

        $found = true;
    }
}
}


/*
 * Blocksuche
 *
 */

if (!$found) {
    $url = "http://localhost:3001/insight-api/block/".$postval;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $output = curl_exec($ch);
    curl_close($ch);

    if ($output != "Not found") {
        $blk = json_decode($output);
        
        if(!isset($blk->hash)) {
            $return = "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";   
        }else if($blk->hash != $postval) {
            return "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";
        }else {
        
        
        $return = '
        <div class="table-responsive">
            <h3>Blockhash: '.$blk->hash.'</h3>
            <table class="table table-striped">
                <tr>
                    <td>Höhe</td>
                    <td class="text-right">'.$blk->height.'</td> 
                </tr>
                <tr>
                    <td>Bestätigungen:</td>
                    <td class="text-right">'.$blk->confirmations.'</td> 
                </tr>
                <tr>
                    <td>Zeitpunkt</td>
                    <td class="text-right">'.date("H:i:s d.m.Y", $blk->time).' UTC</td> 
                </tr>
                <tr>
                    <td>Anzahl der Transaktionen:</td>
                    <td class="text-right">'.count($blk->tx).'</td> 
                </tr>
                <tr>
                    <td>Vorheriger Block:</td>
                    <td class="text-right">'.$blk->previousblockhash.'</td> 
                </tr>
                <tr>
                    <td>Nächster Block:</td>
                    <td class="text-right">'.$blk->nextblockhash.'</td> 
                </tr>
            </table>
        </div>';

        $found = true;
    }
}
}

/*
 * Addresssuche
 *
 */
if (!$found) {
    $url = "http://localhost:3001/insight-api/addr/".$postval;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);  
    curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $output = curl_exec($ch);
    curl_close($ch);
    
    $addr = json_decode($output);

    
    if(!isset($addr->addrStr)) {
        $return = "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";   
    }else if($addr->addrStr != $postval) {
        $return = "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";
    }else {


    if ($output != "Not found" || $output != "Invalid address: Checksum mismatch. Code:1") {
        $addr = json_decode($output);
        
        if($addr->addrStr != $postval) {
            return "Ungültige Suche, bitte überprüfen Sie ihre Eingabe.";
        }


        
        $returntx = "";
        if (isset($addr->transactions)) {
            $tmparr = $addr->transactions;
            
        
            foreach ($tmparr as $element) {
                    $returntx = $returntx.'<tr>
                                           <td>'.$element.'</td>
                                           </tr>';
                }
            }

        $return = '
        <div class="table-responsive">
            <h3>Addresse: '.$addr->addrStr.'</h3>
            <table class="table table-striped">
                <tr>
                    <td>Balance: </td>
                    <td class="text-right">'.$addr->balance.'</td> 
                </tr>
                <tr>
                    <td>Bisher erhaltene BTC:</td>
                    <td class="text-right">'.$addr->totalReceived.'</td> 
                </tr>
                <tr>
                    <td>Bisher versendete BTC:</td>
                    <td class="text-right">'.$addr->totalSent.' UTC</td> 
                </tr>
                <tr>
                    <td>Anzahl der Transaktionen:</td>
                    <td class="text-right">'.$addr->txApperances.'</td> 
                </tr>
                </table>
                <table class="table table-striped">
                <tr>
                    <th">Transaktionen:</span></h3>
                    </th>
                </tr>'.$returntx.'
            </table>
        </div>';

        $found = true;
    }
    }
}

echo $return;
?>