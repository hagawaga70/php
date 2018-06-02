<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
		<?php
					
			// POST action 1   Entfernen eines Datensatzes
			// POST action 0   Hinzufügen eines Datensatzes
			// GET  modus	5	Hinzufügen eines Datensatzes
			// POST action 3	Editieren eines Datensatzes
			// GET  modus  1 	Zeige alle Datensätze
			// GET  modus  2 	Öffnet das Suchfenster und zeigt  alle Datensätze
			// GET  modus  3 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze
			// POST action 5 	Öffnet das Suchfenster und zeigt  alle selektierten Datensätze  // action und der Wert 5 werden nirgends weiter abgefragt
			// GET  modus  4   lehrer.php wird von aktivitaeten aufgerufen und zeigt alle Lehreren zu einer bestimmten Aktivität an 	


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
    			"l_id" 			=> "Lehrer"			,
    			"anrede"		=> "Anrede"			,
    			"vname"			=> "Vorname"		,
    			"nname"			=> "Nachname"		,
    			"telnr"			=> "Telefonnummer"	,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			$suchFenster = 0;  // Hat die Variable suchFenster den Wert 1 öffnet sich ein zusätzliches Suchfenster
			
			
			// ---> Datenbankanbindung  ------------------------------------------------------------------------
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// ---> Löschen eines Datensatzes------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

				$delete = "	DELETE FROM lehrer 
							WHERE		l_id="	.	$_POST['loeschen']	.	
							";"	;
				if (pg_query($db,$delete)) {
				}else {
				
					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
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
			
			if 		($_GET['modus'] == 1){					// Zeige alle Datensätze

				$where = '';								// -- kein WHERE-Clause 	

			}elseif ($_GET['modus'] == 2){					// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster 	= 1		;					// 1 -> Suchfenster wird geöffnet
				$where 			= ''	;					// -- kein WHERE-Clause 	
				
			}elseif ($_GET['modus'] == 3){					// Öffne Suchfenster und zeigt die selektierten Datensätze

				$suchfenster = 1										;	// 1 -> Suchfenster wird geöffnet
				if (is_numeric($_POST["l_id_input"])){						// Anführungsstriche werden entfernt
					$l_id = $_POST["l_id_input"]						;	// ???
				}else{
					$l_id = $_POST["l_id"]								;
				}
				$where = 'WHERE l_id'.$_POST["l_id_operator"] . $l_id	;	// Erstellen des WHERE-Clause -> WHERE l_id [>|>=|= usw]
				
				
			}elseif ($_GET['modus'] == 4){					//	Das Skript lehrer.php wurde vom Skript aktivitaeten au
															//  aufgerufen. lehrer.php zeigt jetzt nur die lehreren zur
															//  übergebenen aktivitaeten_id an. 

				$suchfenster = 0									;
				$where = 'WHERE f.l_id IN (
											SELECT 		l_id 
											FROM 		wirdangeboten 
											WHERE		ak_id ='. $_GET["ak_id"].'
										)';															// Erstellen des WHERE-CLAUSE zur SELECT-ABFRAGE
				echo '<input id="ak_id" name="ak_id" type="hidden" value="'.$_GET["ak_id"].'">';	// Die ak_id wird versteckt per GET weitergegeben
				
			}elseif ($_GET['modus'] == 5){					// Hinzufügen eines Datensatzes			

				$suchfenster = 0	;						// Es wird kein Suchfenster geöffnet
				$where = ''			;						// Alle Datensätze werden angezeigt
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


			if ($suchfenster == 1){   // Der Link "suche/Search" im Menue "Lehreren" wurde angeklickt


				// ---> Zunächste werden alle Datensätze angezeigt -----------------------------------------------

				$formSelect = pg_query($db,
											"	SELECT 				l.l_id, l.anrede, l.vname, l.nname,l.telnr
												FROM 				lehrer l 
												;
								");

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	


				$index=0;				// Zählt die Anzahl der Datensätze 

				// ---> Erstellen eines Arrays mit den l_id's . Die ID's werden für das Select-Menü benötigt

				while($row=pg_fetch_assoc($formSelect)){
					$lehrerID[$index] = $row[$attributeLehrer[0]]		;
					$index++										;
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				$operator[0]=">"	;		// Array mit unterschiedlichen Operatoren für das Suchabfrage
				$operator[1]=">="	;		// |
				$operator[2]="="	;		// |
				$operator[3]="<="	;		// |
				$operator[4]="<"	;		// |


				$verknuepfung[0] ="AND";	// Array mit unterschiedlichen Verknüpfungen unterschiedlicher Suchanfrage (NICHT UMGESETZT)
				$verknuepfung[1] ="OR";		// |

				asort($lehrerID);	// Sortierung der ID's



				
				echo'	<div id="suche">'																;
				echo		'<form action="./lehrer.php?modus=3" method="post" autocomplete="off">'		;   // Such-Formular l_id
				echo			'<label>LehrerID:'														;

				
				// 09 ---> Auswahlmenü Operatoren --------------------------------------------------------------
				echo			'<select name="l_id_operator">'											;
				echo '				<option>---				</option>'									;
				foreach ($operator as $key => $val) {	
					if (array_key_exists('l_id_operator',$_POST) && $_POST['l_id_operator']== $val){
						echo '				<option selected >'.$val.'</option>'						;  	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option >'.$val.'</option>'									;
					}
				}
				echo '			</select>'																;	
				// 09 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// 10 ---> Auswahlmenü l_id's -------------------------------------------------------------------
				echo			'<select name="l_id">'													;
				echo '				<option>---</option>'												;

				foreach ($lehrerID as $key => $val) {	
					if (array_key_exists('l_id',$_POST) && $_POST['l_id']== $val){
						echo '				<option selected >'.$val.'</option>'						;	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option>'.$val.'</option>'									;
					}
				}
				
				echo '			</select>'																;

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// ---> Manuelle Eingabe der gesuchten l_id --------------------------------------------------
				if (array_key_exists('l_id_input',$_POST) && $_POST['l_id_input'] != ''){
					echo '			<input id="l_id_input" name="l_id_input" maxlength="5" size="5" value='.$_POST['l_id_input'].'>'	; // Voreinstellung s.o	
				}else{
					echo '			<input id="l_id_input" name="l_id_input" maxlength="5" size="5" >'									;	
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	
				// ---> Auswahlmenü Verknüpfungsart -----------------------------------------------------------
				echo			'<select name="l_id_verknuepfung">'												;
				echo '				<option>---				</option>'											;
				foreach ($verknuepfung as $key => $val) {	
					if (array_key_exists('l_id_verknuepfung',$_POST) && $_POST['l_id_verknuepfung']== $val){
						echo '				<option selected >'.$val.'</option>'								;
					}else{
						echo '				<option>'.$val.'</option>'											;
					}
				}

				echo '			</select>'																		;
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
				echo '		</label>'																	;
				echo '		<button type="submit" name="action" value="5">Suche</button>'				;
				echo '	</form>'																		;
				echo '</div>'																			;		
			}


			echo'	<div id="rahmen_3">';
			
					if(	array_key_exists('modus',$_GET) && $_GET['modus'] ==6) {				// Der Delete-Button wird angehängt
						echo '<form name="delete" action="lehrer.php" method="POST" >'	;		// Das fieldset wird für die Radio-Button benötigt
						echo	'<fieldset>'											;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7) {		// Der Edit-Button wird angehängt
						echo '<form name="edit" action="lehrer.php" method="POST" >'		;
						echo	'<fieldset>'											; 		// Das fieldset wird für den Edit-Button benötigt
					}


					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerLehrer as $key => $value)	{					// Spaltenkopfbezeichner mit und ohne Link
									if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau" colspan="3">'. $value ."</th>";		// |
										}elseif($key == "l_id"){
											echo '<th class="grau" colspan="3">'. $value ."</th>";		// |
										}else{
											echo '<th class="grau">'. $value ."</th>";					// |
										}
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau colspan="3"">'. $value ."</th>";		// |
										}elseif ($key == "l_id"){
											echo '<th class="grau colspan="3"">'. $value ."</th>";		// |
										}else{
											echo '<th class="grau">'. $value ."</th>";					// |
										}
									}else{																// |
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau" colspan="3">' . '<a href="lehrer.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}elseif($key == "l_id"){
											echo '<th class="grau" colspan="3">' . '<a href="lehrer.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}else{
											echo '<th class="grau" >' . '<a href="lehrer.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}
									}
								}
							
							if(	array_key_exists('modus',$_GET) && $_GET['modus'] ==6) {				// Anfügen des Delete-Button
								echo '<td class="rot"><button type="submit" name="action" value="1">DELETE</button></td>'	;
							}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7) {		// Anfügen des Edit-Button
								echo '<td class="rot"><button type="submit" name="action" value="2">EDIT</button></td>'	;
							}		
							echo "</tr>";
							// 13 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

						if(	(array_key_exists('modus',$_GET) 	&& $_GET['modus'] 		== 5) ||		// ADD
							(array_key_exists('action',$_POST) 	&& $_POST["action"] 	== 2)			// Edit
						  )  {
							define("L_LANG", "de_DE"); // Sprachauswahl für die Kalenderfuntion
							require('calendar/tc_calendar.php');
							
							if	(array_key_exists('action',$_POST) && $_POST["action"] 	== 2){			//Edit 

								$defaultValuesEdit = pg_query($db,
														"	SELECT 		f.l_id, f.f_name, f.von, f.bis, f.kl_ku, f.f_unterkunft 
															FROM 		lehrer f 
															WHERE		f.l_id=" . $_POST['editieren']. "		
															;
													");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

								$SpeicherDefaultWerte= [							// Array zum Speichern der Defaultwerte der Formularlemente (Editieren)
										"l_id" 			=> ""		,
										"f_name"		=> ""		,
										"von"			=> ""		,
										"bis"			=> ""		,
										"kl_ku"			=> ""		,
										"f_unterkunft"	=> ""		,
									];

								$row=pg_fetch_assoc($defaultValuesEdit);

								foreach ($SpeicherDefaultWerte as $key => $val) { 		
									$SpeicherDefaultWertePuffer[$key] = $row[$key];			// Ablegen der Defaultwerte in einem Pufferarray
								}

								$date3_default = $SpeicherDefaultWertePuffer["von"]; 		// Zuweisen eines Defaultwert für das Attribut "von"
								$date4_default = $SpeicherDefaultWertePuffer["bis"]; 		// Zuweisen eines Defaultwert für von Attribut "bis"

							}		 
							
						echo '<tr>';
						echo 	'<form name="insert" action="lehrer.php" method="POST" >'													;
						echo		'<td	class= "gelb" colspan="3">'																					;
						echo 			'<input id="l_id" name="l_id" type="hidden" value="'. $SpeicherDefaultWertePuffer['l_id'] . '">'	; // Versteckte l_id	
						echo 		'</td>'																									;
						echo 		'<td	class= "gelb">'																					;
						echo 			'<input type="text" name="f_name" size="6" value="' .$SpeicherDefaultWertePuffer['f_name']. '"/>'	; // Input-Field &
						echo 		'</td>'																									; // Defaultwert
						echo 		'<td class= "gelb">'																					;			

						// Einfügen einer Kalenderklasse. Damit kann grafisch das Datum eingegeben werden.Hier für das Attribut von
												$myCalendar = new tc_calendar("von", true, false)			;
												$myCalendar->setIcon("calendar/images/iconCalendar.gif")	;
												$myCalendar->setDate(date('d', strtotime($date3_default))
													, date('m', strtotime($date3_default))
													, date('Y', strtotime($date3_default)))					;
												$myCalendar->setPath("calendar/")							;
												$myCalendar->setYearInterval(2000, 2020)					;
												$myCalendar->setAlignment('left', 'bottom')					;
												//$myCalendar->setDatePair('date3', 'date4', $date4_default)	;
												$myCalendar->writeScript()									;	  

						echo 		'</td>'					;
						echo 		'<td class= "gelb">'	;

						// Einfügen einer Kalenderklasse. Damit kann grafisch das Datum eingegeben werden.Hier für das Attribut bis
														$myCalendar = new tc_calendar("bis", true, false)			;
														$myCalendar->setIcon("calendar/images/iconCalendar.gif")	;
														$myCalendar->setDate(date('d', strtotime($date4_default))
															, date('m', strtotime($date4_default))
															, date('Y', strtotime($date4_default)))					;
														$myCalendar->setPath("calendar/")							;
														$myCalendar->setYearInterval(2000, 2020)					;
														$myCalendar->setAlignment('left', 'bottom')					;
														//$myCalendar->setDatePair('date3', 'date4', $date4_default)	;
														$myCalendar->writeScript()									;	  
														
						echo 		'</td>'																								;
						echo 		'<td	class= "gelb">'																				;
						echo 			'<input type="text" name="kl_ku" size="1" value="' .$SpeicherDefaultWertePuffer['kl_ku']. '"/>' ; // Inputfield "kl_ku"
						echo 		'</td>'																								; // & Defaultwert
						echo 		'<td	class= "gelb" colspan="8">'																	;
						
						if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 5)){
								echo 			'<button type="submit" name="action" value="0">ADD</button>	'							; 	// Einfügen des ADD
						}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){										// ODER
								echo 			'<button type="submit" name="action" value="3">Update</button>	'						;	// Update-Button
						}
						echo	 	'</td>'																								;
						echo 	'</form>'																								;	
						echo '</tr>'																									;
					}

						// 14 ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
					
						while($row=pg_fetch_assoc($result)){					// Anzeigen der Datensätze
							echo "<tr>";
								$counter=0;	
								foreach ($attributeLehrer as $value)	{
									if($value == 'l_id') {						// Zum Aufrufen andere Skripte wird die l_id benötigt
										$lehrerID = $row[ $value ]; 
									}
									
									if (($value != 'dummy01') &&($value != 'dummy02') && ($row[ $value ] == '' )&&( $counter==5)){ // Leere Felder werden rosa markiert 
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&l_id='.$lehrerID.'">&#x2795;</a></td>';
										//echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&l_id='.$lehrerID.'">ADD</a></td>';
									}elseif( $value == "dummy01"){
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&l_id='.$lehrerID.'">&#x1f441;</a></td>';	// Link
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&l_id='.$lehrerID.'">&#x2795;</a></td>'	; 	// Link
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&l_id='.$lehrerID.'">&#x2796;</a></td>';  	// Link
									}elseif( $value == "dummy02"){
										echo '<td class="grau">' . '<a href="addShowLehrer.php?sort=&l_id='.$lehrerID.'">&#x1f441;</a></td>';	// Link
										echo '<td class="grau">' . '<a href="addShowLehrer.php?sort=&l_id='.$lehrerID.'">&#x2795;</a></td>';		// Link
										echo '<td class="grau">' . '<a href="addShowLehrer.php?sort=&l_id='.$lehrerID.'">&#x2796;</a></td>';		// Link
									}elseif( $value == 'l_id'){
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&l_id='.$lehrerID.'">&#x1f441;</a></td>'; // Link
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&l_id='.$lehrerID.'">&#x2795;</a></td>';  // Link
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&l_id='.$lehrerID.'">&#x2796;</a></td>';  // Link
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
						
									
									$counter++;
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 6) {	// Einfügen von Radiobutton zum Selektieren 
																								// von zu löschenden  tuple
										echo '<td class="rot"><input type="radio" id="'. $lehrerID .'" name="loeschen" value="'. $lehrerID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $lehrerID .'" name="editieren" value="'. $lehrerID .'">'	;
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

