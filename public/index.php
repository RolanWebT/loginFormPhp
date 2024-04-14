<?php
#**********************************************************************************#


				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				

				require_once('../include/config.inc.php');
				require_once('../include/form.inc.php');
				require_once('../src/autoload.php');
				

#****************************************************************************************************#


				#**************************************#
				#********** OUTPUT BUFFERING **********#
				#**************************************#
				
				/*
					Output Buffering erstellt auf dem Server einen Speicherbereich, in dem Frontend-Ausgaben 
					gespeichert (und nicht sofort im Frontend ausgegeben) werden, bis der Buffer-Inhalt
					explizit gesendet werden soll.
					
					Hat man beispielsweise Probleme mit der Fehlermeldung
					"Warning: Cannot modify header information - headers already sent by 
					(output started at /some/file.php:12) in /some/file.php on line 23",
					hilft ein Buffering des Header-Versands. Hiermit wird der Header solange nicht gesendet, bis das PHP-Skript
					eine explizite Anweisung dazu findet, bspw. ob_end_flush() ODER automatisch am Ende des Skripts.
					
					Diese Funktion ob_start() aktiviert die Ausgabepufferung. Während die Ausgabepufferung aktiv ist, 
					werden Skriptausgaben (inklusive der Headerinformationen) nicht direkt an den Client 
					weitergegeben, sondern in einem internen Puffer gesammelt.
				*/
				if( ob_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten des Output Bufferings! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
					
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Output Buffering erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\r\n";									
				}


#***********************************************************************************************************************#
					
			
				#************************************#
				#********** VALIDATE LOGIN **********#
				#************************************#
				
				/*
					Für die Fortsetzung der Session muss hier der gleiche Name ausgewählt werden,
					wie beim Login-Vorgang, damit die Seite weiß, welches Cookie sie vom Client auslesen soll
				*/
				session_name("LoginFormular");
				
				
				
				#********** START/CONTINUE SESSION **********#
				if( session_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session <i>'LoginFormular'</i> erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				

if(DEBUG)		echo "<pre class='debug Auth value'>Line <b>" . __LINE__ . "</b> <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG)		print_r($_SESSION);					
if(DEBUG)		echo "</pre>";
				
					
					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#

					
					#********** A) NO VALID LOGIN **********#				
					if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// Fehlerfall | User ist nicht eingeloggt
if(DEBUG)			echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: User ist nicht eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						session_destroy();
						
						$loggedIn = false;

					#********** B) VALID LOGIN **********#
					} else {
						// Erfolgsfall | User ist eingeloggt
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: User ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
						session_regenerate_id(true);
												
						$loggedIn = true;
					
					} // CHECK FOR VALID LOGIN END
					
				} // VALIDATE LOGIN END
				


#**********************************************************************************#


				#****************************************#
				#********* INITIALIZE VARIABLES *********#
				#****************************************#			
				
				$loginError 	= NULL;
				$loginBlocked 	= false;

#**********************************************************************************#

				#********** PREVIEW POST ARRAY **********#

