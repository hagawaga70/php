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
			// #GET	 modus  7 	Menü->Aktivitaet->Edit/Bearbeiten
			// #POST action 3	Editieren eines Datensatzes
			// ------------------------------------------------------
			// #GET  modus  1 	Zeige alle Datensätze
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  (NICHT IMPLEMENTIERT)
			// --------------------------------------------------------------------------------------------------------------------------------------
			// #GET  modus  4   	orte.php wird von aktiviteat.php mit einer bestimmten ak_id aufgerufen und zeigt den Anbieter der Aktivität
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  MAP 
			// #GET  modus  8   	orte.php wird aktiviteat.php aufgerufen. 
			// #POST action 8	A)	Ein bestehender Aktivitaetdatensatz (ak_id) wird mit der f_id in der Relation "wirdangeboten" eingetragen
			// #POST action 9	B)  Ein neuer Aktivitaetdatensatz wird angelegt. Die ak_id und die f_id werden in der Relation "wirdangeboten" abgespeichert
			// --------------------------------------------------------------------------------------------------------------------------------------
			//  DELETE
			// #GET  modus  9   	orte.php wird von aktiviteat.php mit einer bestimmten ak_id aufgerufen. Sofern der dazugehörige Anbieter nur
			//						ein Event anbietet wird er gelöscht und die orte_id im enstrechenden Tuple der Relation aktivität gelöscht
			// #POST action 1   	Entfernen eines Datensatzes
			// --------------------------------------------------------------------------------------------------------------------------------------
			// #POST action  2 	Der zu editierende Datensatz wird kein zweites Mal angezeigt
			// --------------------------------------------------------------------------------------------------------------------------------------


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			$schalter 		= ""	;  		// Wird benötigt beim Sortieren der Datensätze nach bestimmten Attributen
			$suchfenster 	= 0		;		// ACHTUNG: Das Suchfenster wurde aus diesem Skript entfernt
			$on				= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			$where			= ""	;		// Zur Erweiterung von SQL-SELECT-Statements
			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Attributen der Relation "Aktivitaet" -----------------------------------------------

			$attributeAktivitaet = [							
    			0	=> "o_id"			,
    			1	=> "entfng"			,
    			2	=> "o_name"			,
    			3	=> "trprtm"			,
    			4	=> "geograf"		,
    			5	=> "ewzahl"			,
    			6	=> "sprache"		,
    			7	=> "schickf"		,
    			8	=> "urb_rur"		,
			];


			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// -----------------------------------------------------------------------------------------------------
			// ---> Array mit den Übersetzungen der Attribte  der Relation "Aktivitaet"------------------------------->

			$spaltenBezeichnerAktivitaet = [

				"o_id"			=>	"Unterkunft"			,
				"entfng"	    =>  "Entfernung"			,	
				"o_name"	    =>  "Name"					,
				"trprtm"	    =>  "Transportmittel"		,
				"geograf"       =>  "geografische Lage"		,
				"ewzahl"	    =>  "Einwohnerzahl"			,
				"sprache"       =>  "Sprache"				,
				"schickf"       =>  "Schickfaktor"			,
				"urb_rur"       =>  "Gegend"
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			
			
			// -----------------------------------------------------------------------------------------------------
			// ---> Datenbankanbindung  ------------------------------------------------------------------------

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");

			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


				// Editieren/Ändern eines Datensatzes in der Relation Anbieter-----------------------------------------------

				if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		// Ändern eines Datensatzes
						$attributeUpdate=""	;											// Attribut-Zeichenkette für das Update-Statement
						$valuesUpdate=""	;											// Value-Zeichenkette für das Update-Statement					
						foreach ($attributeAktivitaet as $key => $val) {

							$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

								if ($key == 0){													// Bei key == 9 keine Einträge, da die ID nicht verändert wird
								}elseif ($key <= 7){
										$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribute  kommasepariert		
										$valuesUpdate 		= $valuesUpdate . "'" . $_POST[$val]		."'".		","	;	// Values kommasepariert
								}elseif($key==8){
										$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
										$valuesUpdate 		= $valuesUpdate . "'". $_POST[$val]		."'"			;	// |
								}
						}



						$update = "	UPDATE 	ort 									
								SET 	(".$attributeUpdate.") 
								= 		(".$valuesUpdate .")
								WHERE	o_id=" .$_POST['o_id']. "
								;"	;											// Zusammenstellen des Update-Statement	
								$note = pg_query($db,$update)	;				// Ausführen des UPDATE-Statement	
						
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
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 2){	// Öffne Suchfenster und zeige alle Datensäte NICHT UMGESETZT

						$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
						$where = 'WHERE ak.anbietr = an.o_id';								

						// MODUS 3 --------------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 3){	// Öffne Suchfenster und zeigt die selektierten Datensätze NICHT UMGESETZT

						$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
						if (is_numeric($_POST["ak_id_input"])){						// Anführungsstriche werden entfernt
								$ak_id = $_POST["ak_id_input"]						;	// ???
						}else{
								$ak_id = $_POST["ak_id"]								;
						}
						$where = 'WHERE ak_id'.$_POST["ak_id_operator"] . $ak_id	;	// Erstellen des WHERE-Clause -> WHERE ak_id [>|>=|= usw]


						// MODUS 4 --------------------------------------------------------------------------------------------------------------------------------
				}elseif ( 	array_key_exists('modus'	, $_GET	) 	&& $_GET[	'modus'	] 	== 4){

							$where= 'WHERE o_id IN (SELECT u_ort FROM unterkunft WHERE u_id=' .$_GET['u_id'].')' ;	// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


 
				}elseif ( 	(array_key_exists('action'	, $_POST) 	&& $_POST[	"action"] 	== 8) ||	// SELECT: Verbinde bestehende ak_id mit f_id
							(array_key_exists('action'	, $_POST) 	&& $_POST[	'action'] 	== 1) 		// DELETE		

						){					//	orte.php wurde vom aktiviteat.php
											//  aufgerufen. orte.php zeigt jetzt nur die Anbieter zur
											//  übergebenen ak_id an. 
						// MODUS 8 MAP ----------------------------------------------------------------------------------------------------------------------------

				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 8){		//	Das Skript orte.php wurde vom Skript  aktiviteat.php
						//  aufgerufen. orte.php zeigt jetzt nur die Aktivitaeten zur
						//  übergebenen f_id an. 

						$suchfenster = 0;
						$where = "";
			/*
						$on = 'ON (ak.anbietr = an.o_id) WHERE 
								ak.ak_id NOT IN (
												SELECT 		ak_id 
												FROM 		wirdangeboten
												WHERE		f_id ='. $_GET["f_id"].'
												)';											// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
			*/

						// MODUS 9 DELETE: Alle zu einer ak_id gehörenden Datensätze werden angezeigt---------------------------------------------------------------
				}elseif (array_key_exists('modus',$_GET) && $_GET['modus'] == 9){		//	Das Skript orte.php wurde vom Skript aktiviteat.php 
						//  aufgerufen. orte.php zeigt jetzt nur die Aktivitaet an
						//  die zur übergebenen f_id gehören

						$suchfenster = 0									;
						$where = 'WHERE o_id IN (
										SELECT 		anbietr 
										FROM 		unterkunft
										WHERE		ak_id ='. $_GET["ak_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE


						// ACTION 2 UPDATE --------------------------------------------------------------------------------------------------------------------------
				}elseif (array_key_exists('action',$_POST) && $_POST['action'] == 2){ // Der zu editieren Datensatz wird kein zweites Mal angezeigt

						$suchfenster = 0									;
						$where = 'WHERE o_id != '. $_POST["editieren"];   // Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
				if(array_key_exists('sort',$_GET)){
						$schalter = $_GET['sort'];
				}else{
						$schalter="";
				}



				if ($schalter != ""){  // Datensätze werden nach dem jeweiligen Attribut sortiert
						$result = pg_query($db,
										"	SELECT 	o_id,entfng,o_name,trprtm,geograf,ewzahl,sprache,schickf,urb_rur
											FROM 	ort
											ORDER BY ". $schalter . "
												;
												");
				}else{
						$Spuffer = 
										"	SELECT 	o_id,entfng,o_name,trprtm,geograf,ewzahl,sprache,schickf,urb_rur
											FROM 	ort;";
						$result = pg_query($db,$Spuffer);

				}

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// Hier stand der SearchBlock siehe Datei SeachFragment.php


				echo'	<div id="rahmen_3">';
				
						if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {				// Der Delete-Button wird angehängt
							echo '<form name="delete" action="orte.php" method="POST" >'		;	// Das fieldset wird für die Radio-Button benötigt
							echo	'<fieldset>'													;
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7    ) {		// Der Edit-Button wird angehängt
							echo '<form name="edit" action="orte.php" method="POST" >'		;
							echo	'<fieldset>'													; 	// Das fieldset wird für den Edit-Button benötigt
						}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8 ||
									array_key_exists('modus',$_GET) && $_GET['modus'] ==4    ) {		
							echo '<form name="edit" action="orte.php" method="POST" >'		;
							echo	'<fieldset>'													;	 	
						}

						$SpeicherDefaultWerte= [				// Array zum Speichern der Defaultwerte der Formularelemente (Editieren)

								"o_id"			=>	""	,
								"entfng"	    =>  ""	,	
								"o_name"	    =>  ""	,
								"trprtm"	    =>  ""	,
								"geograf"       =>  ""	,
								"ewzahl"	    =>  ""	,
								"sprache"       =>  ""	,
								"schickf"       =>  ""	,
								"urb_rur"       =>  ""
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
												echo '<th class="grau" >' . '<a href="orte.php?sort='.$key.'">'. $value ."</a></th>";	// |
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
																"	SELECT 		o_id,entfng,o_name,trprtm,geograf,ewzahl,sprache,schickf,urb_rur
																	FROM 		ort 
																	WHERE		o_id=" . $_POST['editieren']. "		
																	;
															");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

										$row=pg_fetch_assoc($defaultValuesEdit);

										foreach ($SpeicherDefaultWerte as $key => $val) { 		
											
												$SpeicherDefaultWerte[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray

									}
								}
								echo '<tr>'																		;
								echo 	'<form name="insert" action="orte.php" method="POST" >'				;

										$formInputSize = [					// Legt die Größe der Input-Fenster fest
												"o_id"			=>	""	,
												"entfng"	    =>  ""	,	
												"o_name"	    =>  ""	,
												"trprtm"	    =>  ""	,
												"geograf"       =>  ""	,
												"ewzahl"	    =>  ""	,
												"sprache"       =>  ""	,
												"schickf"       =>  ""	,
												"urb_rur"       =>  ""
		
										];
												
									foreach($attributeAktivitaet as $key => $value) {
										if($value == "o_id"){
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
										echo 			'<input id="o_id" name="o_id" type="hidden" value="'. $SpeicherDefaultWerte['o_id'] . '">'; // Versteckt	
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
										if($value == 'o_id') {						// Zum Aufrufen anderer Skripte wird die ak_id benötigt
											$orteID = $row[ $value ]; 
										}
										
										if( $value == 'o_id'){
											echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=12&anbietr='.$orteID.'">&#x1f441;</a></td>'; // Link
										}else{
											echo "<td>" . $row[ $value] . "</td>";
										}
							
										
									}
									if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 9) {	// Einfügen von Radiobutton zum Selektieren 
																									// von zu löschenden  tuple
											echo '<td class="rot"><input type="radio" id="'. $orteID .'" name="loeschen" value="'. $orteID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7 ||
												array_key_exists('modus',$_GET) && $_GET['modus'] == 4 ) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $orteID .'" name="editieren" value="'. $orteID .'">'	;
									}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==8) { 	// Einfügen von Radiobutton zum Selektieren 
																											// von zu editierenden Tuple
											echo '<td class="rot"><input type="radio" id="'. $orteID .'" name="auswaehlen" value="'. $orteID .'">'	;
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

