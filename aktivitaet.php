<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
		<?php

			// GET  modus	1 2 3 4 5 8 9
			// POST action  0 1 2 3 5 8 9
			// ------------------------------------------------------
			// POST action 0  	Hinzufügen eines Datensatzes
			// GET  modus  5	Hinzufügen eines Datensatzes
			// ------------------------------------------------------
			// POST action 3	Editieren eines Datensatzes
			// ------------------------------------------------------
			// GET  modus  1 	Zeige alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  4   	aktivitaet.php wird von fahrt.php aufgerufen und zeigt alle Aktivitaet, die an einer bestimmten Fahrt teilnehmen 	
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// .GET  modus  8   	aktivitaet.php wird fahrt.php aufgerufen. 
			// .POST action 8	A)	Ein bestehender Aktivitaetdatensatz (ak_id) wird mit der f_id in der Relation begleitet eingetragen
			// .POST action 9	B)  Ein neuer Aktivitaetdatensatz wird angelegt. Die ak_id und die f_id werden in der Relation begleitet abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// .GET  modus  9   	aktivitaet.php wird von fahrt.php aufgerufen. 
			// .POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------
			// POST action  2 	Der zu editierende Datensatz wird kein zweites Mal angezeigt
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			$schalter 		= ""	;  		// Wird benötigt beim Sortieren der Datensätze nach bestimmten Attributen
			$suchfenster 	= 0		;		// ACHTUNG: Das Suchfenster wurde aus diesem Skript entfernt
			$where			= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Attributen der Relation "Aktivitaet" -----------------------------------------------

			$attributeAktivitaet = [							
    			0	=> "an_name"	,
    			1	=> "bewert"		,
    			2	=> "bezng"		,
    			3	=> "art"		,
    			4	=> "katgrie"	,
    			5	=> "fabezg"		,
    			6	=> "ort"		,
    			7	=> "vorstzg"	,
    			8	=> "jahrz1"		,
    			9	=> "jahrz2"		,
    			10	=> "jahrz3"		,
    			11	=> "jahrz4"		,
    			12	=> "mialt"		,
    			13	=> "dauer"		,
    			14	=> "akpreis"	,
    			15	=> "ak_id"		,
			];


			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Übersetzungen der Attribte  der Relation "Aktivitaet"------------------------------->

			$spaltenBezeichnerAktivitaet = [
				"anbietr" 	=>	"Anbieter"		,
				"bewert" 	=>  "Bewertung"		,
				"bezng" 	=>  "Bezeichung"	,
				"art" 		=>	"Art"			,
				"katgrie" 	=>	"Kategorie"		,
				"fabezg" 	=>	"Fachbezug"		,
				"ort" 		=>	"Ort"			,
				"vorstzg" 	=>	"Voraussetzung"	,
				"jahrz1" 	=>	"Frühjahr"		,
				"jahrz2" 	=>	"Sommer"		,
				"jahrz3" 	=>	"Herbst"		,
				"jahrz4" 	=>	"Winter"		,
				"mialt" 	=>	"Mindestalter"	,
				"dauer" 	=>	"Dauer/h"		,
				"akpreis" 	=>	"Preis/€"		,
				"ak_id" 	=>	"Fahrt"			,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// -----------------------------------------------------------------------------------------------------
			// ---> Datenbankanbindung  ------------------------------------------------------------------------

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// -----------------------------------------------------------------------------------------------------
			// ---> Hinzufügen eines Datensatzes und Eintrag in die Relation "begleitet"---------------------------------------------------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 9){

				$result= pg_query($db,"SELECT nextval ('aktivitaetSeq')");   	// Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){						// |
					$aktivitaetSequenceNr = $row['nextval'];					// |
				}
				$mapping =

						"WITH data (ak_id,bewert,bezng,art,katgrie,fabezg,ort,vorstzg,jahrz1,jahrz2,jahrz3,jahrz4,mialt,dauer,akpreis) 
						AS (
								VALUES (".
											$aktivitaetSequenceNr 		.","	.
											$_POST["bewert"	]			.",'"	.
											$_POST["bezng"	]			."','"	.
											$_POST["art"	]			."','"	.
											$_POST["katgrie"]			."','"	.
											$_POST["fabezg"	]			."','"	.
											$_POST["ort"	]			."','"	.
											$_POST["vorstzg"]			."','"	.
											$_POST["jahrz1"	]			."','"	.
											$_POST["jahrz2"	]			."','"	.
											$_POST["jahrz3"	]			."','"	.
											$_POST["jahrz4"	]			."',"	.
											$_POST["mialt"	]			.","	.
											$_POST["dauer"	]			.","	.
											$_POST["akpreis"]			.")

						), 
						ins1 AS (
							INSERT INTO aktivitaet (ak_id,bewert,bezng,art,katgrie,fabezg,ort,vorstzg,jahrz1,jahrz2,jahrz3,jahrz4,mialt,dauer,akpreis)
							SELECT FROM data
							ON CONFLICT DO NOTHING
							Returning ak_id
						)
							INSERT INTO wirdangeboten (ak_id,f_id)
							SELECT 	ak_id, f_id
							FROM 	data
							JOIN ins1 USING (ak_id);";






				if (pg_query($db,$mapping)) {
				}else {
				
					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
					print_r($mapping);							//Eine Fehlermeldung wird im Browser angezeigt 
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// -----------------------------------------------------------------------------------------------------
			// --> Löchen einer Verbindung (Relation: begleitet) zwischen einer Fahrt(Relation: fahrt) und einem Aktivitaet (Relation:Aktivitaet)

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

					$errorSwitch=true;	

					if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
					}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																	// eine unvollständige Veränderung der Daten rückgängig gemacht werden

						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($mapping);							//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}
				
					$deleteBegleitet = 	" 	DELETE FROM wirdangeboten
											WHERE		f_id = " .$_POST['f_id']. "
											AND			ak_id = " .$_POST['loeschen']. ";"
										;


					if ($errorSwitch && pg_query($db,$deleteBegleitet)) {
					}else {
					
						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($deleteBegleitet );					//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}


					$deleteAktivitaet = "	DELETE FROM aktivitaet 
											WHERE	ak_id=".$_POST['loeschen']."
											AND		0 = (	SELECT 	count(*) 
															FROM	wirdangeboten
															WHERE	ak_id = ".$_POST['loeschen']."); "
									;



					if ($errorSwitch && pg_query($db,$deleteAktivitaet)) {

						if (pg_query($db,"COMMIT;")) {
						}else {
					
							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							print_r($deleteAktivitaet );					//Eine Fehlermeldung wird im Browser angezeigt 

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
			// ---> Verbindet eine Fahrt mit einem bestehenden Datensatz der Relation aktivitaet---------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 8){

				$insert = "INSERT INTO wirdangeboten (ak_id,f_id) VALUES (".$_POST['auswaehlen'].",".$_POST['f_id'].")"	;	// Erstellen des gesamten INSERT-Statement

				if (pg_query($db,$insert)) {					// Ausführten des INSERT-Statements 
				}else {
					print_r( "Data entry unsuccessful. ")	;	// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db))				;   // |
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


/*
			// -----------------------------------------------------------------------------------------------------
			// Hinzufügen eines Datensatzes ------------------------------------------------------------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 0){

				$result= pg_query($db,"SELECT nextval ('aktivitaetSeq')");   // Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){					// |
					$aktivitaetSequenceNr = $row['nextval'];					// |
				}														// |


				$attributeInsert="";	//  Die Attribute für das INSERT-Statement werden hier abgespeichert
				$valuesInsert="";		//  Die Werte der einzelnen Attribute werden hier abgespeichert
				foreach ($attributeAktivitaet as $key => $val) {
					if ($key == 0){
						$attributeInsert	= $attributeInsert .$val .							","	;  	// ...., attribute1, attribute2, ...			
						$valuesInsert 		= $valuesInsert .	$aktivitaetSequenceNr .				","	;	// Anhängen der Werte: Hier die neue ID	
					}elseif ($key <= 3){
						$attributeInsert	= $attributeInsert.$val.							","	;	// Anhängen mit , als Separationszeichen
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'".		","	;   // | 
					}elseif($key==4){
						$attributeInsert	= $attributeInsert.$val									;	// Letztes/r Attribut/Wert daher ohne Komma	
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'"			;   // anhängen
					}
				}
				$insert = "INSERT INTO aktivitaet (".$attributeInsert.") VALUES (".$valuesInsert .")"	;	// Erstellen des gesamten INSERT-Statement

				if (pg_query($db,$insert)) {					// Ausführten des INSERT-Statements 
				}else {
					print_r( "Data entry unsuccessful. ")	;	// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db))				;   // |
				}
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
*/

			// Editieren/Ändern eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		//	Ändern eines Datensatzes
				$attributeUpdate=""	;												// Attribut-Zeichenkette für das Update-Statement
				$valuesUpdate=""	;												// Value-Zeichenkette für das Update-Statement					
				foreach ($attributeAktivitaet as $key => $val) {
					$POSTbuffer= $_POST[$val];
					if (	($val == "jahrz1") ||
							($val == "jahrz2") ||
							($val == "jahrz3") ||
							($val == "jahrz4") 
						){
							if (		$POSTbuffer == 'ja'){
								$POSTbuffer = 't'	; 
							}elseif (	$POSTbuffer == 'nein'){
								$POSTbuffer = 'f'	; 
							}else{
								print_r('FEHLER: Es wurde weder "ja" noch nein bei den Jahreszeiten eingegeben!!!');
							}
										}else{
										
											$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray
										}

					if ($key == 0){													// Bei key == 0 keine Einträge, das die ID nicht verändert wird
					}elseif ($key <= 14){
						$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribte  kommasepariert		
						$valuesUpdate 		= $valuesUpdate . "'" . $POSTbuffer		."'".		","	;	// Values kommasepariert
					}elseif($key==15){
						$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
						$valuesUpdate 		= $valuesUpdate . "'". $POSTbuffer		."'"			;	// |
					}
				}



				$update = "	UPDATE 	aktivitaet 									
							SET 	(".$attributeUpdate.") 
							= 		(".$valuesUpdate .")
							WHERE	ak_id=" .$_POST['ak_id']. "
							;"	;											// Zusammenstellen des Update-Statement	
				$note = pg_query($db,$update)	;							// Ausführen des UPDATE-Statement	

				if (pg_query($db,$update)) {
				}else {
					print_r( "Data entry unsuccessful. ");					// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db)); 							// |
				}
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

/*
			//ACHTUNG: Hier wird nur ein Datensatz geliefert, auch wenn der Schüler an mehreren Aktivitaeten teilgenommen hat

			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}elseif (array_key_exists('select', $_GET) && array_key_exists('ak_id',$_GET) ){ // Aufruf von aktivitaet.php durch schueler.php
				$where = "WHERE f.ak_id =".$_GET['ak_id'];
			}
*/
			// --->  Verhinderung von Fehlermeldungen ---------------------------------------------------------

			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			// ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
			// MODUS 1 --------------------------------------------------------------------------------------------------------------------------------
			if 		(	(array_key_exists('modus'	,$_GET) 	&& $_GET['modus'] 		== 1) ||  
						(array_key_exists('modus'	,$_GET) 	&& $_GET['modus'] 		== 7) ||
						(array_key_exists('action'	,$_POST) 	&& $_POST['action'] 	== 3)
					){	// Zeige alle Datensätze

				$where = 'WHERE ak.anbietr = an.an_id';								

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
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 4){	//	Das Skript aktivitaet.php wurde vom Skript aktivitaeten au
															//  aufgerufen. aktivitaet.php zeigt jetzt nur die aktivitaeten zur
															//  übergebenen aktivitaeten_id an. 

				$where = 'WHERE ak.anbietr = an.an_id AND
								ak.ak_id IN (
											SELECT 		ak_id 
											FROM 	 	wirdangeboten	
											WHERE		f_id ='. $_GET["f_id"].'
										)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// MODUS 5 --------------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 5){		// Hinzufügen eines Datensatzes			

				$suchfenster = 0	;						// Es wird kein Suchfenster geöffnet
				$where = ''			;						// Alle Datensätze werden angezeigt

			// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 8){		//	Das Skript aktivitaet.php wurde vom Skript  fahrt.php
															//  aufgerufen. aktivitaet.php zeigt jetzt nur die Aktivitaeten zur
															//  übergebenen f_id an. 

				$suchfenster = 0									;
				$where = 'WHERE ak.anbietr = an.an_id AND 
								ak.ak_id NOT IN (
											SELECT 		ak_id 
											FROM 		wirdangeboten
											WHERE		f_id ='. $_GET["f_id"].'
										)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE

			// MODUS 9 DELETE --------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 9){		//	Das Skript aktivitaet.php wurde vom Skript fahrt.php 
															//  aufgerufen. aktivitaet.php zeigt jetzt nur die Aktivitaet an
															//  die zur übergebenen f_id gehören

				$suchfenster = 0									;
				$where = 'WHERE ak.ak_id  IN (
											SELECT 		ak_id 
											FROM 		wirdangeboten
											WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// ACTION 2 UPDATE --------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('action',$_POST) && $_POST['action'] == 2){ // Der zu editieren Datensatz wird kein zweites Mal angezeigt

				$suchfenster = 0									;
				$where = 'WHERE ak.anbietr = an.an_id AND ak.ak_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------

			if ($schalter != ""){
				if($schalter == "an_name"){
					$result = pg_query($db,
											"	SELECT 				ak.ak_id , 	ak.bewert	, ak.bezng 	, ak.art 	, ak.katgrie,
																	ak.fabezg,	ak.ort		, ak.vorstzg, ak.jahrz1	, ak.jahrz2	,
																	ak.jahrz3,	ak.jahrz4	, ak.mialt	, ak.dauer	, ak.akpreis, 
																	an.an_name
												FROM 				aktivitaet ak, anbieter an
												WHERE				ak.anbietr=an.an_id
												ORDER BY			an.an_name
												;
								");
				}else{
					$result = pg_query($db,
											"	SELECT 				ak.ak_id , 	ak.bewert	, ak.bezng 	, ak.art 	, ak.katgrie,
																	ak.fabezg,	ak.ort		, ak.vorstzg, ak.jahrz1	, ak.jahrz2	,
																	ak.jahrz3,	ak.jahrz4	, ak.mialt	, ak.dauer	, ak.akpreis, 
																	an.an_name
												FROM 				aktivitaet ak, anbieter an
												WHERE				ak.anbietr=an.an_id
												ORDER BY			ak." . $schalter . "
												;
								");

				}
			}else{
					$result = pg_query($db,
											"	SELECT 				ak.ak_id , 	ak.bewert	, ak.bezng 	, ak.art 	, ak.katgrie,
																	ak.fabezg,	ak.ort		, ak.vorstzg, ak.jahrz1	, ak.jahrz2	,
																	ak.jahrz3,	ak.jahrz4	, ak.mialt	, ak.dauer	, ak.akpreis, 
																	an.an_name
												FROM 				aktivitaet ak, anbieter an
												" . $where . "
												;
								");

			}


			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// Hier stand der SearchBlock siehe Datei SeachFragment.php


			echo'	<div id="rahmen_3">';
			
					if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
						echo '<form name="delete" action="aktivitaet.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
						echo	'<fieldset>'													;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
						echo '<form name="edit" action="aktivitaet.php" method="POST" >'		;
						echo	'<fieldset>'													; 	// Das fieldset wird für den Edit-Button benötigt
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
								array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
						echo '<form name="edit" action="aktivitaet.php" method="POST" >'		;
						echo	'<fieldset>'													;	 	
					}

					$SpeicherDefaultWerte= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)
						"an_name" 	=> ""		 ,
						"bewert" 	=> ""		 ,
						"bezng" 	=> ""		 ,
						"art" 		=> ""		 ,
						"katgrie" 	=> ""		 ,
						"fabezg" 	=> ""		 ,
						"ort" 		=> ""		 ,
						"vorstzg" 	=> ""		 ,
						"jahrz1" 	=> ""		 ,
						"jahrz2" 	=> ""		 ,
						"jahrz3" 	=> ""		 ,
						"jahrz4" 	=> ""		 ,
						"mialt" 	=> ""		 ,
						"dauer" 	=> ""		 ,
						"akpreis" 	=> ""		 ,
						"ak_id" 	=> ""		 ,
					];


					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerAktivitaet as $key => $value)	{				// Spaltenkopfbezeichner mit und ohne Link
									if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze
											echo '<th class="grau">'. $value ."</th>";					// |
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |
											echo '<th class="grau">'. $value ."</th>";					// |
									}else{																// |
											echo '<th class="grau" >' . '<a href="aktivitaet.php?sort='.$key.'">'. $value ."</a></th>";	// |
									}
								}
							
							if(			array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Anfügen des Delete-Button
								echo 	'<td class="rot">	
												<button type="submit" name="action" value="1">DELETE</button>
												<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">
										</td>'	;
							}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7 ||
										array_key_exists('modus',$_GET) && $_GET['modus'] ==4 ) {		// Anfügen des Edit-Button
								echo '<td class="rot">
												<button type="submit" name="action" value="2">EDIT</button>
										</td>'	;

							}elseif(		array_key_exists('modus',$_GET) && $_GET['modus'] ==8) {		// Anfügen des Edit-Button
								echo '<td class="rot"><button type="submit" name="action" value="8">SELECT</button></td>'	;
							}		
							echo "</tr>";
							// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

						if(	(array_key_exists('modus',$_GET) 	&& $_GET['modus'] 		== 8 ) ||		// vormals 5 ADD
							(array_key_exists('action',$_POST) 	&& $_POST["action"] 	== 2)			// Edit
						  )  {

								if( array_key_exists('action',$_POST) 	&& $_POST["action"] == 2){			// Edit
									$defaultValuesEdit = pg_query($db,
															"	SELECT 		ak.ak_id , 	ak.bewert	, ak.bezng 	, ak.art 	, ak.katgrie,
																			ak.fabezg,	ak.ort		, ak.vorstzg, ak.jahrz1	, ak.jahrz2	,
																			ak.jahrz3,	ak.jahrz4	, ak.mialt	, ak.dauer	, ak.akpreis 
																FROM 		aktivitaet ak 
																WHERE		ak.ak_id=" . $_POST['editieren']. "		
																;
														");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

									$row=pg_fetch_assoc($defaultValuesEdit);

									foreach ($SpeicherDefaultWerte as $key => $val) { 		
										if (	($key == "jahrz1") ||
												($key == "jahrz2") ||
												($key == "jahrz3") ||
												($key == "jahrz4") 
											){
												if (		$row[ $key ] == 't'){
													$SpeicherDefaultWerte[$key] = 'ja'; 
												}else{
													$SpeicherDefaultWerte[$key] = 'nein'; 
												}
										}else{
										
											$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray
										}

								}
							}
							echo '<tr>'																		;
							echo 	'<form name="insert" action="aktivitaet.php" method="POST" >'				;
							echo		'<td	class= "gelb">'												;

								// ---> Wählen des passenden Submit-Button [ADD|Update] ---------------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<button type="submit" name="action" value="9">ADD</button>	'					; 	// Einfügen des ADD
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){						
									echo 			'<button type="submit" name="action" value="3">Update</button>	'				;	// Update-Button
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


								// ---> Versteckte Übergabe. Bei ADD -> f_i bei Update -> ak_id   -------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">'						; // Versteckt	
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){											// ODER
									echo 			'<input id="ak_id" name="ak_id" type="hidden" value="'. $SpeicherDefaultWerte['ak_id'] . '">'; // Versteckt	
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
								

									echo 		'</td>'		;

								$formInputSize = [					// 
									"anbietr" 	=>	1		,
									"bewert" 	=>  1		,
									"bezng" 	=>  10		,
									"art" 		=>	1		,
									"katgrie" 	=>	10		,
									"fabezg" 	=>	10		,
									"ort" 		=>	10		,
									"vorstzg" 	=>	1		,
									"jahrz1" 	=>	1		,
									"jahrz2" 	=>	1		,
									"jahrz3" 	=>	1		,
									"jahrz4" 	=>	1		,
									"mialt" 	=>	1		,
									"dauer" 	=>	1		,
									"akpreis" 	=>	1		,
									"ak_id" 	=>	1		,
								];
											
								foreach($attributeAktivitaet as $key => $value) {
									if($value == "an_name"){
									}elseif($value == "ak_id"){
										echo 		'<td	class= "gelb">'				;
										echo 		'</td>'								;
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
									echo 	'</form>'															;	
									echo '</tr>'																;
						}

						// ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
					
						while($row=pg_fetch_assoc($result)){					// Anzeigen der Datensätze
							echo "<tr>";
								foreach ($attributeAktivitaet as $value)	{
									if($value == 'ak_id') {						// Zum Aufrufen anderer Skripte wird die ak_id benötigt
										$aktivitaetID = $row[ $value ]; 
									}
									
									if( $value == 'ak_id'){
										echo '<td class="hellgrau">' . '<a href="fahrt.php?modus=10&ak_id='.$aktivitaetID.'">&#x1f441;</a></td>'; // Link
									}elseif($value == 'jahrz1' || $value == 'jahrz2' ||  $value == 'jahrz3' ||  $value == 'jahrz4' )
										if ($row[ $value ] == 't'){
											echo "<td>ja</td>";
										}else{
											echo "<td>nein</td>";
										}
									else{
										echo "<td>" . $row[ $value] . "</td>";
									}
						
									
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																								// von zu löschenden  tuple
										echo '<td class="rot"><input type="radio" id="'. $aktivitaetID .'" name="loeschen" value="'. $aktivitaetID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
											array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $aktivitaetID .'" name="editieren" value="'. $aktivitaetID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $aktivitaetID .'" name="auswaehlen" value="'. $aktivitaetID .'">'	;
								}
							echo "</tr>";
						}
					// 14 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
					echo "</table>"	;
				echo '</fieldset>'	;
			echo '</form>'			;		
				?>
				</div>
			</body>
		</html>

