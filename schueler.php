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
			// GET  modus  4   	schueler.php wird von fahrt.php aufgerufen und zeigt alle Schueler, die an einer bestimmten Fahrt teilnehmen 	
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// .GET  modus  8   	schueler.php wird fahrt.php aufgerufen. 
			// .POST action 8	A)	Ein bestehender Schuelerdatensatz (s_id) wird mit der f_id in der Relation nimmtteil eingetragen
			// .POST action 9	B)  Ein neuer Schuelerdatensatz wird angelegt. Die s_id und die f_id werden in der Relation nimmtteil abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// .GET  modus  9   	schueler.php wird von fahrt.php aufgerufen. 
			// .POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------
			// POST action  2 	Der zu editierende Datensatz wird kein zweites Mal angezeigt
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			$schalter 		= ""	;  		// Wird benötigt beim Sortieren der Datensätze nach bestimmten Attributen
			$suchfenster 	= 0		;		// ACHTUNG: Das Suchfenster wurde aus diesem Skript entfernt
			$where			= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Attributen der Relation "Schueler" -----------------------------------------------
			$attributeSchueler = [							
    			0	=> "s_id"		,
    			1	=> "vname"		,
    			3	=> "nname"		,
    			4	=> "gebdat"		,
    			5	=> "m_w"			,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Übersetzungen der Attribte  der Relation "Schueler"------------------------------->

			$spaltenBezeichnerSchueler = [
    			"s_id" 			=> "Fahrt"			,
    			"vname"			=> "Vorname"		,
    			"nname"			=> "Nachname"		,
    			"gebdat"		=> "Geburtstag"		,
    			"m_w"			=> "Geschlecht"		,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// -----------------------------------------------------------------------------------------------------
			// ---> Datenbankanbindung  ------------------------------------------------------------------------

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// -----------------------------------------------------------------------------------------------------
			// ---> Hinzufügen eines Datensatzes und Eintrag in die Relation "nimmtteil"---------------------------------------------------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 9){

				$result= pg_query($db,"SELECT nextval ('schuelerSeq')");   	// Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){						// |
					$schuelerSequenceNr = $row['nextval'];					// |
				}

				$mapping =

						"WITH data (f_id, s_id,vname,nname,gebdat,m_w) 
						AS (
								VALUES (".$_POST["f_id"].",".$schuelerSequenceNr .",'".$_POST["vname"]."','".$_POST["nname"]."','".$_POST["gebdat"]."',".$_POST["m_w"].")
						), 
						ins1 AS (
							INSERT INTO schueler (s_id,vname,nname,gebdat,m_w)
							SELECT s_id,vname,nname,gebdat,m_w FROM data
							ON CONFLICT DO NOTHING
							Returning s_id
						)
							INSERT INTO nimmtteil (s_id,f_id)
							SELECT 	s_id, f_id
							FROM 	data
							JOIN ins1 USING (s_id);";

				if (pg_query($db,$mapping)) {
				}else {
				
					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
					print_r($mapping);				//Eine Fehlermeldung wird im Browser angezeigt 
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// -----------------------------------------------------------------------------------------------------
			// --> Löchen einer Verbindung (Relation: nimmtteil) zwischen einer Fahrt(Relation: fahrt) und einem Schueler (Relation:Schueler)

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

					$errorSwitch=true;	

					if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
					}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																	// eine unvollständige Veränderung der Daten rückgängig gemacht werden

						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($mapping);							//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}
				
					$deleteNimmtteil = 	" 	DELETE FROM nimmtteil
											WHERE		f_id = " .$_POST['f_id']. "
											AND			s_id = " .$_POST['loeschen']. ";"
										;


					if ($errorSwitch && pg_query($db,$deleteNimmtteil)) {
					}else {
					
						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($deleteNimmtteil );					//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}


					$deleteSchueler = "	DELETE FROM schueler 
										WHERE	s_id=".$_POST['loeschen']."
										AND		0 = (	SELECT 	count(*) 
														FROM	nimmtteil
														WHERE	s_id = ".$_POST['loeschen']."); "
									;



					if ($errorSwitch && pg_query($db,$deleteSchueler)) {

						if (pg_query($db,"COMMIT;")) {
						}else {
					
							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							print_r($deleteSchueler );					//Eine Fehlermeldung wird im Browser angezeigt 

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
			// ---> Verbindet eine Fahrt mit einem bestehenden Datensatz der Relation schueler---------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 8){

				$insert = "INSERT INTO nimmtteil (s_id,f_id) VALUES (".$_POST['auswaehlen'].",".$_POST['f_id'].")"	;	// Erstellen des gesamten INSERT-Statement

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

				$result= pg_query($db,"SELECT nextval ('schuelerSeq')");   // Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){					// |
					$schuelerSequenceNr = $row['nextval'];					// |
				}														// |


				$attributeInsert="";	//  Die Attribute für das INSERT-Statement werden hier abgespeichert
				$valuesInsert="";		//  Die Werte der einzelnen Attribute werden hier abgespeichert
				foreach ($attributeSchueler as $key => $val) {
					if ($key == 0){
						$attributeInsert	= $attributeInsert .$val .							","	;  	// ...., attribute1, attribute2, ...			
						$valuesInsert 		= $valuesInsert .	$schuelerSequenceNr .				","	;	// Anhängen der Werte: Hier die neue ID	
					}elseif ($key <= 3){
						$attributeInsert	= $attributeInsert.$val.							","	;	// Anhängen mit , als Separationszeichen
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'".		","	;   // | 
					}elseif($key==4){
						$attributeInsert	= $attributeInsert.$val									;	// Letztes/r Attribut/Wert daher ohne Komma	
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'"			;   // anhängen
					}
				}
				$insert = "INSERT INTO schueler (".$attributeInsert.") VALUES (".$valuesInsert .")"	;	// Erstellen des gesamten INSERT-Statement

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
				foreach ($attributeSchueler as $key => $val) {
					if ($key == 0){													// Bei key == 0 keine Einträge, das die ID nicht verändert wird
					}elseif ($key <= 3){
						$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribte  kommasepariert		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'".		","	;	// Values kommasepariert
					}elseif($key==4){
						$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'"			;	// |
					}
				}



				$update = "	UPDATE 	schueler 									
							SET 	(".$attributeUpdate.") 
							= 		(".$valuesUpdate .")
							WHERE	s_id=" .$_POST['s_id']. "
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
			//ACHTUNG: Hier wird nur ein Datensatz geliefert, auch wenn der Schüler an mehreren Schueleren teilgenommen hat

			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}elseif (array_key_exists('select', $_GET) && array_key_exists('s_id',$_GET) ){ // Aufruf von schueler.php durch schueler.php
				$where = "WHERE f.s_id =".$_GET['s_id'];
			}

			// --->  Verhinderung von Fehlermeldungen ---------------------------------------------------------
*/
			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			// ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
			// MODUS 1 --------------------------------------------------------------------------------------------------------------------------------
			if 		(array_key_exists('modus',$_GET) && $_GET['modus'] == 1){	// Zeige alle Datensätze

				$where = '';								// -- kein WHERE-Clause 	

			// MODUS 2 --------------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 2){	// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
				$where 			= ''	;					// -- kein WHERE-Clause 	
				
			// MODUS 3 --------------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 3){	// Öffne Suchfenster und zeigt die selektierten Datensätze (NICHT IMPLEMENTIERT)

				$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
				if (is_numeric($_POST["s_id_input"])){						// Anführungsstriche werden entfernt
					$s_id = $_POST["s_id_input"]						;	// ???
				}else{
					$s_id = $_POST["s_id"]								;
				}
				$where = 'WHERE s_id'.$_POST["s_id_operator"] . $s_id	;	// Erstellen des WHERE-Clause -> WHERE s_id [>|>=|= usw]
				
				
			// MODUS 4 --------------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 4){	//	Das Skript schueler.php wurde vom Skript fahrt.php
															//  aufgerufen. schueler.php zeigt jetzt nur die Schueler zur
															//  übergebenen fahrt_id an. 

				$where = 'WHERE s.s_id IN (
											SELECT 		s_id 
											FROM 	 	nimmtteil	
											WHERE		f_id ='. $_GET["f_id"].'
										)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// MODUS 5 --------------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 5){		// Hinzufügen eines Datensatzes			

				$suchfenster = 0	;						// Es wird kein Suchfenster geöffnet
				$where = ''			;						// Alle Datensätze werden angezeigt

			// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 8){		//	Das Skript schueler.php wurde vom Skript fahrt.php
															//  aufgerufen. schueler.php zeigt jetzt nur die Schüler zur
															//  übergebenen f_id an. 

				$suchfenster = 0									;
				$where = 'WHERE s.s_id NOT IN (
											SELECT 		s_id 
											FROM 		nimmtteil
											WHERE		f_id ='. $_GET["f_id"].'
										)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE

			// MODUS 9 DELETE --------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 9){		//	Das Skript schueler.php wurde vom Skript fahrt.php 
															//  aufgerufen. schueler.php zeigt jetzt nur die Schueler an
															//  die zur übergebenen f_id gehören

				$suchfenster = 0									;
				$where = 'WHERE s.s_id  IN (
											SELECT 		s_id 
											FROM 		nimmtteil
											WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// ACTION 2 UPDATE --------------------------------------------------------------------------------------------------------------------------
			}elseif (array_key_exists('action',$_POST) && $_POST['action'] == 2){ // Der zu editieren Datensatz wird kein zweites Mal angezeigt

				$suchfenster = 0									;
				$where = 'WHERE s.s_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
			switch ($schalter) {
				case "s_id":					// Sortiert nach s_id
					$result = pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat,s.m_w
												FROM 				schueler s 
												ORDER BY			s.s_id;
								");
					break;
				case "vname":					// Sortiert nach vname
					$result = pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat,s.m_w
												FROM 				schueler s 
												ORDER BY			s.vname;
								");

					break;
				case "nname":					 	// Sortiert nach nname
					$result = pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat,s.m_w
												FROM 				schueler s 
												ORDER BY			s.nname;
								");

													break;
				case "gebdat":						// Sortiert nach gebdat
					$result = pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat,s.m_w
												FROM 				schueler s 
												ORDER BY			s.gebdat;
								");



													break;
				case "m_w":					// Sortiert nach m_w
					$result = 	pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat,s.m_w
												FROM 				schueler s 
												ORDER BY			s.m_w;
								");


													break;

				default:					// Standardabfrage mit keinem oder unterschiedlichem WHERE-Clause
					$result = pg_query($db,
											"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat, s.m_w
												FROM 				schueler s 
												".$where."
												;
								");
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// Hier stand der SearchBlock siehe Datei SeachFragment.php


			echo'	<div id="rahmen_3">';
			
					if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
						echo '<form name="delete" action="schueler.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
						echo	'<fieldset>'												;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
						echo '<form name="edit" action="schueler.php" method="POST" >'		;
						echo	'<fieldset>'												; 	// Das fieldset wird für den Edit-Button benötigt
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
								array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
						echo '<form name="edit" action="schueler.php" method="POST" >'		;
						echo	'<fieldset>'												; 	
					}

					$SpeicherDefaultWerte= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)
						"s_id" 			=> ""		,
						"vname"			=> ""		,
						"nname"			=> ""		,
						"gebdat"		=> ""		,
						"m_w"			=> ""		,
					];

					$SpeicherDefaultWertePuffer= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)
						"s_id" 			=> ""		,
						"vname"			=> ""		,
						"nname"			=> ""		,
						"gebdat"		=> ""		,
						"m_w"			=> ""		,
					];





					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerSchueler as $key => $value)	{				// Spaltenkopfbezeichner mit und ohne Link
									if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze
											echo '<th class="grau">'. $value ."</th>";					// |
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |
											echo '<th class="grau">'. $value ."</th>";					// |
									}else{																// |
											echo '<th class="grau" >' . '<a href="schueler.php?sort='.$key.'">'. $value ."</a></th>";	// |
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

															"	SELECT 				s.s_id, s.vname, s.nname,s.gebdat, s.m_w
																FROM 				schueler s 
																WHERE		s.s_id=" . $_POST['editieren']. "		
																;
														");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

									$row=pg_fetch_assoc($defaultValuesEdit);

									foreach ($SpeicherDefaultWerte as $key => $val) { 		
										$SpeicherDefaultWertePuffer[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray
									}
								}
							
							echo '<tr>'																		;
							echo 	'<form name="insert" action="schueler.php" method="POST" >'				;
							echo		'<td	class= "gelb">'												;

								// ---> Wählen des passenden Submit-Button [ADD|Update] ---------------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<button type="submit" name="action" value="9">ADD</button>	'					; 	// Einfügen des ADD
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){						
									echo 			'<button type="submit" name="action" value="3">Update</button>	'				;	// Update-Button
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


								// ---> Versteckte Übergabe. Bei ADD -> f_i bei Update -> s_id   -------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">'						; // Versteckt	
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){											// ODER
									echo 			'<input id="s_id" name="s_id" type="hidden" value="'. $SpeicherDefaultWertePuffer['s_id'] . '">'; // Versteckt	
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

							echo 		'</td>'																									;
							echo 		'<td	class= "gelb">'																					;
							echo 			'<input type="text" name="vname" size="15" value="' .$SpeicherDefaultWertePuffer['vname']. '"/>'	; // Input-Field &
							echo 		'</td>'																									; // Defaultwert
							echo 		'<td	class= "gelb">'																					;
							echo 			'<input type="text" name="nname" size="15" value="' .$SpeicherDefaultWertePuffer['nname']. '"/>'; // Inputfield "vname"
							echo 		'</td>'																								; // & Defaultwert
							echo 		'<td	class= "gelb">'																					;
							echo 			'<input type="text" name="gebdat" size="15" value="' .$SpeicherDefaultWertePuffer['gebdat']. '"/>'; // Inputfield "vname"
							echo 		'</td>'																									;	
							echo 		'<td	class= "gelb">'																					;
							echo 			'<input type="text" name="m_w" size="1" value="' .$SpeicherDefaultWertePuffer['m_w']. '"/>'	; // Inputfield "vname"
							echo 		'</td>'																									;
							echo 	'</form>'																									;	
							echo '</tr>'																										;
						}

						// 14 ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
					
						while($row=pg_fetch_assoc($result)){					// Anzeigen der Datensätze
							echo "<tr>";
								foreach ($attributeSchueler as $value)	{
									if($value == 's_id') {						// Zum Aufrufen anderer Skripte wird die s_id benötigt
										$schuelerID = $row[ $value ]; 
									}
									
									if( $value == 's_id'){
										echo '<td class="hellgrau">' . '<a href="fahrt.php?modus=11&s_id='.$schuelerID.'">&#x1f441;</a></td>'; // Link
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
						
									
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																								// von zu löschenden  tuple
										echo '<td class="rot"><input type="radio" id="'. $schuelerID .'" name="loeschen" value="'. $schuelerID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
											array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $schuelerID .'" name="editieren" value="'. $schuelerID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $schuelerID .'" name="auswaehlen" value="'. $schuelerID .'">'	;
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

