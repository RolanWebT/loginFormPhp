<?php
#**********************************************************************************#


				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#


				require_once ('../include/config.inc.php');
				require_once ('../include/form.inc.php');
				require_once ('../include/date.inc.php');
				require_once ('../src/autoload.php');


#**********************************************************************************#				



				#****************************************#
				#********** SECURE PAGE ACCESS **********#^
				#****************************************#				

				session_name('LoginFormular');

				#********** START|CONTINUE SESSION	**********#
				if (session_start() === false) {
					// Fehlerfall
if (DEBUG)		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";

				} else {
					// Erfolgsfall
if (DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";


if (DEBUG) 		echo "<pre class='debug Auth value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";
if (DEBUG)		print_r($_SESSION);
if (DEBUG)		echo "</pre>";



					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#

					if (isset($_SESSION['ID']) === false or $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR']) {
					// Fehlerfall
if (DEBUG)			echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";



					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen	
					session_destroy();

					// 2. User auf öffentliche Seite umleiten
					header('LOCATION: ./');


					// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
					exit();

					#********** ALLOW PAGE ACCESS **********#				
				} else {
					// Erfolgsfall
if (DEBUG)			echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";

					session_regenerate_id(true);

					$userID = $_SESSION['ID'];


					$lastLogin = date_create($_SESSION['lastLogin']);
					$currentLogin = date_create(date("Y-m-d H:i:s"));

		
					$loginDiff = calc_date_diff($lastLogin, $currentLogin);

					/**** in Days 
					$loginDiff = date_diff($lastLogin, $currentLogin)->format('%a days');			  
					*/


					$loggedIn = true;
				}
			}

#**********************************************************************************#


				#****************************************#
				#********* INITIALIZE VARIABLES *********#
				#****************************************#			
				
				$errorPassword 	= NULL;
				$dbSuccess 		= NULL;
				$showHistory 	= false;


#**********************************************************************************#


				#***************************************************#
				#********** FETCH USER DATA FROM DATABASE **********#
				#***************************************************#

if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
			

				// Schritt 1 DB: DB-Verbindung herstellen

				$str_driver = 'csv';
				$str_host = str_replace('\\', '/', realpath(__DIR__.'/../data'));
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DB Pfad ist: " . $str_host . ". <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
				$str_dsn = sprintf('%s:/%s', $str_driver, $str_host);
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DSN ist: " . $str_dsn . ". <i>(" . basename(__FILE__) . ")</i></p>\n";										

				$obj_database = \Test\Database::factory($str_dsn);
						
if(DEBUG)		echo "<p class='debug db ok'><b>Line " . __LINE__ . "</b>: Connection zum DB ist erfolgreich. <i>(" . basename(__FILE__) . ")</i></p>\n";				
				

				#********** FETCH USER DATA FROM DB BY USER ID **********#	
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetch Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
				$obj_query_select = $obj_database->buildSelect()->cols(['id', 'username','password', 'failed', 'blocked', 'lastlogin'])->from('user')->where('id', $userID);
				$userData = $obj_database->fetchRow($obj_query_select);

				
				#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)	echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($obj_database);				
				

				#********** PREVIEW USER DATA **********#

if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($userData);					
if(DEBUG_V)			echo "</pre>";	

				$userName 			= $userData['username'];
				$userPassword 		= $userData['password'];


#**********************************************************************************#

				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#

				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if (isset($_GET['action'])) {
if (DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben... <i>(" . basename(__FILE__) . ")</i></p>";


					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if (DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
$action = sanitizeString($_GET['action']);

if (DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action = $action <i>(" . basename(__FILE__) . ")</i></p>";

					// Schritt 3 URL: ggf. Verzweigung

					#********** LOGOUT **********#
					if ($action === 'logout') {
if (DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: 'Logout' wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>";

						session_destroy();
						header("Location: index.php");
						exit();

					} else if ( $action === 'showActivities') {
					
					$showHistory = true;

					#******* FETCH ALL ACIIVITIES BY A USER FROM LOG *******#

if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 1 DB: DB-Verbindung herstellen

					$str_driver = 'csv';
					$str_host = str_replace('\\', '/', realpath(__DIR__.'/../data'));
if(DEBUG)			echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DB Pfad ist: " . $str_host . ". <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
					$str_dsn = sprintf('%s:/%s', $str_driver, $str_host);
if(DEBUG)			echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DSN ist: " . $str_dsn . ". <i>(" . basename(__FILE__) . ")</i></p>\n";										

					$obj_database = \Test\Database::factory($str_dsn);
						
if(DEBUG)			echo "<p class='debug db ok'><b>Line " . __LINE__ . "</b>: Connection zum DB ist erfolgreich. <i>(" . basename(__FILE__) . ")</i></p>\n";				
				

					#********** FETCH USER DATA FROM DB BY USER NAME **********#	
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Fetch Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$obj_query_select_log = $obj_database->buildSelect()->cols(['date','username','action'])->from('log')->where('username', $userName);
					$userLogData = $obj_database->fetchAssoc($obj_query_select_log);

						

					#********** PREVIEW USER DATA **********#

if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userLogData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($userLogData);					
if(DEBUG_V)			echo "</pre>";

					#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
					unset($obj_database);				
		
					// FETCH ALL ACIIVITIES BY A USER FROM LOG END

					} else if ($action === 'hideActivities') {
						$showHistory = false;
					}
						

				} // PROCESS URL PARAMETERS END


#***************************************************************************************#


				#*************************************************#
				#******* PROCESS FORM UPDATE USER PASSWORD *******#
				#*************************************************#
				
				#********** PREVIEW POST ARRAY **********#

if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";

				#****************************************#

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formEditPassword']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'Edit User Password' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
					
					
					// Schritt 2 FORM: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
					$password 			= sanitizeString( $_POST['f1'] );
					$passwordCheck 		= sanitizeString( $_POST['f2'] );
					$passwordOrigin 	= sanitizeString( $_POST['f3'] );

					/*
						DEBUGGING:
						1. Ist der Variablenname korrekt geschrieben?
						2. Steht in jeder Variable der korrekte Wert?
					*/	
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$password: $password <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$passwordCheck: $passwordCheck <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$passwordOrigin: $passwordOrigin <i>(" . basename(__FILE__) . ")</i></p>\n";



					#*********************************************#
					#********** PROCESS PASSWORD CHANGE **********#
					#*********************************************#
					
					#********** CHECK IF USER CHANGES PASSWORD **********#
					if( $password === NULL AND $passwordCheck === NULL AND $passwordOrigin === NULL ) {
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Password change is inactive. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
if(DEBUG)			echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Password change is active. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						#********** 1. CHECK IF NEW PASSWORD MATCHES REQUIREMENTS **********#
						$errorPassword = validateInputString( $password);
					
						if( $errorPassword !== NULL ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das neue Passwort erfüllt nicht die Mindestanforderungen! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
						} else {
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das neue Passwort erfüllt die Mindestanforderungen. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							

							#********** 2. CHECK IF PASSWORD MATCHES PASSWORD CHECK **********#
							if( $password !== $passwordCheck ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Passworte stimmen nicht überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
								// Fehlermeldung für User
								$errorPassword = 'Die Passworte stimmen nicht überein!';
								
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Passworte stimmen überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
								#******** 3. CHECK IF NEW PASSWORD IS DIFFERENT FROM THE PASSWORD IN DB *****#
								if( $password === $userPassword) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Keine neue Password eingegeben! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
									// Fehlermeldung für User
									$errorPassword = 'Sie haben desselben aktuellen Password eingegeben!';

								 } else {
									
									#********** 4. CHECK IF PASSWORD ORIGIN MATCHES PASSWORD FROM DB **********#
									if( $passwordOrigin !== $userPassword ) {
										// Fehlerfall
if(DEBUG)							echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das alte Passwort stimmt nicht mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
										// Fehlermeldung für User
										$errorPassword = 'Das Bestätigungspasswort ist falsch!';
									
									} else {
										// Erfolgsfall
if(DEBUG)							echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das alte Passwort stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
									} //  4. CHECK IF PASSWORD ORIGIN MATCHES PASSWORD FROM DB END

								 } // 3. CHECK IF NEW PASSWORD IS DIFFERENT FROM THE PASSWORD IN DB END

							} // 2. CHECK IF PASSWORD MATCHES PASSWORD CHECK END
				 
						} // 1. CHECK IF NEW PASSWORD MATCHES REQUIREMENTS END

					} // PROCESS PASSWORD CHANGE END
					#************************************************#

					#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
					if( $errorPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						// Schritt 4 FORM: Weiterverarbeitung der Formularwerte
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Formularwerte werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#		

						// Schritt 1 DB: DB-Verbindung herstellen
						$str_driver = 'csv';
						$str_host = str_replace('\\', '/', realpath(__DIR__.'/../data'));
if(DEBUG)				echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DB Pfad ist: " . $str_host . ". <i>(" . basename(__FILE__) . ")</i></p>\n";						
						
						$str_dsn = sprintf('%s:/%s', $str_driver, $str_host);
if(DEBUG)				echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: DSN ist: " . $str_dsn . ". <i>(" . basename(__FILE__) . ")</i></p>\n";										

						$obj_database = \Test\Database::factory($str_dsn);
						
if(DEBUG)				echo "<p class='debug db ok'><b>Line " . __LINE__ . "</b>: Connection zum DB ist erfolgreich. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						#********** UPDATE USER PASSWORD IN DB **********#	
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Benutzer Passwort in die DB ... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
						$obj_query_update = $obj_database->buildUpdate()->table('user')->where('username', $userName)->set('password', $password);
						$obj_database->execute($obj_query_update);

						// Write Report to log.csv
						$currentDate = date("Y-m-d H:i:s");
						$obj_query_log = $obj_database->buildInsert()->table('log')->set('username',$userName)->set('date', $currentDate)->set('action', "Update password");
						$obj_database->execute($obj_query_log);

if(DEBUG)				echo "<p class='debug class ok'><b>Line " . __LINE__ . "</b>: New Password für User '{$userName}' ist erfolgreich in DB gespeichert<i>(" . basename(__FILE__) . ")</i></p>\n";				

						// Erfolgsmeldung für User
						$dbSuccess = 'Ihre Daten wurden erfolgreich geändert.';

						#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)			echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($obj_database);


					} // FINAL FORM VALIDATION (FIELDS VALIDATION) END

				} // PROCESS FORM UPDATE USER PASSWORD END

#****************************************************************************************************#



?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Welcome Page</title>
	<link rel="stylesheet" href="./assets/css/theme.css">
	<link rel="stylesheet" href="./assets/css/debug.css">
</head>

<body>
<?php if ($loggedIn): ?>
	<!-- ---------- PAGE HEADER START ---------- -->

	<header class="main">
		<p class="normal_text"> Hallo <span class="data"><?= $userName ?></span>, 
		your last login since: <span class="data"><?= $loginDiff ?></span>
		<a class="btn logout" href="?action=logout"> Logout</a></p>
	</header>

	<hr>
	<br>

	<!-- ---------- PAGE HEADER END ---------- -->
	<div class="main">
		<h1 class="center">Welcome Page :) </h1>

		<div class="update">
				<h2> Edit your Password </h2>
				<form  method="POST">
				<input type="hidden" name="formEditPassword">

				<?php if($errorPassword): ?>
					<span class="error"><?= $errorPassword ?></span><br>
				<?php endif ?>
				<?php if($dbSuccess): ?>
					<span class="success"><?= $dbSuccess ?></span>
				<?php endif ?>

				<input type="password" name="f1" size="20" placeholder="New password..." required><br>
				<input type="password" name="f2" placeholder="Rewrite new password..." required><br>
				<br>
				<input type="password" name="f3" placeholder="Old password to confirm..." required>
				
				<br>
				
				<input type="submit" class="btn service" value="Update password">
				</form>
			</div>
			<div class="update">
				<h2>History</h2>
				<a class="btn service" href="?action=showActivities"> Show activities</a>
				<a class="btn service" href="?action=hideActivities"> Hide activities</a>
			</div>
			<?php if($showHistory): ?>
				<?php foreach($userLogData AS $userLogItem): ?>
					<p class="log_text">Am <?= $userLogItem['date'] ?>: <span> <?= $userLogItem['action'] ?> </span></p>
				<?php endforeach ?>
			<?php endif ?>
			<!-- 
			<div class="update">
				<h2>Log File</h2>
				<a class="btn service" href="?action=emptyCash"> Delete your History</a>
			</div>
			-->
		
		</div>
		
		
			
	

	</div>
	
<?php else: ?>
	<div class="main">
		<h1 class="center">Somthing went wrong :( </h1>

		<p class="normal_text"> Please refresh your page or contact our service Section <i><a href='mailto:service@mail.com'>service@mail.com</a></i></p>

	</div>
<?php endif ?>
</body>

</html>