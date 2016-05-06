# Allgemeine Information
Dieses Projekt ist im Rahmen eines Master Projekts an dem Institut für Internetsicherheit entstanden. Das Ergebnis ist eine Webseite, die unterschiedliche Dienste nutzt um Informationen aus der Blockchain und anderen Quellen über Bitcoins und das Bitcoin-Netzwerk aufbereitet darstellt.

# Inbetriebnahme
Als Grundsystem wird ein 64-Bit Linux benötigt. Mit ausreichend Speicherplatz und einer großen Swap-Partition (20+ Gig). Außerdem wird ein Webserver und php auf dem System benötigt.

Die Daten werden Hauptsächlich aus der Blockchain gezogen. Hierfür wird die Bitcoin-Blockchain so wie die insight-api von Bitpay lokal auf dem System benötigt. Da die insight-api auf bitcore-node von bitpay aufsetzt und dieser die Blockchain pflegt, reicht es dieses System zu installieren.

[bitcore-node] (https://github.com/bitpay/bitcore-node)

[insight-API] (https://github.com/bitpay/insight-api)

Auf den github Seiten steht wie die jeweiligen Systeme zu installieren sind. Hierbei ist zu beachten, das zu erst bitcore-node und anschließend die insight-api installiert werden. Die insight-api muss während des Betriebs der Webseite im Hintergrund laufen.


Hier folgt ein Beispiel wie auf Ubuntu 14.10 vorgegangen wird.
Zunächst muss git installiert werden:
```
apt-get install git
```

Weil bitcore-node eine bestimmte nvm Version benötigt, wird diese gleich mit installiert.
Zunächst wird [nvm](https://github.com/creationix/nvm) installiert:
```
curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.0/install.sh | bash
```
Wenn nvm korrekt installiert ist lässt sich mit dem folgenden Befehl die benötigte Version beziehen:
```
nvm install v0.12
```
Jetzt werden noch die Abhängigkeiten für bitcore-node installiert:
```
apt-get install build-essential libtool autotools-dev automake autoconf pkg-config libssl-dev
```
Nun kann das git Repository geclont werden:
```
git clone https://github.com/bitpay/bitcore-node.git
cd bitcore-node
npm install
```
Mit
```
npm start
```
kann Überprüft werden ob alles ordnungsgemäß läuft. Wenn ohne es Fehlermeldung startet, mit “Strg+C” abbrechen. Es kann insight-api installiert werden.

Auf dem Beispiel System wurde dazu im Home-Verzeichnis folgender Befehl ausgeführt:
```
/home/$user/bitcore-node/bin/bitcore-node create mynode
cd mynode
/home/$user/bitcore-node/bin/bitcore-node install insight-api
``` 

Wenn es ohne Fehlermeldung durchlief, ist jetzt bitcore-node mit der insight-api auf dem System installiert. Um auf die Blockchain mit RPC-Aufrufen zugreifen zu können, muss jetzt die bitcoin.conf angepasst werden. 
```
gedit /home/$user/mynode/data/bitcoin-conf  
```
Hier müssen jetzt folgende Zeilen hinzugefügt werden:
```
whitelist=127.0.0.1
txindex=1
server=1
rpcuser=bitcoinrpc
rpcpassword=7CUFYjTDpMXGtKLiiffRbS5zCeZKtMpcemHE5QNqvkpC
rpcport=18332
rpcconnect=127.0.0.1

```
Der “rpcuser” Name sowie das “rpcpassword” kann frei gewählt werden, muss dann aber in den php-Dateien angepasst werden.
Jetzt kann damit begonnen werden die Blockchain über die insight-api herunterzuladen. 
```
cd /home/$user/mynode
cd /home/$user/bitcore-node/bin/bitcore-node start
```
Mit dem obigen Befehl wird die API gestartet und beginnt automatisch mit dem Herrunterladen der Blockchain. Wichtig ist, dass dieser Befehl ausgeführt wird während man in dem “mynode”-Verzeichnis ist, da sonst nicht die Blockchain für die insight-api mit bezug auf die entsprechende Konfigurationsdatei herruntergeladen wird. 

Dieser Vorgang kann einige Tage dauern. Außerdem muss jeder Zeit im Hintergrund die insight-api laufen.


Nachdem die insight-api installiert und die Blockchain herrunter geladen wurde, wird jetzt in dem Home-Verzeichnis ein neuer Ordner angelegt und die Dateien für das BTC-Kennzahlensystem herrein kopiert. Die Daten können direkt von github geclont werden.
```
cd /home/$user/
git clone https://github.com/btc-kennzahlen/BTC-Kennzahlen.git
```
Auf dem Beispiel System heißt der Ordner “BTC-Kennzahlen”. In diesem Ordner wird nun ein Parser installiert, welcher die Blockchain durchläuft und Daten in einer Dateispeichert. Den Parser inklusive Installationsanleitung findet man hier:  [https://github.com/znort987/blockparser] (https://github.com/znort987/blockparser)

Um den Parser auf dem Beispiel System zu installieren wurde wiefolgt vorgegangen:
Zunächst wurden die benötigten Abhängigkeiten installiert:
```
apt-get install libssl-dev build-essential g++ libboost-all-dev libsparsehash-dev git-core perl
```
Anschließend der Parser von github geclont:
```
cd /home/$user/btc-kennzahlen/
mkdir parser
cd parser
git clone github.com:znort987/blockparser.git
```
Der Parser erwartet die Blockchain in dem Standart-Ordner /home/$user/.bitcoin. Da die Blockchain in dem Beispiel System aber in dem mynode-Ordner liegt, findet der Parser die Block-Dateien nicht. Dies kann leicht behoben werden, indem in der Datei parser.cpp die Zeile 76 angepasst wird
```
gedit home/$user/btc-kennzahlen/parser/blockparser/parser.cpp
```
 Von
```
static auto kCoinDirName = “.bitcoin”;

```
in
```
static auto kCoinDirName = “mynode/data”;
```
Jetzt kann mit
```
cd blockparser
make
```
der Code für den Parser kompiliert werden. Mit einem Testlauf kann überprüft werden, ob der Parser richtig arbeitet.
```
./parser simple
```

Wenn dies ohne Fehlermeldung durchlief, ist der blockparser richtig installiert und findet die Blockchain auf dem System.



Für die Karten Darstellung wird leaflet inklusive der markercluster Erweiterung genutzt. leaflet wird wird von einem Content Delivery Network (cdn) gezogen. Die markerlcuster Erweiterung wird lokal auf dem System gespeichert. Zu beachten ist, dass die markercluster mit der leaflet Version übereinstimmt. Zu Zeitpunkt der Abgabe ist leaflet 0.7 die stable Version und es existiert ein 1.0rc. Über das cdn wird die 0.7 Version gezogen, markercluster muss entsprechend der Version angepasst sein.

[Markercluster] (https://github.com/Leaflet/Leaflet.markercluster)


Auf dem Beispiel System wurde für die Installation von markercluster wiefolgt vorgegangen:
Die Erweiterung wird mit bower installiert. Um bower auf dem System zu installieren wird sowohl nodejs als auch npm benötigt. Mithilfe von npm kann dann bower installiert werden. 
```
apt-get install npm nodejs
npm install bower -g
```
Anschließend wird über bower markcluster installiert.
```
cd /home/$user/btc-kennzahlen
mkdir js
cd js
bower install leaflet.markercluster
```


Als letzte Komponente für BTC-Kennzahlen wird jetzt noch GeoIP2 inklusive der GeoLite2 City auf dem System installiert.

[GeoIP2] (https://github.com/maxmind/GeoIP2-php)

[GeoLite2 City] (https://dev.maxmind.com/geoip/geoip2/geolite2/)

Auf dem Beispiel System wurde hierzu wiefolgt vorgegangen:
```
cd /home/$user/btc-kennzahlen/php
curl -sS https://getcomposer.org/installer | php
php composer.phar require geoip2/geoip2:~2.0
```

Die Datenbank GeoLite2 City wird von der oben angegebenen Seite in der MaxMind DB binary,zipped Variante herrunter geladen und in dem Ordner 
/home/$user/btc-kennzahlen/php/GeoLite2DB
entpackt. Die Datenbank muss von Zeit zur Zeit aktualisiert werden, sonst kann es zu Fehlern kommen und die Tabelle auf der Seite “Karte” wird nicht mehr angezeigt.

Alle Komponenten auf dem System installiert.

# Vor der ersten Ausführung
Damit alles richtig auf der Webseite angezeigt wird, müssen folgende Befehle ausgeführt werden:
```
cd parser/
cd blockparser/
./parser allbalances  > /home/$user/webserver/parser/allbalances
./parser simple  > /home/$user/webserver/parser/simplestats
```
Diese Befehle nutzen den blockparser, welcher die gesammte blockchain durchläuft und die Ergebnisse in einer Datei speichert. Die allbalances Datei ist mehrere Gigabyte groß. Der Vorgang benötigt etwas Zeit. Je nach System dauert dies unterschiedlich lange.
Der allbalance-Befehl wird benutzt um die Seite “Top 50” und “Verteilung” mit Daten zu versorgen.

Der simplestats-Befehl wird für die “Übersichts”-Seite genutzt und benötigt weniger Zeit und Ressourcen von dem System als der allbalance-Befehl.

btchistory.php: Das php-script “btchistory” wird benutzt um die Blockchain einmal komplett auszulesen und die “Graphen”-Seite mit Informationen zu versorgen.

btcverteilung.php: Das script verarbeitet die allbalance Datei die von dem Blockparser erstellt wird.

Die oben aufgeführten Programme sollten regelmäßig ausgeführt werden um die Daten auf der Webseite aktuell zu halten.

