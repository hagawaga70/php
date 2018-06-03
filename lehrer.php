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
			// POST action  0 1 3 5   8 9
			// ------------------------------------------------------
			// POST action 0  	Hinzufügen eines Datensatzes
			// GET  modus  5	Hinzufügen eines Datensatzes
			// ------------------------------------------------------
			// POST action 3	Editieren eines Datensatzes
			// ------------------------------------------------------
			// GET  modus  1 	Zeige alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  // action und der Wert 5 werden nirgends weiter abgefragt
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  4   	lehrer.php wird von fahrt.php aufgerufen und zeigt alle Lehrer, die an einer bestimmten Fahrt teilnehmen 	
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// .GET  modus  8   	lehrer.php wird fahrt.php aufgerufen. 
			// .POST action 8	A)	Ein bestehender Lehrerdatensatz (l_id) wird mit der f_id in der Relation begleitet eingetragen
			// .POST action 9	B)  Ein neuer Lehrerdatensatz wird angelegt. Die l_id und die f_id werden in der Relation begleitet abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// .GET  modus  9   	lehrer.php wird von fahrt.php aufgerufen. 
			// .POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			// ---> Array mit den Attributen der Relation "Lehrer" -----------------------------------------------
			$attributeLehrer = [							
    			0	=> "l_id"		,
    			1	=> "anrede"		,
    			2	=> "vname"		,
    			3	=> "nname"		,
    			4	=> "telnr"		,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// ---> Array mit den Übersetzungen der Attribte  der Relation "Lehrer"------------------------------->
			$spaltenBezeichnerLehrer = [
    			"l_id" 			=> "Fahrt"			,
    			"anrede"		=> "Anrede"			,
    			"vname"			=> "Vorname"		,
    			"nname"			=> "Nachname"		,
    			"telnr"			=> "Telefonnummer"	,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// ---> Datenbankanbindung  ------------------------------------------------------------------------
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// ---> Hinzufügen eines Datensatzes und Eintrag in die Relation "begleitet"---------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 9){

				$result= pg_query($db,"SELECT nextval ('lehrerSeq')");   	// Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){						// |
					$lehrerSequenceNr = $row['nextval'];					// |
				}

				$mapping =

						"WITH data (f_id, l_id,anrede,vname,nname,telnr) 
						AS (
								VALUES (".$_POST["f_id"].",".$lehrerSequenceNr .",'".$_POST["anrede"]."','".$_POST["vname"]."','".$_POST["nname"]."',".$_POST["telnr"].")
						), 
						ins1 AS (
							INSERT INTO lehrer (l_id,anrede,vname,nname,telnr)
							SELECT l_id,anrede,vname,nname,telnr FROM data
							ON CONFLICT DO NOTHING
							Returning l_id
						)
							INSERT INTO begleitet (l_id,f_id)
							SELECT 	l_id, f_id
							FROM 	data
							JOIN ins1 USING (l_id);";

				if (pg_query($db,$mapping)) {
				}else {
				
					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
					print_r($mapping);				//Eine Fehlermeldung wird im Browser angezeigt 
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// --> Löchen einer Verbindung (Relation: begleitet) zwischen einer Fahrt(Relation: fahrt) und einem Lehrer (Relation:Lehrer)
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

					$errorSwitch=true;	

					if (pg_query($db,"BEGIN TRANSACTION;")) {
					}else {
					
						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($mapping);							//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}
				
					$deleteBegleitet = 	" 	DELETE FROM begleitet
											WHERE		f_id = " .$_POST['f_id']. "
											AND			l_id = " .$_POST['loeschen']. ";"
										;


					if ($errorSwitch && pg_query($db,$deleteBegleitet)) {
					}else {
					
						print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
						print_r($deleteBegleitet );					//Eine Fehlermeldung wird im Browser angezeigt 
						$errorSwitch = false;
					}


					$deleteLehrer = "	DELETE FROM lehrer 
										WHERE	l_id=".$_POST['loeschen']."
										AND		0 = (	SELECT 	count(*) 
														FROM	begleitet
														WHERE	l_id = ".$_POST['loeschen']."); "
									;



					if ($errorSwitch && pg_query($db,$deleteLehrer)) {

						if (pg_query($db,"COMMIT;")) {
						}else {
					
							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							print_r($deleteLehrer );					//Eine Fehlermeldung wird im Browser angezeigt 

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

			// ---> Verbindet eine Fahrt mit einem bestehenden Datensatz der Relation lehrer---------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 8){

				$insert = "INSERT INTO begleitet (l_id,f_id) VALUES (".$_POST['auswaehlen'].",".$_POST['f_id'].")"	;	// Erstellen des gesamten INSERT-Statement

				if (pg_query($db,$insert)) {					// Ausführten des INSERT-Statements 
				}else {
					print_r( "Data entry unsuccessful. ")	;	// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db))				;   // |
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<











			// Hinzufügen eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 0){

				$result= pg_query($db,"SELECT nextval ('lehrerSeq')");   // Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){					// |
					$lehrerSequenceNr = $row['nextval'];					// |
				}														// |


				$attributeInsert="";	//  Die Attribute für das INSERT-Statement werden hier abgespeichert
				$valuesInsert="";		//  Die Werte der einzelnen Attribute werden hier abgespeichert
				foreach ($attributeLehrer as $key => $val) {
					if ($key == 0){
						$attributeInsert	= $attributeInsert .$val .							","	;  	// ...., attribute1, attribute2, ...			
						$valuesInsert 		= $valuesInsert .	$lehrerSequenceNr .				","	;	// Anhängen der Werte: Hier die neue ID	
					}elseif ($key <= 3){
						$attributeInsert	= $attributeInsert.$val.							","	;	// Anhängen mit , als Separationszeichen
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'".		","	;   // | 
					}elseif($key==4){
						$attributeInsert	= $attributeInsert.$val									;	// Letztes/r Attribut/Wert daher ohne Komma	
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'"			;   // anhängen
					}
				}
				$insert = "INSERT INTO lehrer (".$attributeInsert.") VALUES (".$valuesInsert .")"	;	// Erstellen des gesamten INSERT-Statement

				if (pg_query($db,$insert)) {					// Ausführten des INSERT-Statements 
				}else {
					print_r( "Data entry unsuccessful. ")	;	// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db))				;   // |
				}
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// 032 Editieren/Ändern eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		//	Ändern eines Datensatzes
				$attributeUpdate=""	;												// Attribut-Zeichenkette für das Update-Statement
				$valuesUpdate=""	;												// Value-Zeichenkette für das Update-Statement					
				foreach ($attributeLehrer as $key => $val) {
					if ($key == 0){													// Bei key == 0 keine Einträge, das die ID nicht verändert wird
					}elseif ($key <= 3){
						$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribte  kommasepariert		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'".		","	;	// Values kommasepariert
					}elseif($key==4){
						$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'"			;	// |
					}
				}



				$update = "	UPDATE 	lehrer 									
							SET 	(".$attributeUpdate.") 
							= 		(".$valuesUpdate .")
							WHERE	l_id=" .$_POST['l_id']. "
							;"	;											// Zusammenstellen des Update-Statement	
				$note = pg_query($db,$update)	;							// Ausführen des UPDATE-Statement	

				if (pg_query($db,$update)) {
				}else {
					print_r( "Data entry unsuccessful. ");					// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db)); 							// |
				}
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			//ACHTUNG: Hier wird nur ein Datensatz geliefert, auch wenn der Schüler an mehreren Lehreren teilgenommen hat

			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}elseif (array_key_exists('select', $_GET) && array_key_exists('l_id',$_GET) ){ // Aufruf von lehrer.php durch schueler.php
				$where = "WHERE f.l_id =".$_GET['l_id'];
			}

			// --->  Verhinderung von Fehlermeldungen ---------------------------------------------------------

			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			// ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
			// MODUS 1 --------------------------------------------------------------------------------------------------------------------------------
			if 		($_GET['modus'] == 1){					// Zeige alle Datensätze

				$where = '';								// -- kein WHERE-Clause 	

			// MODUS 2 --------------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 2){					// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
				$where 			= ''	;					// -- kein WHERE-Clause 	
				
			// MODUS 3 --------------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 3){					// Öffne Suchfenster und zeigt die selektierten Datensätze

				$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
				if (is_numeric($_POST["l_id_input"])){						// Anführungsstriche werden entfernt
					$l_id = $_POST["l_id_input"]						;	// ???
				}else{
					$l_id = $_POST["l_id"]								;
				}
				$where = 'WHERE l_id'.$_POST["l_id_operator"] . $l_id	;	// Erstellen des WHERE-Clause -> WHERE l_id [>|>=|= usw]
				
				
			// MODUS 4 --------------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 4){					//	Das Skript lehrer.php wurde vom Skript aktivitaeten au
															//  aufgerufen. lehrer.php zeigt jetzt nur die lehreren zur
															//  übergebenen aktivitaeten_id an. 

				$suchfenster = 0									;
				$where = 'WHERE l.l_id IN (
											SELECT 		l_id 
											FROM 	 	begleitet	
											WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// MODUS 5 --------------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 5){					// Hinzufügen eines Datensatzes			

				$suchfenster = 0	;						// Es wird kein Suchfenster geöffnet
				$where = ''			;						// Alle Datensätze werden angezeigt

			// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 8){					//	Das Skript lehrer.php wurde vom Skript aktivitaeten au
															//  aufgerufen. lehrer.php zeigt jetzt nur die lehreren zur
															//  übergebenen aktivitaeten_id an. 

				$suchfenster = 0									;
				$where = 'WHERE l.l_id NOT IN (
											SELECT 		l_id 
											FROM 		begleitet
											WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE

			// MODUS 9 DELETE --------------------------------------------------------------------------------------------------------------------------
			}elseif ($_GET['modus'] == 9){					//	Das Skript lehrer.php wurde vom Skript fahrt.php 
															//  aufgerufen. lehrer.php zeigt jetzt nur die Lehrer an
															//  die zur übergebenen f_id gehören

				$suchfenster = 0									;
				$where = 'WHERE l.l_id  IN (
											SELECT 		l_id 
											FROM 		begleitet
											WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				
			// ACTION 2 DELETE --------------------------------------------------------------------------------------------------------------------------
			}elseif ($_POST['action'] == 2){				

				$suchfenster = 0									;
				$where = 'WHERE l.l_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
			}

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
			switch ($schalter) {
				case "l_id":					// Sortiert nach l_id
					$result = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												ORDER BY			l.l_id;
								");
					break;
				case "anrede":					// Sortiert nach anrede
					$result = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												ORDER BY			l.anrede;
								");

					break;
				case "vname":					 	// Sortiert nach vname
					$result = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												ORDER BY			l.vname;
								");

													break;
				case "nname":						// Sortiert nach nname
					$result = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												ORDER BY			l.nname;
								");



													break;
				case "telnr":					// Sortiert nach telnr
					$result = 	pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												ORDER BY			l.telnr;
								");


													break;

				default:					// Standardabfrage mit keinem oder unterschiedlichem WHERE-Clause
					$result = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												".$where."
												;
								");
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// Hier stand der SearchBlock siehe Datei SeachFragment.php


			echo'	<div id="rahmen_3">';
			
					if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
						echo '<form name="delete" action="lehrer.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
						echo	'<fieldset>'												;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
						echo '<form name="edit" action="lehrer.php" method="POST" >'		;
						echo	'<fieldset>'												; 	// Das fieldset wird für den Edit-Button benötigt
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
								array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
						echo '<form name="edit" action="lehrer.php" method="POST" >'		;
						echo	'<fieldset>'												; 	
					}


					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerLehrer as $key => $value)	{				// Spaltenkopfbezeichner mit und ohne Link
									if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze
											echo '<th class="grau">'. $value ."</th>";					// |
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |
											echo '<th class="grau">'. $value ."</th>";					// |
									}else{																// |
											echo '<th class="grau" >' . '<a href="lehrer.php?sort='.$key.'">'. $value ."</a></th>";	// |
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

										$SpeicherDefaultWerte= [							// Array zum Speichern der Defaultwerte der Formularlemente (Editieren)
										"l_id" 			=> ""		,
										"anrede"		=> ""		,
										"vname"			=> ""		,
										"nname"			=> ""		,
										"telnr"			=> ""		,
									];
								if( array_key_exists('action',$_POST) 	&& $_POST["action"] == 2){			// Edit
									$defaultValuesEdit = pg_query($db,
															"	SELECT 		l.l_id, l.anrede, l.vname, l.nname, l.telnr
																FROM 		lehrer l 
																WHERE		l.l_id=" . $_POST['editieren']. "		
																;
														");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

									$row=pg_fetch_assoc($defaultValuesEdit);

									foreach ($SpeicherDefaultWerte as $key => $val) { 		
										$SpeicherDefaultWertePuffer[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray
									}
								}
							
							echo '<tr>'																											;
							echo 	'<form name="insert" action="lehrer.php" method="POST" >'													;
							echo		'<td	class= "gelb">'																		;

								// ---> Wählen des passenden Submit-Button [ADD|Update] ---------------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<button type="submit" name="action" value="9">ADD</button>	'							; 	// Einfügen des ADD
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){										// ODER
									echo 			'<button type="submit" name="action" value="3">Update</button>	'						;	// Update-Button
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


								// ---> Versteckte Übergabe. Bei ADD -> f_i bei Update -> l_id   -------------------------------------------------------------
								if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
									echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">'						; // Versteckt	
								}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){											// ODER
									echo 			'<input id="l_id" name="l_id" type="hidden" value="'. $SpeicherDefaultWertePuffer['l_id'] . '">'; // Versteckt	
								}
								//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

							echo 		'</td>'																									;
							echo 		'<td	class= "gelb">'																					;
							echo 			'<input type="text" name="anrede" size="6" value="' .$SpeicherDefaultWertePuffer['anrede']. '"/>'	; // Input-Field &
							echo 		'</td>'																									; // Defaultwert
							echo 		'<td	class= "gelb">'																				;
							echo 			'<input type="text" name="vname" size="15" value="' .$SpeicherDefaultWertePuffer['vname']. '"/>' ; // Inputfield "vname"
							echo 		'</td>'																								; // & Defaultwert
							echo 		'<td	class= "gelb">'																				;
							echo 			'<input type="text" name="nname" size="15" value="' .$SpeicherDefaultWertePuffer['nname']. '"/>' ; // Inputfield "vname"
							echo 		'</td>'																								;	
							echo 		'<td	class= "gelb">'																				;
							echo 			'<input type="text" name="telnr" size="15" value="' .$SpeicherDefaultWertePuffer['telnr']. '"/>' ; // Inputfield "vname"
							echo 		'</td>'																								;
							echo 	'</form>'																								;	
							echo '</tr>'																									;
						}

						// 14 ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
					
						while($row=pg_fetch_assoc($result)){					// Anzeigen der Datensätze
							echo "<tr>";
								foreach ($attributeLehrer as $value)	{
									if($value == 'l_id') {						// Zum Aufrufen anderer Skripte wird die l_id benötigt
										$lehrerID = $row[ $value ]; 
									}
									
									if( $value == 'l_id'){
										echo '<td class="hellgrau">' . '<a href="fahrt.php?modus=10&l_id='.$lehrerID.'">&#x1f441;</a></td>'; // Link
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
						
									
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																								// von zu löschenden  tuple
										echo '<td class="rot"><input type="radio" id="'. $lehrerID .'" name="loeschen" value="'. $lehrerID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
											array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $lehrerID .'" name="editieren" value="'. $lehrerID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $lehrerID .'" name="auswaehlen" value="'. $lehrerID .'">'	;
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

