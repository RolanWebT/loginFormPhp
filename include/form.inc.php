<?php
#**********************************************************************************#


				#******************************#
				#********** FORM INC **********#
				#******************************#


#**********************************************************************************#


				#*************************************#
				#********** SANITIZE STRING **********#
				#*************************************#
				
				/**
				*
				*	Ersetzt potentiell gefährliche Steuerzeichen durch HTML-Entities
				*	Entfernt vor und nach einem String unnötige Whitespaces
				*	Ersetzt Leerstring und reine Whitespaces durch NULL
				*	
				*	@params		String	$value								Die zu bereinigende Zeichenkette
				*	@params		Bool		$convertEmptyStringToNull		Angabe, ob Leerstrings in NULL umgewandelt werden sollen
				*
				*	@return		String|NULL			Die bereinigte Zeichenkette|NULL, falls $value NULL oder '' ist
				*
				*/
				function sanitizeString($value) {
					#********** LOCAL SCOPE START **********#
if(DEBUG_F)		echo "<p class='debug sanitizeString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					/*
						Da in PHP künftig kein Aufruf der PHP-eigenen Funktionen
						mit NULL-Werten erlaubt ist, rufen wir die PHP-Funktionen
						nur auf, wenn $value NICHT NULL ist.
						Für DB-Operationen soll NULL nicht mit Leersteings überschrieben
						werden. Daher wird an dieser Stelle ein Leerstring durch NULL ersetzt.
					*/
					if( $value !== NULL ) {

						/*
							SCHUTZ GEGEN EINSCHLEUSUNG UNERWÜNSCHTEN CODES:
							Damit so etwas nicht passiert: <script>alert("HACK!")</script>
							muss der empfangene String ZWINGEND entschärft werden!
							htmlspecialchars() wandelt potentiell gefährliche Steuerzeichen wie
							< > " & in HTML-Code um (&lt; &gt; &quot; &amp;).
							
							Der Parameter ENT_QUOTES wandelt zusätzlich einfache ' in &apos; um.
							Der Parameter ENT_HTML5 sorgt dafür, dass der generierte HTML-Code HTML5-konform ist.
							
							Der 1. optionale Parameter regelt die zugrundeliegende Zeichencodierung 
							(NULL=Zeichencodierung wird vom Webserver übernommen)
							
							Der 2. optionale Parameter bestimmt die Zeichenkodierung
							
							Der 3. optionale Parameter regelt, ob bereits vorhandene HTML-Entities erneut entschärft werden
							(false=keine doppelte Entschärfung)
						*/
						$value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
						
						/*
							trim() entfernt VOR und NACH einem String (aber nicht mitten drin) 
							sämtliche sog. Whitespaces (Leerzeichen, Tabs, Zeilenumbrüche)
						*/
						$value = trim($value);
						
						/*
							Sollte $value ausschließlich Whitespaces beinhalten, liefert trim() an dieser
							Stelle einen Leerstring zurück. Dieser Leerstring muss wieder in NULL umgewandelt werden.
							
							Auch ein von vornherein übergebener Leerstring sollte an dieser Stelle in NULL
							umgewandelt werden, damit es keine Probleme mit leeren Datenbankfeldern (NULL) gibt, die
							ansonsten durch Leerstrings überschrieben würden.
						*/
						if( $value === '' ) {
							$value = NULL;
						}
						
					}
					
					return $value;
					#********** LOCAL SCOPE END **********#
				}


