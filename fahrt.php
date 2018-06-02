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
			// GET  modus  4   fahrt.php wird von aktivitaeten aufgerufen und zeigt alle Fahrten zu einer bestimmten Aktivität an 	


			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			// ---> Array mit den Attributen der Relation "Fahrt" -----------------------------------------------
			$attributeFahrt = [							
    			0	=> "f_id"		,
    			1	=> "f_name"		,
    			2	=> "von"		,
    			3	=> "bis"		,
    			4	=> "kl_ku"		,
    			5	=> "u_name"		,
    			6	=> "o_name"		,
    			7	=> "dummy01"	,
    			8	=> "dummy02"	,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// ---> Array mit den Übersetzungen der Attribte  der Relation "Fahrt"------------------------------->
			$spaltenBezeichnerFahrt = [
    			"f_id" 			=> "Aktivitäten"	,
    			"f_name"		=> "Fahrtname"		,
    			"von"			=> "von"			,
    			"bis"			=> "bis"			,
    			"kl_ku"			=> "Klasse_Kurs"	,
    			"f_unterkunft"	=> "Unterkunft"		,
    			"o_name"		=> "Ort"			,
    			"dummy01"		=> "Schueler"		,
    			"dummy02"		=> "Lehrer"			,
			];
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			$suchFenster = 0;  // Hat die Variable suchFenster den Wert 1 öffnet sich ein zusätzliches Suchfenster
			
			
			// ---> Datenbankanbindung  ------------------------------------------------------------------------
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


						
			// ---> Löschen eines Datensatzes------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){

				$delete = "	DELETE FROM fahrt 
							WHERE		f_id="	.	$_POST['loeschen']	.	
							";"	;
				if (pg_query($db,$delete)) {
				}else {
				
					print_r(pg_last_error($db));				//Eine Fehlermeldung wird im Browser angezeigt 
				}
			}			
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			// Hinzufügen eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 0){

				$result= pg_query($db,"SELECT nextval ('fahrtSeq')");   // Eine neue ID für den Datensatz wird geliefert
				while($row=pg_fetch_assoc($result)){					// |
					$fahrtSequenceNr = $row['nextval'];					// |
				}														// |


				$attributeInsert="";	//  Die Attribute für das INSERT-Statement werden hier abgespeichert
				$valuesInsert="";		//  Die Werte der einzelnen Attribute werden hier abgespeichert
				foreach ($attributeFahrt as $key => $val) {
					if ($key == 0){
						$attributeInsert	= $attributeInsert .$val .							","	;  	// ...., attribute1, attribute2, ...			
						$valuesInsert 		= $valuesInsert .	$fahrtSequenceNr .				","	;	// Anhängen der Werte: Hier die neue ID	
					}elseif ($key <= 3){
						$attributeInsert	= $attributeInsert.$val.							","	;	// Anhängen mit , als Separationszeichen
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'".		","	;   // | 
					}elseif($key==4){
						$attributeInsert	= $attributeInsert.$val									;	// Letztes/r Attribut/Wert daher ohne Komma	
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'"			;   // anhängen
					}
				}
				$insert = "INSERT INTO fahrt (".$attributeInsert.") VALUES (".$valuesInsert .")"	;	// Erstellen des gesamten INSERT-Statement

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
				foreach ($attributeFahrt as $key => $val) {
					if ($key == 0){													// Bei key == 0 keine Einträge, das die ID nicht verändert wird
					}elseif ($key <= 3){
						$attributeUpdate	= $attributeUpdate.$val.							","	;	// Attribte  kommasepariert		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'".		","	;	// Values kommasepariert
					}elseif($key==4){
						$attributeUpdate	= $attributeUpdate.$val									;	// Letztes/r Attribute/Value ohne Komma		
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'"			;	// |
					}
				}



				$update = "	UPDATE 	fahrt 									
							SET 	(".$attributeUpdate.") 
							= 		(".$valuesUpdate .")
							WHERE	f_id=" .$_POST['f_id']. "
							;"	;											// Zusammenstellen des Update-Statement	
				$note = pg_query($db,$update)	;							// Ausführen des UPDATE-Statement	

				if (pg_query($db,$update)) {
				}else {
					print_r( "Data entry unsuccessful. ");					// Im Falle eines Fehlers erscheint im Brower eine Fehlermeldung
					print_r(pg_last_error($db)); 							// |
				}
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			//ACHTUNG: Hier wird nur ein Datensatz geliefert, auch wenn der Schüler an mehreren Fahrten teilgenommen hat

			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}elseif (array_key_exists('select', $_GET) && array_key_exists('f_id',$_GET) ){ // Aufruf von fahrt.php durch schueler.php
				$where = "WHERE f.f_id =".$_GET['f_id'];
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
				if (is_numeric($_POST["f_id_input"])){						// Anführungsstriche werden entfernt
					$f_id = $_POST["f_id_input"]						;	// ???
				}else{
					$f_id = $_POST["f_id"]								;
				}
				$where = 'WHERE f_id'.$_POST["f_id_operator"] . $f_id	;	// Erstellen des WHERE-Clause -> WHERE f_id [>|>=|= usw]
				
				
			}elseif ($_GET['modus'] == 4){					//	Das Skript fahrt.php wurde vom Skript aktivitaeten au
															//  aufgerufen. fahrt.php zeigt jetzt nur die fahrten zur
															//  übergebenen aktivitaeten_id an. 

				$suchfenster = 0									;
				$where = 'WHERE f.f_id IN (
											SELECT 		f_id 
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
				case "f_id":					// Sortiert nach f_id
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			f.f_id;
								");
					break;
				case "f_name":					// Sortiert nach f_name
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			f.f_name;
								");
					break;
				case "von":					 	// Sortiert nach von
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			f.von;
								");
					break;
				case "bis":						// Sortiert nach bis
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			f.bis;
								");
					break;
				case "kl_ku":					// Sortiert nach kl_ku
					$result = 	pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			f.kl_ku;
								");
					break;
				case "f_unterkunft":			// Sortiert nach f_unterkunft
					$result = 	pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			u.u_name;
								");
					break;
				case "o_name":					// Sortiert nach o_name
					$result = 	pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name ,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												ORDER BY			o.o_name;
								");
					break;

				default:					// Standardabfrage mit keinem oder unterschiedlichem WHERE-Clause
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name,o.o_name
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												".$where."
												;
								");
			}
			// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			if ($suchfenster == 1){   // Der Link "suche/Search" im Menue "Fahrten" wurde angeklickt


				// ---> Zunächste werden alle Datensätze angezeigt -----------------------------------------------

				$formSelect = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name, o.o_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												LEFT OUTER JOIN     ort o 
												ON                  (u.u_ort = o.o_id)
												;
								");
				// 07 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
	


				$index=0;				// Zählt die Anzahl der Datensätze 

				// ---> Erstellen eines Arrays mit den f_id's . Die ID's werden für das Select-Menü benötigt

				while($row=pg_fetch_assoc($formSelect)){
					$fahrtID[$index] = $row[$attributeFahrt[0]]		;
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

				asort($fahrtID);	// Sortierung der ID's



				
				echo'	<div id="suche">'																;
				echo		'<form action="./fahrt.php?modus=3" method="post" autocomplete="off">'		;   // Such-Formular f_id
				echo			'<label>FahrtID:'														;

				
				// 09 ---> Auswahlmenü Operatoren --------------------------------------------------------------
				echo			'<select name="f_id_operator">'											;
				echo '				<option>---				</option>'									;
				foreach ($operator as $key => $val) {	
					if (array_key_exists('f_id_operator',$_POST) && $_POST['f_id_operator']== $val){
						echo '				<option selected >'.$val.'</option>'						;  	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option >'.$val.'</option>'									;
					}
				}
				echo '			</select>'																;	
				// 09 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// 10 ---> Auswahlmenü f_id's -------------------------------------------------------------------
				echo			'<select name="f_id">'													;
				echo '				<option>---</option>'												;

				foreach ($fahrtID as $key => $val) {	
					if (array_key_exists('f_id',$_POST) && $_POST['f_id']== $val){
						echo '				<option selected >'.$val.'</option>'						;	// Nach der Suche werden die Suchparameter
					}else{																					// automatisch voreingestellt
						echo '				<option>'.$val.'</option>'									;
					}
				}
				
				echo '			</select>'																;

				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



				// ---> Manuelle Eingabe der gesuchten f_id --------------------------------------------------
				if (array_key_exists('f_id_input',$_POST) && $_POST['f_id_input'] != ''){
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" value='.$_POST['f_id_input'].'>'	; // Voreinstellung s.o	
				}else{
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" >'									;	
				}
				// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	
				// ---> Auswahlmenü Verknüpfungsart -----------------------------------------------------------
				echo			'<select name="f_id_verknuepfung">'												;
				echo '				<option>---				</option>'											;
				foreach ($verknuepfung as $key => $val) {	
					if (array_key_exists('f_id_verknuepfung',$_POST) && $_POST['f_id_verknuepfung']== $val){
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
						echo '<form name="delete" action="fahrt.php" method="POST" >'	;		// Das fieldset wird für die Radio-Button benötigt
						echo	'<fieldset>'											;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7) {		// Der Edit-Button wird angehängt
						echo '<form name="edit" action="fahrt.php" method="POST" >'		;
						echo	'<fieldset>'											; 		// Das fieldset wird für den Edit-Button benötigt
					}


					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerFahrt as $key => $value)	{					// Spaltenkopfbezeichner mit und ohne Link
									if(array_key_exists('select',$_GET) && $_GET['select']==1){			// zum Sortieren der Datensätze
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau" colspan="3">'. $value ."</th>";		// |
										}elseif($key == "f_id"){
											echo '<th class="grau" colspan="3">'. $value ."</th>";		// |
										}else{
											echo '<th class="grau">'. $value ."</th>";					// |
										}
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){		// |
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau colspan="3"">'. $value ."</th>";		// |
										}elseif ($key == "f_id"){
											echo '<th class="grau colspan="3"">'. $value ."</th>";		// |
										}else{
											echo '<th class="grau">'. $value ."</th>";					// |
										}
									}else{																// |
										if($key == "dummy01" || $key == "dummy02"){
											echo '<th class="grau" colspan="3">' . '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}elseif($key == "f_id"){
											echo '<th class="grau" colspan="3">' . '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";	// |
										}else{
											echo '<th class="grau" >' . '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";	// |
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
														"	SELECT 		f.f_id, f.f_name, f.von, f.bis, f.kl_ku, f.f_unterkunft 
															FROM 		fahrt f 
															WHERE		f.f_id=" . $_POST['editieren']. "		
															;
													");								// SELECT-Anfrage für die Defaultwerte der Formularelemente (Editieren)

								$SpeicherDefaultWerte= [							// Array zum Speichern der Defaultwerte der Formularlemente (Editieren)
										"f_id" 			=> ""		,
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
						echo 	'<form name="insert" action="fahrt.php" method="POST" >'													;
						echo		'<td	class= "gelb" colspan="3">'																					;
						echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $SpeicherDefaultWertePuffer['f_id'] . '">'	; // Versteckte f_id	
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
								foreach ($attributeFahrt as $value)	{
									if($value == 'f_id') {						// Zum Aufrufen andere Skripte wird die f_id benötigt
										$fahrtID = $row[ $value ]; 
									}
									
									if (($value != 'dummy01') &&($value != 'dummy02') && ($row[ $value ] == '' )&&( $counter==5)){ // Leere Felder werden rosa markiert 
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id='.$fahrtID.'">&#x2795;</a></td>';
										//echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id='.$fahrtID.'">ADD</a></td>';
									}elseif( $value == "dummy01"){
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&f_id='.$fahrtID.'">&#x1f441;</a></td>';	// Link
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&f_id='.$fahrtID.'">&#x2795;</a></td>'	; 	// Link
										echo '<td class="hellgrau">' . '<a href="addShowSchuler.php?sort=&f_id='.$fahrtID.'">&#x2796;</a></td>';  	// Link
									}elseif( $value == "dummy02"){
										echo '<td class="grau">' . '<a href="lehrer.php?sort=&f_id='.$fahrtID.'">&#x1f441;</a></td>';	// Link
										echo '<td class="grau">' . '<a href="lehrer.php?modus=8&f_id='.$fahrtID.'">&#x2795;</a></td>';		// Link
										echo '<td class="grau">' . '<a href="lehrer.php?sort=&f_id='.$fahrtID.'">&#x2796;</a></td>';		// Link
									}elseif( $value == 'f_id'){
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&f_id='.$fahrtID.'">&#x1f441;</a></td>'; // Link
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&f_id='.$fahrtID.'">&#x2795;</a></td>';  // Link
										echo '<td class="hellgrau">' . '<a href="unterkunft.php?modus=1&f_id='.$fahrtID.'">&#x2796;</a></td>';  // Link
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
						
									
									$counter++;
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 6) {	// Einfügen von Radiobutton zum Selektieren 
																								// von zu löschenden  tuple
										echo '<td class="rot"><input type="radio" id="'. $fahrtID .'" name="loeschen" value="'. $fahrtID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7) { 	// Einfügen von Radiobutton zum Selektieren 
																										// von zu editierenden Tuple
										echo '<td class="rot"><input type="radio" id="'. $fahrtID .'" name="editieren" value="'. $fahrtID .'">'	;
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

