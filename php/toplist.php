<?php 
// Erstellt die Seite der Top-Addressen. Dafür muss der Blockparser einmal die Datei top erstellt haben.
// Um die Datei zu erstellen benutze:
// cd parser/
// cd blockparser/
// ./parser allbalances --limit=50 > /home/sven/webserver/parser/top

$file = fopen("../parser/top","r");

$info = "";
$month = "";
$toggle = false;

while(!feof($file)) {
    $row = fgets($file,10000);
    if (preg_match("^([0-9]+).([0-9]{8})^",$row, $matches)) {
        
        $rowarr = preg_split('/ /', $row, -1, PREG_SPLIT_NO_EMPTY);
        
        $info = $info."<tr>
                        <td>".$rowarr[0]."</td>
                        <td>".$rowarr[1]."</td>
                        <td>".$rowarr[2]."</td>
                        <td>".$rowarr[3]."</td>
                        <td>".$rowarr[7]." ".$rowarr[6].". ".monthchecker($rowarr[5])." ".$rowarr[8]."</td>
                        <td>".$rowarr[9]."</td>
                        <td>".$rowarr[13]." ".$rowarr[12].". ".monthchecker($rowarr[11])." ".$rowarr[14]."</td>
                    </tr>";
    }
}
fclose($file);

//echo $info;

$table = '
<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <th"><h3>Bitcoin Topliste</h3></th>
        </tr>
        <tr>
            <td>Bitcoins</td>
            <td>Hash160</td>
            <td>Base58</td>
            <td>Eingegangene Transaktionen</td>
            <td>Zeitpunkt der letzten eingegangenden Transaktion</td>
            <td>Ausgegangene Transaktionen</td>
            <td>Zeitpunkt der letzten ausgegangenen Transaktionen</td>
        </tr>
        '.$info.'
    </table>
</div>';

echo $table;

function monthchecker ($element) {
    if ($element == "Jan") {
        return "Januar";
    } else if ($element == "Feb") {
        return "Februar";
    }  else if ($element == "Mar") {
        return "März";
    } else if ($element == "Apr") {
        return "April";
    } else if ($element == "May") {
        return "Mai";
    } else if ($element == "Jun") {
        return "Juni";
    } else if ($element == "Jul") {
        return"Juli";
    } else if ($element == "Aug") {
        return "August";
    } else if ($element == "Oct") {
        return "Oktober";
    } else if ($element == "Sep") {
        return "September";
    } else if ($element == "Nov") {
        return "November";
    } else if ($element == "Dec") {
        return "Dezember";
    } else {
        return $element;
    }
}

?>