if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";


				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	


				// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
				$userEmail 		= sanitizeString( $_POST['email'] );
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userEmail: $userEmail <i>(" . basename(__FILE__) . ")</i></p>\n";

				$userPassword 	= sanitizeString( $_POST['password'] );
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userPassword: $userPassword <i>(" . basename(__FILE__) . ")</i></p>\n";
				

				// Schritt 3 FORM: ggf. Werte validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					/*
						[x] Validieren der Formularwerte (Feldprüfungen)
						[ ] Vorbelegung der Formularfelder für den Fehlerfall 
						[ ] Abschließende Prüfung, ob das Formular insgesamt fehlerfrei ist
					*/
					$errorUserEmail			= validateEmail( $userEmail );
					$errorUserPassword		= validateInputString($userPassword);
					
					#********** FINAL FORM VALIDATION **********#					
					if( $errorUserEmail !== NULL OR $errorUserPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						$loginError = 'Login Email oder Password falsch!';
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
									
						// Schritt 4 FORM: Daten weiterverarbeiten
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";


						

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
				
						#********** PREVIEW DATABASE OBJECT **********#

if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$obj_database <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)				print_r($obj_database);					
if(DEBUG_V)				echo "</pre>";									


						#********** FETCH USER DATA FROM DB BY EMAIL **********#	
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
						if( isset($_POST["email"]) ) {
							$obj_query_select = $obj_database->buildSelect()->cols(['id', 'username','password', 'failed', 'blocked', 'lastlogin'])->from('user')->where('username', $_POST["email"]);

							$userData = $obj_database->fetchRow($obj_query_select);
							
							#********** PREVIEW USER DATA **********#

if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)				print_r($userData);					
if(DEBUG_V)				echo "</pre>";	

							
			
						#********** 1. VERIFY LOGIN EMAIL **********#
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Email-Adresse... <i>(" . basename(__FILE__) . ")</i></p>\n";
							

							if( empty($userData) ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Email-Adresse: '{$_POST["email"]}' wurde NICHT in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				

								// NEUTRALE Fehlermeldung an den User
								$loginError = 'Login Email oder Password falsch!';	

								#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)					echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
								unset($obj_database);


							} else {
								// Erfolgreich
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Email-Adresse: '{$_POST["email"]}' wurde in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				

								#********* 2. VERIFY BLOCKED STATUS *********#

								if( $userData["blocked"] != 1) {
									// User ist nicht gesperrt
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Der User: '{$_POST["email"]}' ist nicht gesperrt! <i>(" . basename(__FILE__) . ")</i></p>\n";				

									#********** 3. VERIFY PASSWORD **********#

									if ($userData["password"] != $_POST["password"] ) {
										// Fehlerfall: falsches Passwort 
if(DEBUG)							echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt NICHT mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				

										// NEUTRALE Fehlermeldung an den User
										$loginError = 'Login Email oder Password falsch!';
									
										#****** UPDATE USER DATE IN DB ******# 									
										if( ($userData['failed'] >= 0) && ( $userData['failed'] < 2 ) ){
											/* 
												/OR/ && ( $userData['failed'] < 3 )
												then the user would be blocked out in forth failed login attempt.
												Blocked status would be:
												if ( $userData['failed'] == 3 )
											*/
											// Increase failed Login Attempts by one 
											$failed = ++$userData['failed'];
if(DEBUG)								echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: \$failed: $failed <i>(" . basename(__FILE__) . ")</i></p>\n";				

											$obj_query_update = $obj_database->buildUpdate()->table('user')->where('username', $_POST["email"])->set('failed', $failed);
											$obj_database->execute($obj_query_update);

											// Write Report to log.csv
											$currentDate = date("Y-m-d H:i:s");
											$obj_query_log = $obj_database->buildInsert()->table('log')->set('username',$_POST["email"])->set('date', $currentDate)->set('action', $failed ." failed Login");
											$obj_database->execute($obj_query_log);

if(DEBUG)								echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: Failed Login Versuch beim User '{$_POST["email"]}' <i>(" . basename(__FILE__) . ")</i></p>\n";				

										} else if ( $userData['failed'] == 2 ) {
											
											#********* PROCESS BLOCK USER *********#
											/*
												[] Increase 'failed' flag in user.csv by one
												[] 'blocked' Field = 1
												[] write Report to log.csv
												[] Debug Ausgabe
												[] Fehlermeldung an den User
												[] Login Formular deaktivieren
											*/

											// Increase failed Login Attempts by One and Block User
											$failed = ++$userData['failed'];
if(DEBUG)								echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: \$failed: $failed <i>(" . basename(__FILE__) . ")</i></p>\n";				

											$obj_query_update = $obj_database->buildUpdate()->table('user')->where('username', $_POST["email"])->set('failed', $failed);
											$obj_database->execute($obj_query_update);
											
											$obj_query_blocked = $obj_database->buildUpdate()->table('user')->where('username', $_POST["email"])->set('blocked', 1);
											$obj_database->execute($obj_query_blocked);

											// Write Report to log.csv
											$currentDate = date("Y-m-d H:i:s");

											$obj_query_log = $obj_database->buildInsert()->table('log')->set('username',$_POST["email"])->set('date', $currentDate)->set('action', "Benutzer wurder gesperrt wegen zu vieler Fehlversuche beim Login");
											$obj_database->execute($obj_query_log);

if(DEBUG)								echo "<p class='debug class err'><b>Line " . __LINE__ . "</b>: User '{$_POST["email"]}' wird gesperrt!! <i>(" . basename(__FILE__) . ")</i></p>\n";				

											// Fehlermeldung an den User Und Login wird gesperrt
											$loginError = "Sie haben 3 Mal das Passwort flasch eingegeben. Das Login ist leider gesperrt! <br> Bitte kontaktieren Sie uns unter: <i><a href='mailto:service@mail.com'>service@mail.com</a></i> um dieses Problem zu beheben";

											// Deactivieren den Login Formular 
											$loginBlocked = true;

										}
										
										#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)							echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
										unset($obj_database);

									} else {
										// Erfolgsfall: Passwort ist korrekt
if(DEBUG)							echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Passwort stimmt mit DB überein. LOGIN OK. <i>(" . basename(__FILE__) . ")</i></p>\n";				

										
										#********** 3. PROCESS LOGIN **********#
if(DEBUG)							echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Login wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";

										#****** SAVE NEW VALUES to DB ******#
										/*
											[] write current DateTime 
											[] Reset failed Value
											[] Report to log.csv
										*/

										$currentLogin = date("Y-m-d H:i:s");

										$obj_query_lastlogin = $obj_database->buildUpdate()->table('user')->where('username', $_POST["email"])->set('lastlogin', $currentLogin);
										$obj_database->execute($obj_query_lastlogin);

										$obj_query_reset = $obj_database->buildUpdate()->table('user')->where('username', $_POST["email"])->set('failed', 0);
										$obj_database->execute($obj_query_reset);

										// Write Report to log.csv

										$obj_query_log = $obj_database->buildInsert()->table('log')->set('username',$_POST["email"])->set('date', $currentLogin)->set('action', "Erfolgreich Login");
										$obj_database->execute($obj_query_log);

										#********** START SESSION **********#
										if( !session_start()) {
											// Fehlerfall
if(DEBUG)								echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
											$loginError = 'Der Loginvorgang konnte nicht durchgeführt werden!<br>
																Bitte überprüfen Sie die Sicherheitseinstellungen Ihres Browsers und 
																aktivieren Sie die Annahme von Cookies für diese Seite.';
											
										} else {
											// Erfolgsfall
if(DEBUG)								echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				

										
											#********** SAVE USER DATA INTO SESSION FILE **********#
if(DEBUG)								echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Schreibe Userdaten in Session... <i>(" . basename(__FILE__) . ")</i></p>";

											$_SESSION['IPAddress']		= $_SERVER['REMOTE_ADDR'];
											$_SESSION['ID']				= $userData['id'];
											$_SESSION['userName']		= $userData['username'];
											$_SESSION['password']		= $userData['password'];
											$_SESSION['blocked']		= $userData['blocked'];
											$_SESSION['lastLogin']		= $userData['lastlogin'];

if(DEBUG_V)								echo "<pre class='debug Auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)								print_r($_SESSION);					
if(DEBUG_V)								echo "</pre>";

										// User ist eingeloggt
if(DEBUG)								echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: User: <i>{$_SESSION['userName']} </i> ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
										$loggedIn = true;
									
									
										#********** REDIRECT TO DASHBOARD PAGE **********#
										header('LOCATION: welcome.php');

										#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)							echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
										unset($obj_database);

										} // 4. PROCESS LOGIN END
									} // 3. VERIFY PASSWORD END

								} else {
									// User ist gesperrt
if(DEBUG)							echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Der User: '{$_POST["email"]}' ist gesperrt! <i>(" . basename(__FILE__) . ")</i></p>\n";				

									// Fehlermeldung an den User Und Login wird gesperrt
									$loginError = "Dieser User ist leider gesperrt! <br> Bitte kontaktieren Sie uns unter: <i><a href='mailto:service@mail.com'>service@mail.com</a></i> um dieses Problem zu beheben";

									// Deactivieren den Login Formular 
									$loginBlocked = true;									
								} // 2. VERIFY BLOCKED STATUS  END
								
							} // 1. VERIFY LOGIN EMAIL END 							

						} // FETCH USER DATA FROM DB END

					} // FINAL FORM VALIDATION END

				} // PROCESS FORM LOGIN END


				#****************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
		<title>Antaui Login</title>
		<link rel="stylesheet" href="./assets/css/theme.css">
		<link rel="stylesheet" href="./assets/css/debug.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	</head>
	
	<body>	
		<h1 class="center">
			<a href="https://github.com/antaui/Testaufgabe" target="_blank">
				<img src="./assets/img/antaui-logo.jpeg">
			</a>
		</h1>
		<div class="login">
		<!-- -------- LOGIN FORM START -------- -->
			<form method="POST">
				<input type="hidden" name="formLogin">
					<span id="result" ></span><br>
				<?php if($loginError): ?>
					<span class="error"><?= $loginError ?></span><br>
				<?php endif ?>
	
				<?php if($loginBlocked): ?>
					<label for="email">E-Mail-Adresse</label>
					<input type="email" id="email" name="email" oninvalid="this.setCustomValidity('Please Enter your email')" size="20" disabled="true">
					<label for="user_pass">Password</label>
					<input type="password" id="user_pass" name="password" size="20" disabled="true">
					<input type="submit" id="submit" class="submit disabled" value="Login" disabled="true">
				<?php else: ?>
					<label for="email">E-Mail-Adresse</label>
					<input type="email" id="email" name="email" size="20" required>
					<label for="user_pass">Password</label>
					<input type="password" id="user_pass" name="password" size="20" spellcheck="false" required>
					<input type="submit" id="submit" class="submit" value="Login">
				<?php endif ?>

			</form>
		<!-- -------- LOGIN FORM END -------- -->
		</div>
	<script src="./assets/js/functions.js"></script>
	</body>
</html>

		