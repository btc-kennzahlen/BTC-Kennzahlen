<?php 

// Erstellt die Infoseite über die Verteilung aller Bitcoins auf die Adressen.
// Die Datei result.txt muss existieren. Wird erstellt von "btcverteilung.php".

$file = fopen("../parser/result.txt","r");
error_reporting(E_ALL ^  E_NOTICE);

$info ="";
$toggle = false;

while(!feof($file)) {
    $row = fgets($file,10000);    
    $rowarr = preg_split('/:/', $row, -1, PREG_SPLIT_NO_EMPTY);
    $info = $info."<tr>";
    if (array_key_exists(0, $rowarr)) {
        $info = $info.'<td class="text-right">'.$rowarr[0].'</td>';
    }
    if (array_key_exists(1, $rowarr)) {
        $info = $info.'<td class="text-right">'.number_format($rowarr[1],0,',','.').'</td>';
    }
    if (array_key_exists(3, $rowarr)) {
        $info = $info.'<td class="text-right">'.number_format($rowarr[3],8,',','.').'</td>';
    }
    if (array_key_exists(5, $rowarr)) {
        $info = $info.'<td class="text-right">'.number_format($rowarr[5],8,',','.').'</td>';
    }
    if (array_key_exists(7, $rowarr)) {
        $info = $info.'<td class="text-right">'.number_format($rowarr[7],8,',','.').'</td>';
    }
}

fclose($file);

$table = '<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <td class="text-right">Balance</td>
            <td class="text-right">Addressen</td>
            <td class="text-right">Addressen in Prozent</td>
            <td class="text-right">Bitcoins</td>
            <td class="text-right">Bitcoins in Prozent</td>
        </tr>
        '.$info.'
    </table>
</div>';

echo $table;
?>