#**********************************************************************************#


				#*******************************************#
				#********** VALIDATE INPUT STRING **********#
				#*******************************************#

				/**
				*
				*	Prüft einen übergebenen String auf Mindestlänge und Maximallänge sowie optional 
				* 	zusätzlich auf Pflichtangabe.
				*	Generiert Fehlermeldung bei Leerstring, NULL oder ungültiger Länge
				*
				*	@param	NULL|String	$value											Der zu übergebende String
				*	@param	Bool			$mandatory=true								Angabe zu Pflichteingabe
				*	@param	Integer		$maxLength=INPUT_STRING_MAX_LENGTH		Die zu prüfende Maximallänge
				*	@param	Integer		$minLength=INPUT_STRING_MIN_LENGTH		Die zu prüfende Mindestlänge															
				*
				*	@return	String|NULL														Fehlermeldung | ansonsten NULL
				*
				*/
				function validateInputString($value, $mandatory=true, $maxLength=INPUT_STRING_MAX_LENGTH, $minLength=INPUT_STRING_MIN_LENGTH) {
					#********** LOCAL SCOPE START **********#
if(DEBUG_F)		echo "<p class='debug validateInputString'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value', [$minLength | $maxLength], mandatory: $mandatory) <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** MANDATORY CHECK **********#
					/*
						Da ein zu prüfender String nicht zwangsläufig aus einem Formular,
						sondern beispielsweise auch aus einem JSON-Objekt stammen könnte, sollten
						hier auch NULL-Werte mit geprüft werden.
					*/
					if( $mandatory === true AND ($value === '' OR $value === NULL) ) {
						// Fehlerfall
						return "Dies ist ein Pflichtfeld!";
										
					
					#********** MAXIMUM LENGTH CHECK **********#
					/*
						Da die Felder in der Datenbank oftmals eine Längenbegrenzung besitzen,
						die Datenbank aber bei Überschreiten dieser Grenze keine Fehlermeldung
						ausgibt, sondern alles, das über diese Grenze hinausgeht, stillschweigend 
						abschneidet, muss vorher eine Prüfung auf diese Maximallänge durchgeführt 
						werden. Nur so kann dem User auch eine entsprechende Fehlermeldung ausgegeben
						werden.
					*/
					/*
						mb_strlen() erwartet als Datentyp einen String. Wenn (später bei der OOP)
						jedoch ein anderer Datentyp wie Integer oder Float übergeben wird, wirft
						mb_strlen() einen Fehler. Da es ohnehin keinen Sinn maht, einen Zahlenwert
						auf seine Länge (Anzahl der Zeichen) zu prüfen, wird diese Prüfung nur für
						den Datentyp 'String' durchgeführt.
					*/
					} elseif( $value !== NULL AND mb_strlen($value) > $maxLength ) {
						// Fehlerfall
						return "Darf maximal $maxLength Zeichen lang sein!";
					
					
					#********** MINIMUM LENGTH CHECK **********#
					/*
						Es gibt Sonderfälle, bei denen eine Mindestlänge für einen Userinput
						vorgegeben ist, beispielsweise bei der Erstellung von Passwörtern.
						Damit nicht-Pflichtfelder aber auch weiterhin leer sein dürfen, muss
						die Mindestlänge als Standardwert mit 0 vorbelegt sein.
						
						Bei einem optionalen Feldwert, der gleichzeitig eine Mindestlänge
						einhalten muss, darf die Prüfung keine Leersrtings validieren, da 
						diese nie die Mindestlänge erfüllen und somit der Wert nicht mehr 
						optional wäre.
					*/
					/*
						mb_strlen() erwartet als Datentyp einen String. Wenn (später bei der OOP)
						jedoch ein anderer Datentyp wie Integer oder Float übergeben wird, wirft
						mb_strlen() einen Fehler. Da es ohnehin keinen Sinn macht, einen Zahlenwert
						auf seine Länge (Anzahl der Zeichen) zu prüfen, wird diese Prüfung nur für
						den Datentyp 'String' durchgeführt.
					*/
					} elseif( $value !== NULL AND mb_strlen($value) < $minLength ) {
						// Fehlerfall
						return "Muss mindestens $minLength Zeichen lang sein!";
						
					}
					
					return NULL;					
					#********** LOCAL SCOPE END **********#
				}
				

#**********************************************************************************#


				#*******************************************#
				#********** VALIDATE EMAIL FORMAT **********#
				#*******************************************#
				
				/**
				*
				*	Prüft einen übergebenen String auf eine valide Email-Adresse und Pflichtfeld.
				*	Generiert Fehlermeldung bei ungültiger Email-Adresse und Leerstring
				*
				*	@param	String	$value							Der zu übergebende String
				*
				*	@return	String|NULL									Fehlermeldung | ansonsten NULL
				*
				*/
				function validateEmail($value) {
					#********** LOCAL SCOPE START **********#
if(DEBUG_F)		echo "<p class='debug validateEmail'>🌀 <b>Line " . __LINE__ . "</b>: Aufruf " . __FUNCTION__ . "('$value') <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					#********** MANDATORY CHECK **********#
					/*
						Da ein zu prüfender String nicht zwangsläufig aus einem Formular,
						sondern beispielsweise auch aus einem JSON-Objekt stammen könnte, sollten
						hier auch NULL-Werte mit geprüft werden.
					*/
					if( $value === '' OR $value === NULL ) {
						// Fehlerfall
						return "Dies ist ein Pflichtfeld!";
										
					
					#********** VALIDATE EMAIL FORMAT **********#
					} elseif( filter_var($value, FILTER_VALIDATE_EMAIL) === false ) {
						// Fehlerfall
						return "Dies ist keine gültige Email-Adresse!";
						
					} else {
						
						return NULL;
					}
					#********** LOCAL SCOPE END **********#
				}


#**********************************************************************************#

?>