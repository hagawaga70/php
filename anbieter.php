<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
		<?php

			// GET  modus	1 2 3 4 8 9
			// POST action  0 1 2 3 5 8 9
			// ------------------------------------------------------
			// GET	 modus  7 	Menü->Aktivitaet->Edit/Bearbeiten
			// #POST action 3	Editieren eines Datensatzes
			// ------------------------------------------------------
			// GET  modus  1 	Zeige alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  4   	anbieter.php wird von aktiviteat.php aufgerufen und zeigt alle Aktivitaet, die an einer bestimmten Fahrt teilnehmen 	
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// .GET  modus  8   	anbieter.php wird aktiviteat.php aufgerufen. 
			// #POST action 8	A)	Ein bestehender Aktivitaetdatensatz (ak_id) wird mit der f_id in der Relation "wirdangeboten" eingetragen
			// #POST action 9	B)  Ein neuer Aktivitaetdatensatz wird angelegt. Die ak_id und die f_id werden in der Relation "wirdangeboten" abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// .GET  modus  9   	anbieter.php wird von aktiviteat.php aufgerufen. 
			// #POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------
			// POST action  2 	Der zu editierende Datensatz wird kein zweites Mal angezeigt
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			$schalter 		= ""	;  		// Wird benötigt beim Sortieren der Datensätze nach bestimmten Attributen
			$suchfenster 	= 0		;		// ACHTUNG: Das Suchfenster wurde aus diesem Skript entfernt
			$on				= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Attributen der Relation "Aktivitaet" -----------------------------------------------

			$attributeAktivitaet = [							
    			0	=> "an_name"			,
    			1	=> "strasse"			,
    			2	=> "hausnr"				,
    			3	=> "ort"				,
    			4	=> "plz"				,
    			5	=> "url"				,
    			6	=> "email"				,
    			7	=> "telefon"			,
    			8	=> "ansprechperson"		,
    			9	=> "an_id"				,
			];


			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Übersetzungen der Attribte  der Relation "Aktivitaet"------------------------------->

			$spaltenBezeichnerAktivitaet = [
				"an_name"	   	=>  "Name"					,
				"strasse"	   	=>  "Strasse"				,
				"hausnr"		=>	"Haus-Nr."				,
				"ort"		   	=>	"Ort"					,
				"plz"		   	=>	"PLZ"					,
				"url"		   	=>	"Webseite"				,
				"email"		   	=>	"Email"					,
				"telefon"	   	=>	"Telefon-Nr"			,
				"ansprechperson"=>	"Ansprechperson"		,
				"an_id"		   	=>	"Fahrt/en"				,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// -----------------------------------------------------------------------------------------------------
			// ---> Datenbankanbindung  ------------------------------------------------------------------------

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// -----------------------------------------------------------------------------------------------------
			// ---> Hinzufügen eines Datensatzes ---------------------------------------------------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 9){
				$result= pg_query($db,"SELECT nextval ('anbieterSeq')");   	// Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){						// |
					$anbieterSequenceNr = $row['nextval'];					// |
				}
				$errorSwitch=true;	

					if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
					}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																	// eine unvollständige Veränderung der Daten rückgängig gemacht werden

							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							$errorSwitch = false;
					}
					$insertAnbieter ="
					INSERT INTO anbieter (an_id,an_name,strasse,hausnr,ort,plz,url,email,telefon,ansprechperson)
					VALUES (".
											$anbieterSequenceNr 			.",'"	.
											$_POST["an_name"]				."','"	.
											$_POST["strasse"]				."',"	.
											$_POST["hausnr"	]				.",'"	.
											$_POST["ort"	]				."',"	.
											$_POST["plz"	]				.",'"	.
											$_POST["url"	]				."','"	.
											$_POST["email"	]				."','"	.
											$_POST["telefon"] 				."','"	.
											$_POST["ansprechperson"]		."');";
				


				
					if ($errorSwitch && pg_query($db,$insertAnbieter)) {
					}else {

							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							print_r($anbietrUpdate  );					//Eine Fehlermeldung wird im Browser angezeigt 
							$errorSwitch = false;
					}
					$anbietrUpdate = 	" 	UPDATE 	aktivitaet 
									SET 	anbietr = ". $anbieterSequenceNr ."
									WHERE	ak_id 	= " .$_POST['ak_id']. ";"
					;


					
					if ($errorSwitch && pg_query($db,$anbietrUpdate)) {

							if (pg_query($db,"COMMIT;")) {
							}else {

									print_r(pg_last_error($db));					//Eine Fehlermeldung wird im Browser angezeigt 
									print_r($deleteAnbieter );						//Eine Fehlermeldung wird im Browser angezeigt 

									if (pg_query($db,"ROllBACK;")) {
									}else {

											print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
									}
							}
					}else{
							if (pg_query($db,"ROllBACK;")) {
							}else {
									print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							}
					}



				if (pg_query($db,$insert)) {
				}else {

						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($insert);							//Eine Fehlermeldung wird im Browser angezeigt 
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// -----------------------------------------------------------------------------------------------------
			// --> Löchen der anbietr ID in aktivitaet und ggf. löschen de Datensatzes in der Relation Anbieter. 
			//     Der Datensatz wird nur gelöscht, wenn in "aktivitaet" die anbietr ID kein zweites Mal vorhanden ist. 

				if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

						$errorSwitch=true;	

						if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
						}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																		// eine unvollständige Veränderung der Daten rückgängig gemacht werden

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}

						$nullUpdate = 	" 	UPDATE 	aktivitaet 
											SET 	anbietr = NULL
											WHERE	ak_id 	= " .$_POST['ak_id']. ";"
								;


						if ($errorSwitch && pg_query($db,$nullUpdate)) {
						}else {

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								print_r($nullUpdate  );					//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}


						$deleteAnbieter = "	DELETE FROM anbieter 
								WHERE	an_id=".$_POST['loeschen']."
								AND		0 = (	SELECT 	count(*) 
												FROM	aktivitaet
												WHERE	anbietr = ".$_POST['loeschen']."); "
								;



						if ($errorSwitch && pg_query($db,$deleteAnbieter)) {

								if (pg_query($db,"COMMIT;")) {
								}else {

										print_r(pg_last_error($db));					//Eine Fehlermeldung wird im Browser angezeigt 
										print_r($deleteAnbieter );						//Eine Fehlermeldung wird im Browser angezeigt 

										if (pg_query($db,"ROllBACK;")) {
										}else {

												print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
										}
								}
						}else{
								if (pg_query($db,"ROllBACK;")) {
								}else {
										print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								}
						}


				}	

				// -----------------------------------------------------------------------------------------------------
				// ---> Verbindet eine Aktivitaet mit einem bestehenden Datensatz der Relation anbieter---------------------

				if(	array_key_exists('action',$_POST) && $_POST["action"] == 8){

						$updateAnbietrAktivitaet 	= 	" 	UPDATE 	aktivitaet 
															SET 	anbietr = " .$_POST['auswaehlen']."
															WHERE	ak_id 	= " .$_POST['ak_id']. ";"
								;
																													
						if (pg_query($db,$updateAnbietrAktivitaet)) {		// Ausführten des Update-Statements 
						}else {
								print_r( "Data entry unsuccessful. ")	;	// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
								print_r(pg_last_error($db))				;   // |
						}

				}			
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Editieren/Ändern eines Datensatzes ------------------------------------------------------------------------
				if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		// Ändern eines Datensatzes
						$attributeUpdate=""	;											// Attribut-Zeichenkette für das Update-Statement
						$valuesUpdate=""	;											// Value-Zeichenkette für das Update-Statement					
						foreach ($attributeAktivitaet as $key => $val) {

							$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

								if ($key == 9){													// Bei key == 9 keine Einträge, das die ID nicht verändert wird
								}elseif ($key <= 7){
										$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribute  kommasepariert		
										$valuesUpdate 		= $valuesUpdate . "'" . $_POST[$val]		."'".		","	;	// Values kommasepariert
								}elseif($key==8){
										$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
										$valuesUpdate 		= $valuesUpdate . "'". $_POST[$val]		."'"			;	// |
								}
						}



						$update = "	UPDATE 	anbieter 									
								SET 	(".$attributeUpdate.") 
								= 		(".$valuesUpdate .")
								WHERE	an_id=" .$_POST['an_id']. "
								;"	;											// Zusammenstellen des Update-Statement	
								$note = pg_query($db,$update)	;							// Ausführen des UPDATE-Statement	
						
						if (pg_query($db,$update)) {
						}else {
								print_r( "Data entry unsuccessful. ");					// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
								print_r(pg_last_error($db)); 							// |
								print_r($update);
								print_r($_GET);
								print_r($_POST);
						}
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


				// ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
				// MODUS 1 --------------------------------------------------------------------------------------------------------------------------------
				if 		(	(array_key_exists(	'modus'	,$_GET) 	&& $_GET['modus'] 		== 1) || 	// Aufruf vom Menue->Aktivitaeten->Zeige 
							(array_key_exists(	'modus'	,$_GET) 	&& $_GET['modus'] 		== 7) ||	// Aufruf vom Menue->Aktivitaeten->Edit 
							(array_key_exists(	'action',$_POST) 	&& $_POST['action'] 	== 3) ||	// Ner
							(array_key_exists(	'action',$_POST) 	&& $_POST['action'] 	== 9)
						){	// Zeige alle Datensätze

						$where = "";								

						// MODUS 2 --------------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 2){	// Öffne Suchfenster und zeige alle Datensäte

						$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
						$where = 'WHERE ak.anbietr = an.an_id';								

						// MODUS 3 --------------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 3){	// Öffne Suchfenster und zeigt die selektierten Datensätze

						$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
						if (is_numeric($_POST["ak_id_input"])){						// Anführungsstriche werden entfernt
								$ak_id = $_POST["ak_id_input"]						;	// ???
						}else{
								$ak_id = $_POST["ak_id"]								;
						}
						$where = 'WHERE ak_id'.$_POST["ak_id_operator"] . $ak_id	;	// Erstellen des WHERE-Clause -> WHERE ak_id [>|>=|= usw]


						// MODUS 4 --------------------------------------------------------------------------------------------------------------------------------
				}elseif ( 	array_key_exists('modus'	, $_GET	) 	&& $_GET[	'modus'	] 	== 4){

							$where= 'WHERE an_id IN (SELECT anbietr FROM aktivitaet WHERE ak_id =' .$_GET['ak_id'].')' ;	// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


 
				}elseif ( 	(array_key_exists('action'	, $_POST) 	&& $_POST[	"action"] 	== 8) ||	// SELECT: Verbinde bestehenden ak_id mit f_id
							(array_key_exists('action'	, $_POST) 	&& $_POST[	'action'] 	== 1) 		// DELETE		

						){					//	anbieter.php wurde vom aktiviteat.php
											//  aufgerufen. anbieter.php zeigt jetzt nur die Aktivitaeten zur
											//  übergebenen f_id an. 

							if(		array_key_exists('f_id'   , $_POST )) {
								$f_id = $_POST["f_id"];
							}elseif(	array_key_exists('f_id'   , $_GET ))		{ 
								$f_id = $_GET["f_id"];
							}	

							$on = 'ON (ak.anbietr = an.an_id) WHERE
								ak.ak_id IN (
												SELECT 		ak_id 
												FROM 	 	wirdangeboten	
												WHERE		f_id ='. $f_id .'
											)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


						// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 8){		//	Das Skript anbieter.php wurde vom Skript  aktiviteat.php
						//  aufgerufen. anbieter.php zeigt jetzt nur die Aktivitaeten zur
						//  übergebenen f_id an. 

			/*			$suchfenster = 0									;
						$on = 'ON (ak.anbietr = an.an_id) WHERE 
								ak.ak_id NOT IN (
												SELECT 		ak_id 
												FROM 		wirdangeboten
												WHERE		f_id ='. $_GET["f_id"].'
												)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
			*/

						// MODUS 9 DELETE: Alle zu einer f_id gehörenden Datensätze werden angezeigt---------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 9){		//	Das Skript anbieter.php wurde vom Skript aktiviteat.php 
						//  aufgerufen. anbieter.php zeigt jetzt nur die Aktivitaet an
						//  die zur übergebenen f_id gehören

						$suchfenster = 0									;
						$where = 'WHERE an_id IN (
										SELECT 		anbietr 
										FROM 		aktivitaet
										WHERE		ak_id ='. $_GET["ak_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


						// ACTION 2 UPDATE --------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('action',$_POST) && $_POST['action'] == 2){ // Der zu editieren Datensatz wird kein zweites Mal angezeigt

						$suchfenster = 0									;
						$where = 'WHERE an_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
				if(array_key_exists('sort',$_GET)){
						$schalter = $_GET['sort'];
				}else{
						$schalter="";
				}


				if ($schalter != ""){
						$result = pg_query($db,
										"	SELECT 	an_id,an_name,strasse,hausnr,ort,plz,url,email,telefon,ansprechperson
											FROM 	anbieter 
											ORDER BY ". $schalter . "
												;
												");
				}else{
						$Spuffer = 
										"	SELECT 	an_id,an_name,strasse,hausnr,ort,plz,url,email,telefon,ansprechperson
											FROM 	anbieter " . $where . ";";
						$result = pg_query($db,$Spuffer);

				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// Hier stand der SearchBlock siehe Datei SeachFragment.php


				echo'	<div id="rahmen_3">';
				
						if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
							echo '<form name="delete" action="anbieter.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
							echo	'<fieldset>'													;
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
							echo '<form name="edit" action="anbieter.php" method="POST" >'		;
							echo	'<fieldset>'													; 	// Das fieldset wird für den Edit-Button benötigt
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
									array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
							echo '<form name="edit" action="anbieter.php" method="POST" >'		;
							echo	'<fieldset>'													;	 	
						}

						$SpeicherDefaultWerte= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)
							"an_name"	   	=>  ""		,
							"strasse"	   	=>  ""		,
							"hausnr"		=>	""		,
							"ort"		   	=>	""		,
							"plz"		   	=>	""		,
							"url"		   	=>	""		,
							"email"		   	=>	""		,
							"telefon"	   	=>	""		,
							"ansprechperson"=>	""		,
							"an_id"		   	=>	""				
						];


						echo "<table>";

								// ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
								echo "<tr>";

									foreach ($spaltenBezeichnerAktivitaet as $key => $value)	{			// Spaltenkopfbezeichner mit und ohne Link
										if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze

												echo '<th class="grau">'. $value ."</th>";					// |

										}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |

												echo '<th class="grau">'. $value ."</th>";					// |

										}else{																// |
												echo '<th class="grau" >' . '<a href="anbieter.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}
									}
								
									if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Anfügen des Delete-Button

										echo 	'<td class="rot">	
														<button type="submit" name="action" value="1">DELETE</button>
														<input id="ak_id" name="ak_id" type="hidden" value="'. $_GET['ak_id']. '">
												</td>'	;

									}elseif(array_key_exists('modus',$_GET) && $_GET['modus'] ==7 ||
											array_key_exists('modus',$_GET) && $_GET['modus'] ==4 ) {		// Anfügen des Edit-Button

										echo '<td class="rot">
														<button type="submit" name="action" value="2">EDIT</button>
											 </td>'	;

									}elseif(array_key_exists('modus',$_GET) && $_GET['modus'] ==8) {		// Anfügen des Edit-Button

										echo '<td class="rot"><button type="submit" name="action" value="8">SELECT</button></td>'	;
									}		
								echo "</tr>";
								// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

							if(	(array_key_exists('modus',$_GET) 	&& $_GET['modus'] 		== 8 ) ||		// vormals 5 ADD
								(array_key_exists('action',$_POST) 	&& $_POST["action"] 	== 2 )			// Edit
							  )  {

									if( array_key_exists('action',$_POST) 	&& $_POST["action"] == 2){			// Edit
										$defaultValuesEdit = pg_query($db,
																"	SELECT 		an_id,an_name,strasse,hausnr,ort,plz,url,email,telefon,ansprechperson
																	FROM 		anbieter  
																	WHERE		an_id=" . $_POST['editieren']. "		
																	;
															");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

										$row=pg_fetch_assoc($defaultValuesEdit);

										foreach ($SpeicherDefaultWerte as $key => $val) { 		
											
												$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

									}
								}
								echo '<tr>'																		;
								echo 	'<form name="insert" action="anbieter.php" method="POST" >'				;

																	$formInputSize = [					// 
										"an_name"	   	=> 10 		,
										"strasse"	   	=> 10 		,
										"hausnr"		=>	3		,
										"ort"		   	=> 10		,
										"plz"		   	=> 	6		,
										"url"		   	=>	20		,
										"email"		   	=>	15		,
										"telefon"	   	=>	10		,
										"ansprechperson"=>	15		,
									];
												
									foreach($attributeAktivitaet as $key => $value) {
										if($value == "an_id"){
											if(	(array_key_exists(	'modus',	$_GET) 		&& $_GET['modus'] 		== 8) ||
												(array_key_exists(	'action',	$_POST) 	&& $_POST["action"] 	== 2)){						
											}else{
												echo 		'<td	class= "gelb">'				;
												echo 		'</td>'								;
											}
										}else{

											echo 		'<td	class= "gelb">'											;
											echo 			'<input type="text" name="'					.
																	$attributeAktivitaet[ $key ]		.	
																	'" size="'.$formInputSize[$value]	.
																	'" value="' 						.
																	$SpeicherDefaultWerte[ $value ]	. 
																	'"/>'												; 
											echo 		'</td>'															; 
										}
									} // -------------------------------------------------------------------------
									echo		'<td	class= "gelb">'												;

									// ---> Wählen des passenden Submit-Button [ADD|Update] ---------------------------------------------------------------------
									if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// ADD
										echo 			'<button type="submit" name="action" value="9">ADD</button>	'					; 	// Einfügen des ADD
									}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){						
										echo 			'<button type="submit" name="action" value="3">Update</button>	'				;	// Update-Button
									}
									//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


									// ---> Versteckte Übergabe. Bei ADD -> f_i bei Update -> ak_id   -------------------------------------------------------------
									if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
										echo 			'<input id="ak_id" name="ak_id" type="hidden" value="'. $_GET['ak_id']. '">'						; // Versteckt	
									}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){											// ODER
										echo 			'<input id="an_id" name="an_id" type="hidden" value="'. $SpeicherDefaultWerte['an_id'] . '">'; // Versteckt	
									}
									//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
									

										echo 		'</td>'		;


										echo 	'</form>'															;	
										echo '</tr>'																;
							}

							// ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
						
							while($row=pg_fetch_assoc($result)){					// Anzeigen der Datensätze
								echo "<tr>";
									foreach ($attributeAktivitaet as $value)	{
										if($value == 'an_id') {						// Zum Aufrufen anderer Skripte wird die ak_id benötigt
											$anbieterID = $row[ $value ]; 
										}
										
										if( $value == 'an_id'){
											echo '<td class="hellgrau">' . '<a href="aktivitaet.php?modus=12&anbietr='.$anbieterID.'">&#x1f441;</a></td>'; // Link
										}else{
											echo "<td>" . $row[ $value] . "</td>";
										}
							
										
									}
									if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																									// von zu löschenden  tuple
											echo '<td class="rot"><input type="radio" id="'. $anbieterID .'" name="loeschen" value="'. $anbieterID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
												array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $anbieterID .'" name="editieren" value="'. $anbieterID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $anbieterID .'" name="auswaehlen" value="'. $anbieterID .'">'	;
									}
								echo "</tr>";
							}
							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						// 14 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
						echo "</table>"	;
					echo '</fieldset>'	;
				echo '</form>'			;		
					?>
					</div>
				</body>
			</html>

