# Aufgabenstellung
Erstelle ein Loginformular, in dem Benutzername im E-Mail-Format und ein Passwort abgefragt werden können.
Implementiere vor dem Absenden eine clientseiteige E-Mail-Formats-Validierung, welche auf eine full-qualified-domain prüft (user@host.domain).
Implementiere eine serverseitige Überprüfung, ob der Login erfolgreich war. Dabei sollen folgende Szenarien mindestens abgedeckt werden:

- ungültige Kombination aus Benutzername und Passwort
- Benutzerdaten sind korrekt, der Login ist aber gesperrt
- Login war erfolgreich

Wurde für einen Benutzer das Passwort 3 Mal falsch eingegeben, sollte der Login gesperrt werden.

War der Login erfolgreich, sollte das erneute Aufrufen der Seite eine Begrüßungsseite darstellen und nicht mehr den Login.
Die Begrüßungsseite soll mindestens folgende Inhalte anzeigen:

- Ansprechen des angemeldeten Benutzers
- Ausgabe der zeitlichen Differenz des letzten Logins zur aktuellen Anmeldung
- einen funktionalen Logout-Button
- Protokoll der Aktionen des aktuell angemeldeten Benutzers im System

Ist der User ausgeloggt, erscheint wieder die Login-Seite.

Jede Benutzer-Interaktion im System soll protokolliert werden. Decke dabei mindestens folgende Szenarien ab:

- Login
- Logout
- Login-Sperre nach 3-maliger Passwort-Falscheingabe

Optisch muss es nicht zu anspruchsvoll sein, es wäre aber schön, wenn es nicht nur schwarzer Text auf weißem Hintergrund ist. 

# Autoloader-Support
Ein Autoloader wird bereitgestellt und unterstützt das Laden der Klassen
``` php public/example.php
require '../src/autoload.php';
```

# Projektstruktur
Der Document-Root für den Webserver ist der Ordner "public".
Die Datenbank liegt im Ordner "data". Es gibt 2 Tabellen:

- user
- log

Das Framework liegt im Ordner "src".

Das Framework darf gern erweitert werden, eine Notwendigkeit besteht aber nicht.

# Database
Es gibt eine rudimentäre dateibasierte Datenbank im CSV Format. 
Da Datenbanktreiber und -layer dynamisch gehalten und austauschbar sind, wird diese per DSN geladen, zum Beispiel: 
``` php public/db.php
$str_driver = 'csv';
$str_host = str_replace('\\', '/', realpath(__DIR__.'/../data'));
$str_dsn = sprintf('%s://%s', $str_driver, $str_host);
$obj_database = \Test\Database::factory($str_dsn);
```
Für das CSV-Database-Layer stehen rudimentäre Abfrage-Optionen bereit, welche ebenso rudimentäre Operationen unterstützen.
Unterstützt wird:

- Select 
- Delete
- Update
- Insert
  
Zum Beispiel: 
``` php public/db.php
$obj_query = $obj_database->buildSelect()->cols(['id', 'username'])->from('user');
print_r($obj_database->fetchAssoc($obj_query));
```
Der Where-Clause unterstützt derzeit ausschließlich den "equal" -Operand. Werte hingegen können schon per Alias gewrapped werden.

# Zu beachtende Standards und Regeln
- PHP >=7.2 <8.0
- PSR-0, PSR-4
- OOP und MVC
- keine verwendung externer Client-Resourcen (CSS, JS Frameworks)
