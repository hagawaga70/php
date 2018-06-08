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
			// GET	 modus  7 	Menü->Unterkunft->Edit/Bearbeiten
			// #POST action 3	Editieren eines Datensatzes
			// ------------------------------------------------------
			// GET  modus  1 	Zeige alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  4   	unterkunft.php wird von fahrt.php aufgerufen und zeigt alle Unterkunft, die an einer bestimmten Fahrt teilnehmen 	
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// .GET  modus  8   	unterkunft.php wird fahrt.php aufgerufen. 
			// #POST action 8	A)	Ein bestehender Unterkunftdatensatz (u_id) wird mit der f_id in der Relation "fahrt" eingetragen
			// #POST action 9	B)  Ein neuer Unterkunftdatensatz wird angelegt. Die u_id und die f_id werden in der Relation "wirdangeboten" abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// .GET  modus  9   	unterkunft.php wird von fahrt.php aufgerufen. 
			// #POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------
			// POST action  2 	Der zu editierende Datensatz wird kein zweites Mal angezeigt
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			$schalter 		= ""	;  		// Wird benötigt beim Sortieren der Datensätze nach bestimmten Attributen
			$suchfenster 	= 0		;		// ACHTUNG: Das Suchfenster wurde aus diesem Skript entfernt
			$on				= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Attributen der Relation "Unterkunft" -----------------------------------------------

			$attributeUnterkunft = [							
    			0	=> "u_id"		,
    			1	=> "u_name"		,
    			2	=> "typ"		,
    			3	=> "errbar"		,
    			4	=> "kosten"		,
    			5	=> "entfkern"	,
    			6	=> "u_ort"		,
			];


			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Übersetzungen der Attribte  der Relation "Unterkunft"------------------------------->

			$spaltenBezeichnerUnterkunft = [

				"u_id"	 	=>	"Fahrt"				,
				"u_name"	=>  "Name"				,
				"typ"	   	=>  "Typ"				,
				"errbar"	=>	"Erreichbarkeit"	,
				"kosten"	=>	"Kosten"			,
				"entfkern"	=>	"Entfernung"		,
				"u_ort"	   	=>	"Ort"				,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// -----------------------------------------------------------------------------------------------------
			// ---> Datenbankanbindung  ------------------------------------------------------------------------

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// -----------------------------------------------------------------------------------------------------
			// ---> Hinzufügen eines Datensatzes und Eintrag in die Relation "fahrt"---------------------------------------------------------------

			if(	array_key_exists('action',$_POST) && $_POST["action"] == 9){
				$result= pg_query($db,"SELECT nextval ('unterkunftSeq')");   	// Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){							// |
					$unterkunftSequenceNr = $row['nextval'];					// |
				}

				$errorSwitch=true;	

				if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
				}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																		// eine unvollständige Veränderung der Daten rückgängig gemacht werden

					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
					$errorSwitch = false;
				}


				
				if(array_key_exists('o_name_list',$_POST) && $_POST["o_name_list"] != '---' ){

						$selectOrte = " SELECT o_id FROM ort WHERE o_name = ".$_POST['o_name_list'].";";	
						
						$row=pg_fetch_assoc($selectOrte);
						
						if(	!array_key_exists('o_id',$row)){

							print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
							$errorSwitch = false;
								
						}else{
							$orteSequenceNr = $row['o_id'];
						}
				}else{
						$result= pg_query($db,"SELECT nextval ('orteSeq')");   	// Eine neue ID für den Datensatz wird geliefert
						while($row=pg_fetch_assoc($result)){							// |
							$orteSequenceNr = $row['nextval'];					// |
						}
				
						$insertOrte = "	INSERT INTO ort (o_id,o_name,entfng)
										VALUES			(".$orteSequenceNr.",'".$_POST["o_name"]."',0)";
											
						if ($errorSwitch && pg_query($db,$insertOrte)) {
						}else {

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								print_r($mapping );							//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}
				}


				$insertUnterkunft ="
					INSERT INTO unterkunft (u_id, u_name, typ, errbar, kosten, entfkern, u_ort)
					VALUES (".
											$unterkunftSequenceNr 								.",'"	.
											$_POST["u_name"		]								."','"	.
											$_POST["typ"		]								."','"	.
											$_POST["errbar"		]								."',"	.
											$_POST["kosten"		]								.","	.
											$_POST["entfkern"	]								.","	.
											$orteSequenceNr 									.");";	



						if ($errorSwitch && pg_query($db,$insertUnterkunft)) {
						}else {

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								print_r($mapping );							//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}
						$anbietrUpdate =    "   UPDATE fahrt 
                          	    	       		SET     f_unterkunft = ". $unterkunftSequenceNr ."
                            	         		WHERE   f_id   = " .$_POST['f_id']. ";"
                     	;


						if ($errorSwitch && pg_query($db,$anbietrUpdate)) {

								if (pg_query($db,"COMMIT;")) {
								}else {

										print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
										print_r($anbietrUpdate);					//Eine Fehlermeldung wird im Browser angezeigt 

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
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// -----------------------------------------------------------------------------------------------------
			// --> Löchen einer Verbindung zwischen einer Fahrt(Relation: fahrt) und einem Unterkunft (Relation:Unterkunft)

				if(array_key_exists('action',$_POST) && $_POST["action"] == 1){
						$errorSwitch=true;	

						if (pg_query($db,"BEGIN TRANSACTION;")) { 		// Da in zwei Relationen Veränderungen durchgeführt werden müssen. 
						}else {											// kann im Fehlerfall eines Teil-SQL-Statements durch Transaction/Rollback
																		// eine unvollständige Veränderung der Daten rückgängig gemacht werden

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}

						$nullUpdate =   "   UPDATE  fahrt 
											SET     f_unterkunft = NULL
											WHERE   f_id   = " .$_POST['f_id']. ";"
										;


						if ($errorSwitch && pg_query($db,$nullUpdate)) {
						}else {

								print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
								print_r($nullUpdate);					//Eine Fehlermeldung wird im Browser angezeigt 
								$errorSwitch = false;
						}


						$deleteUnterkunft = "	DELETE FROM unterkunft 
								WHERE	u_id=".$_POST['loeschen']."
								AND		0 = (	SELECT 	count(*) 
												FROM	fahrt
												WHERE	f_unterkunft = ".$_POST['loeschen']."); "
								;



						if ($errorSwitch && pg_query($db,$deleteUnterkunft)) {

								if (pg_query($db,"COMMIT;")) {
								}else {

										print_r(pg_last_error($db));					//Eine Fehlermeldung wird im Browser angezeigt 
										print_r($deleteUnterkunft );					//Eine Fehlermeldung wird im Browser angezeigt 

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
				// ---> Verbindet eine Fahrt mit einem bestehenden Datensatz der Relation unterkunft---------------------

				if(	array_key_exists('action',$_POST) && $_POST["action"] == 8){

						$selectUpdate = "	Update 	fahrt
										 	SET 	f_unterkunft 	= "	.$_POST['auswaehlen']."
											WHERE 	f_id 			= "	.$_POST['f_id'].";";




						if (pg_query($db,$selectUpdate)) {					// Ausführten des INSERT-Statements 
								print_r(pg_last_error($db))				;   // |
						}

				}			
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Editieren/Ändern eines Datensatzes ------------------------------------------------------------------------
				if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		// Ändern eines Datensatzes
						$attributeUpdate=""	;											// Attribut-Zeichenkette für das Update-Statement
						$valuesUpdate=""	;											// Value-Zeichenkette für das Update-Statement					
						foreach ($attributeUnterkunft as $key => $val) {

								$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

								if ($key == 0){													// Bei key == 0 keine Einträge, das die ID nicht verändert wird
								}elseif ($key <= 4){
										$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribute  kommasepariert		
										$valuesUpdate 		= $valuesUpdate . "'" . $_POST[$val]	."'".		","	;	// Values kommasepariert
								}elseif($key==5){
										$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
										$valuesUpdate 		= $valuesUpdate . "'". $_POST[$val]		."'"			;	// |
								}
						}



						$update = "	UPDATE 	unterkunft 									
								SET 	(".$attributeUpdate.") 
								= 		(".$valuesUpdate .")
								WHERE	u_id=" .$_POST['u_id']. "
								;"	;											// Zusammenstellen des Update-Statement	
								$note = pg_query($db,$update)	;							// Ausführen des UPDATE-Statement	

						if (pg_query($db,$update)) {
						}else {
								print_r( "Data entry unsuccessful. ");					// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
								print_r(pg_last_error($db)); 							// |
								print_r($update);
						}
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


				// ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
				// MODUS 1 --------------------------------------------------------------------------------------------------------------------------------
				if 		(	(array_key_exists(	'modus'	,$_GET) 	&& $_GET['modus'] 		== 1) || 	// Aufruf vom Menue->Unterkunften->Zeige 
							(array_key_exists(	'modus'	,$_GET) 	&& $_GET['modus'] 		== 7) ||	// Aufruf vom Menue->Unterkunften->Edit 
							(array_key_exists(	'action',$_POST) 	&& $_POST['action'] 	== 3) ||	// Ner
							(array_key_exists(	'action',$_POST) 	&& $_POST['action'] 	== 9)
						){	// Zeige alle Datensätze

						$on = "ON u.u_ort = o.o_id";								

						// MODUS 2 --------------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 2){	// Öffne Suchfenster und zeige alle Datensäte: NICHT IMPLEMENTIERT

						$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
						$where = 'WHERE f.f_unterkunft = u.u_id';								

						// MODUS 3 --------------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 3){	// Öffne Suchfenster und zeigt die selektierten Datensätze :NICHT IMPLEMENTIERT

						$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
						if (is_numeric($_POST["u_id_input"])){						// Anführungsstriche werden entfernt
								$u_id = $_POST["u_id_input"]						;	// ???
						}else{
								$u_id = $_POST["u_id"]								;
						}
						$where = 'WHERE u_id'.$_POST["u_id_operator"] . $u_id	;	// Erstellen des WHERE-Clause -> WHERE u_id [>|>=|= usw]


						// MODUS 4 --------------------------------------------------------------------------------------------------------------------------------
				}elseif ( 	(array_key_exists('modus',$_GET) 		&& $_GET[	"modus"] 	== 4) ||	 
							(array_key_exists('action',$_POST) 		&& $_POST[	"action"] 	== 8) 	// SELECT: Verbinde bestehenden u_id mit f_id

						){					//	unterkunft.php wurde vom fahrt.php
											//  aufgerufen. unterkunft.php zeigt jetzt nur die Unterkünfte zur
											//  übergebenen f_id an. 

							if(		array_key_exists('f_id'   , $_POST )) {
								$f_id = $_POST["f_id"];
							}elseif(	array_key_exists('f_id'   , $_GET ))		{ 
								$f_id = $_GET["f_id"];
							}
/*	
							$on = 'ON (f.f_unterkunft = u.u_id) WHERE u.u_id  IN (
										SELECT 		u_id 
										FROM 		fahrt
										WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE

*/

							$on = 'ON (u.u_ort = u.u_id) WHERE
									u.u_id IN (
												SELECT 		f_unterkunft
												FROM 	 	fahrt	
												WHERE		f_id ='. $f_id .'
											)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


						// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------
				}elseif (
							(array_key_exists('modus',$_GET) 		&& $_GET['modus'] 		== 8) ||
							(array_key_exists('action'	, $_POST) 	&& $_POST[	'action'] 	== 1)	// DELETE		
						) 	
						{		//	Das Skript unterkunft.php wurde vom Skript  fahrt.php
						//  aufgerufen. unterkunft.php zeigt jetzt nur die Unterkünfte zur
						//  übergebenen f_id an. 

						$suchfenster = 0									;
						$on = 'ON (u.u_ort = o.o_id)'; 
/*
						$on = 'ON (u.u_ort = o.o_id) WHERE 
								u.u_id NOT IN (
												SELECT 		f_unterkunft
												FROM 		fahrt
												WHERE		f_id ='. $_GET["f_id"].'
												)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
*/

						// MODUS 9 DELETE: Alle zu einer f_id gehörenden Datensätze werden angezeigt---------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 12){		//	Das Skript unterkunft.php wurde vom Skript fahrt.php 
						$on = 'ON (f.f_unterkunft = u.u_id) WHERE u.u_id  IN (
										SELECT 		u_id 
										FROM 		unterkunft
										WHERE		u_id ='. $_GET["u_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE



				}elseif ( (array_key_exists('modus',$_GET) && $_GET['modus'] == 9) ){		//	Das Skript unterkunft.php wurde vom Skript fahrt.php 
						//  aufgerufen. unterkunft.php zeigt jetzt nur die Unterkunft an
						//  die zur übergebenen f_id gehören

						$suchfenster = 0									;
						$on = 'ON (u.u_ort = o.o_id) WHERE u.u_id  IN (
										SELECT 		f_unterkunft 
										FROM 		fahrt
										WHERE		f_id ='. $_GET["f_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


						// ACTION 2 UPDATE --------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('action',$_POST) && $_POST['action'] == 2){ // Der zu editieren Datensatz wird kein zweites Mal angezeigt

						$suchfenster = 0									;
						$on = 'ON u.u_ort = o.o_id  WHERE u.u_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
				if(array_key_exists('sort',$_GET)){
						$schalter = $_GET['sort'];
				}else{
						$schalter="";
				}


				if ($schalter != ""){
						if($schalter == "o_name"){
								$result = pg_query($db,

												"	
												SELECT 	u.u_id, u.u_name, u.typ, u.errbar, u.kosten, u.entfkern, o.o_name 
												FROM 				unterkunft u
												LEFT OUTER JOIN 	ort o
												ON					u.u_ort=o.o_id
												ORDER BY			o.o_name
												;
												");
						}else{
								$result = pg_query($db,
												"
												SELECT 				u.u_id, u.u_name, u.typ, u.errbar, u.kosten, u.entfkern, o.o_name 
												FROM 				unterkunft u
												LEFT OUTER JOIN 	ort o
												ON					u.u_ort=o.o_id
												ORDER BY			u.".$schalter."
												;

												");

						}
				}else{
						$query ="	
										SELECT 				u.u_id, u.u_name, u.typ, u.errbar, u.kosten, u.entfkern, o.o_name 
										FROM 				unterkunft u
										LEFT OUTER JOIN 	ort o
										" . $on . "
										;"; 
						$result = pg_query($db,$query);
						print_r($query);

				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// Hier stand der SearchBlock siehe Datei SeachFragment.php


				echo'	<div id="rahmen_3">';
				
						if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
							echo '<form name="delete" action="unterkunft.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
							echo	'<fieldset>'													;
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
							echo '<form name="edit" action="unterkunft.php" method="POST" >'		;
							echo	'<fieldset>'													; 	// Das fieldset wird für den Edit-Button benötigt
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
									array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
							echo '<form name="edit" action="unterkunft.php" method="POST" >'		;
							echo	'<fieldset>'													;	 	
						}

						$SpeicherDefaultWerte= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)

								"u_id"	 	=>	""				,
								"u_name"	=>  ""				,
								"typ"	   	=>  ""				,
								"errbar"	=>	""				,
								"kosten"	=>	""				,
								"entfkern"	=>	""				,
								"o_name"	=>	""				,
						];


						echo "<table>";

								// ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
								echo "<tr>";

									foreach ($spaltenBezeichnerUnterkunft as $key => $value)	{			// Spaltenkopfbezeichner mit und ohne Link
										if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze

												echo '<th class="grau">'. $value ."</th>";					// |

										}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |

											if($key == 'u_ort'){
												echo '<th colspan="2" class="grau">'. $value ."</th>";					// |
											}else{
												echo '<th class="grau" >' . '<a href="unterkunft.php?sort='.$key.'">'. $value ."</a></th>";	// |
											}

										}else{
												if($key == 'u_ort'){
													echo '<th colspan="2" class="grau" >' . '<a href="unterkunft.php?sort='.$key.'">'. $value ."</a></th>";	// |
												}else{
													echo '<th class="grau" >' . '<a href="unterkunft.php?sort='.$key.'">'. $value ."</a></th>";	// |
												}
										}
									}
								
									if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Anfügen des Delete-Button

										echo 	'<td class="rot">	
														<button type="submit" name="action" value="1">DELETE</button>
														<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">
												</td>'	;

									}elseif(array_key_exists('modus',$_GET) && $_GET['modus'] ==7 ||
											array_key_exists('modus',$_GET) && $_GET['modus'] ==4 ) {		// Anfügen des Edit-Button

										echo '<td class="rot">
														<button type="submit" name="action" value="2">EDIT</button>
											 </td>'	;

									}elseif(array_key_exists('modus',$_GET) && $_GET['modus'] ==8) {		// Anfügen des Edit-Button

										echo '<td class="rot"><button type="submit" name="action" value="8">SELECT</button></td>'	;
										$formSelectOption = pg_query($db,
																	"
																	SELECT 		o_id,o_name	
																	FROM 		ort  
																	;
															");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

										while($row=pg_fetch_assoc($formSelectOption)){
											$o_name_list[$row['o_id']]= $row['o_name'];
										}
									}		
								echo "</tr>";
								// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

							if(	(array_key_exists('modus',$_GET) 	&& $_GET['modus'] 		== 8 ) ||		// vormals 5 ADD
								(array_key_exists('action',$_POST) 	&& $_POST["action"] 	== 2)			// Edit
							  )  {

									if( array_key_exists('action',$_POST) 	&& $_POST["action"] == 2){			// Edit
										$defaultValuesEdit = pg_query($db,
																	"
																	SELECT 		u.u_id, u.u_name, u.typ, u.errbar, u.kosten, u.entfkern, u.u_ort 
																	FROM 		unterkunft u 
																	WHERE		u.u_id=" . $_POST['editieren']. "		
																	;
															");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

										$row=pg_fetch_assoc($defaultValuesEdit);

										foreach ($SpeicherDefaultWerte as $key => $val) { 		
											
											$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

										}
								}
								echo '<tr>'																		;
								echo 	'<form name="insert" action="unterkunft.php" method="POST" >'				;

								if(	(array_key_exists(	'modus',$_GET) 		&& $_GET["modus"] 	== 2) ||
									(array_key_exists(	'modus',$_GET) 		&& $_GET["modus"] 	== 8) ||
									(array_key_exists(	'action',$_POST) 	&& $_POST["action"] == 2)
									
									){						
									//echo		'<td	 class= "gelb">'												;
								}else{
									//echo		'<td	class= "gelb">'												;
									
								}
									echo '<td	class= "gelb">'	;
									

									// ---> Wählen des passenden Submit-Button [ADD|Update] ---------------------------------------------------------------------
									if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// ADD
										echo 			'<button type="submit" name="action" value="9">ADD</button>	'					; 	// Einfügen des ADD
									}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){						
										echo 			'<button type="submit" name="action" value="3">Update</button>	'				;	// Update-Button
									}
									//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


									// ---> Versteckte Übergabe. Bei ADD -> f_i bei Update -> u_id   -------------------------------------------------------------
									if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 8)){ 	// vormals 5
										echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $_GET['f_id']. '">'						; // Versteckt	
									}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){											// ODER
										echo 			'<input id="u_id" name="u_id" type="hidden" value="'. $SpeicherDefaultWerte['u_id'] . '">'; // Versteckt	
									}
									//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
									

										echo 		'</td>'		;

									$formInputSize = [					// 

										"u_id"	 	=>	"10"				,
										"u_name"	=>  "15"				,
										"typ"	   	=>  "10"				,
										"errbar"	=>	"10"				,
										"kosten"	=>	"4"					,
										"entfkern"	=>	"4"					,
										"u_ort"	   	=>	"10"				,
									];
												
									foreach($attributeUnterkunft as $key => $value) {
										if($value == "u_id"){
										}elseif($value == "u_ort"){

											echo 		'<td	colspan="2" class= "gelb">

																<input type="text" name="o_name" size="'.$formInputSize[$value].'"/>'; 
											echo				'<select name="o_name_list">'											;
											echo '					<option>---</option>'									;
											foreach ($o_name_list as $key => $val) {	
												echo '				<option >'.$val.'</option>'									;
											}

											echo				'</select>'											;

											echo 		'</td>'								;
										}else{

												echo 		'<td	class= "gelb">'											;
												echo 			'<input type="text" name="'					.
																		$attributeUnterkunft[ $key ]		.	
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
									foreach ($attributeUnterkunft as $value)	{
										if($value == 'u_id') {						// Zum Aufrufen anderer Skripte wird die u_id benötigt
											$unterkunftID = $row[ $value ]; 
										}
									}
									foreach ($attributeUnterkunft as $value)	{
										if( $value == 'u_id'){
											echo '<td class="hellgrau">' . '<a href="fahrt.php?modus=14&u_id='.$unterkunftID.'">&#x1f441;</a></td>'; // Link
										}elseif($value== 'u_ort'){
										//	if ($row[ $value] == ''){
										//	 	echo '<td class="grau"></td>';   // Link
	                                         	echo '<td class="grau">' .  $row[ 'o_name' ] .'</td>';    // Link
	                                         	echo '<td class="grau">' . '<a href="orte.php?modus=4&u_id='.$unterkunftID .'">&#x1f441;</a></td>';    // Link
	    	                           //     	echo '<td class="grau"></td>';    // Link
										//	}else{
										//		echo '<td class="grau">' . '<a href="orte.php?modus=4&u_id='.$unterkunftID .'">&#x1f441;</a></td>';   // Link
	                                   //      	echo '<td class="grau">' .  $row[ $value ] .'</td>';    // Link
	    	                          //       	echo '<td class="grau">' . '<a href="orte.php?modus=9&u_id='.$unterkunftID .'">&#x2796;</a></td>';    // Link
									//		}
	
										}else{
											echo "<td>" . $row[ $value] . "</td>";
										}
							
										
									}
									if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																									// von zu löschenden  tuple
											echo '<td class="rot"><input type="radio" id="'. $unterkunftID .'" name="loeschen" value="'. $unterkunftID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
												array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $unterkunftID .'" name="editieren" value="'. $unterkunftID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $unterkunftID .'" name="auswaehlen" value="'. $unterkunftID .'">'	;
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

