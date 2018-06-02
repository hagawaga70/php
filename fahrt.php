<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
		<?php

			require("./navigationsMenue.php");			/*Der ausgelagerte Navigationsblock wird eingefügt*/

			// 01---> Array mit den Attributen der Relation "Fahrt"
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
			// 01<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// 02---> Array mit den Übersetzungen der Attribte  der Relation "Fahrt"
			$spaltenBezeichnerFahrt = [
    			"f_id" 			=> "Fahrt ID"		,
    			"f_name"		=> "Fahrtname"		,
    			"von"			=> "von"			,
    			"bis"			=> "bis"			,
    			"kl_ku"			=> "Klasse_Kurs"	,
    			"f_unterkunft"	=> "Unterkunft"		,
    			"o_name"		=> "Ort"			,
    			"dummy01"		=> "Schueler"		,
    			"dummy02"		=> "Lehrer"			,
			];
			// 02<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			$suchFenster = 0;  // Hat die Variable suchFenster den Wert 1 öffnet sich ein zusätzliches Suchfenster
			
			
			// 03---> Datenbankanbindung  ------------------------------------------------------------------------
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			// 03<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			$fehlermeldung='';
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 1){
				//$insert = "DEINTO fahrt (".$attributeInsert.") VALUES (".$valuesInsert .")"	;
				$delete = "	DELETE FROM fahrt 
							WHERE		f_id="	.	$_POST['loeschen']	.	
							";"	;
				$note = pg_query($db,$delete);
/*														;
				print_r($delete)	;
				echo '<pre>';
				var_dump($_GET)		;
				var_dump($_POST)	;
				echo '</pre>'		;
*/
				if (pg_query($db,$delete)) {
				}else {
				
					$fehlermeldung=pg_last_error($db); 
					print_r(pg_last_error($db)); 
				}
			}			

			// 031 Hinzufügen eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 0){
				$result= pg_query($db,"SELECT nextval ('fahrtSeq')");
				while($row=pg_fetch_assoc($result)){
					$fahrtSequenceNr = $row['nextval'];
				}
				$attributeInsert="";
				$valuesInsert="";
				foreach ($attributeFahrt as $key => $val) {
					if ($key == 0){
						$attributeInsert	= $attributeInsert .$val .							","	;			
						$valuesInsert 		= $valuesInsert .	$fahrtSequenceNr .				","	;
					}elseif ($key <= 3){
						$attributeInsert	= $attributeInsert.$val.							","	;			
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'".		","	;
					}elseif($key==4){
						$attributeInsert	= $attributeInsert.$val									;			
						$valuesInsert 		= $valuesInsert . "'".$_POST[$val]		."'"			;
					}
				}
				$insert = "INSERT INTO fahrt (".$attributeInsert.") VALUES (".$valuesInsert .")"	;
				$note = pg_query($db,$insert)														;
/*
				echo '<pre>';
				print_r($insert);
				var_dump($_GET)		;
				var_dump($_POST)	;
				echo '</pre>'		;
*/
/*
				if (pg_query($db,$insert)) {
					print_r( "Data entered successfully. ");
				}else {
					print_r( "Data entry unsuccessful. ");
					print_r(pg_last_error($db)); 
				}
*/
			}
			// 031 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// 032 Editieren/Ändern eines Datensatzes ------------------------------------------------------------------------
			if(	array_key_exists('action',$_POST) && $_POST["action"] == 3){		//UPDATE
				$attributeUpdate="";
				$valuesUpdate="";
				foreach ($attributeFahrt as $key => $val) {
					if ($key == 0){
					}elseif ($key <= 3){
						$attributeUpdate	= $attributeUpdate.$val.							","	;			
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'".		","	;
					}elseif($key==4){
						$attributeUpdate	= $attributeUpdate.$val									;			
						$valuesUpdate 		= $valuesUpdate . "'".$_POST[$val]		."'"			;
					}
				}



				$update = "	UPDATE 	fahrt 
							SET 	(".$attributeUpdate.") 
							= 		(".$valuesUpdate .")
							WHERE	f_id=" .$_POST['f_id']. "
							;"	;
				$note = pg_query($db,$update)	;

				if (pg_query($db,$update)) {
				}else {
					print_r( "Data entry unsuccessful. ");
					print_r(pg_last_error($db)); 
				}
			}
			// 032 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}elseif (array_key_exists('select', $_GET) && array_key_exists('f_id',$_GET) ){ // Aufruf von fahrt.php durch schueler.php
				$where = "WHERE f.f_id =".$_GET['f_id'];
			}

			// 04 --->  Verhinderung von Fehlermeldungen ---------------------------------------------------------

			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// 04 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			// 05 ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
			
			if 		($_GET['modus'] == 1){					// Zeige alle Datensätze

				$where = '';

			}elseif ($_GET['modus'] == 2){					// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster = 1;	
				$where = '';
				
			}elseif ($_GET['modus'] == 3){					// Öffne Suchfenster und zeigt die selektierten Datensätze

				$suchfenster = 1									;
				if (is_numeric($_POST["f_id_input"])){
					$f_id = $_POST["f_id_input"]					;
				}else{
					$f_id = $_POST["f_id"]							;
				}
				$where = 'WHERE f_id'.$_POST["f_id_operator"] . $f_id;
				
				
			}elseif ($_GET['modus'] == 4){					//	Das Skript fahrt.php wurde vom Skript aktivitaeten au
															//  aufgerufen. fahrt.php zeigt jetzt nur die fahrten zur
															//  übergebenen aktivitaeten_id an. 

				$suchfenster = 0									;
				$where = 'WHERE f.f_id IN (
											SELECT 		f_id 
											FROM 		wirdangeboten 
											WHERE		ak_id ='. $_GET["ak_id"].'
										)';
				echo '<input id="ak_id" name="ak_id" type="hidden" value="'.$_GET["ak_id"].'">';	
				
			}elseif ($_GET['modus'] == 5){				

				$suchfenster = 0	;
				$where = ''			;
				
			}



			

			// 05 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// 06 ---> Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
			switch ($schalter) {
				case "f_id":
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
				case "f_name":
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
				case "von":
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
				case "bis":
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
				case "kl_ku":
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
				case "f_unterkunft":
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
				case "o_name":
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

				default:
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
			// 06 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			if ($suchfenster == 1){   // Der Link "suche" im Menue "Fahrten" wurde angeklickt


				// 07 ---> Zunächste werden alle Datensätze angezeigt
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
	


				$index=0;

				// 08 ---> Erstellen eines Arrays mit den f_id's . Die ID's werden für das Select-Menü benötigt

				while($row=pg_fetch_assoc($formSelect)){
					$fahrtID[$index] = $row[$attributeFahrt[0]]		;
					$index++										;
				}
				// 08 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				$operator[0]=">"	;
				$operator[1]=">="	;
				$operator[2]="="	;
				$operator[3]="<="	;
				$operator[4]="<"	;


				$verknuepfung[0] ="AND";
				$verknuepfung[1] ="OR";

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
				// 10 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

				// 11 ---> Manuelle Eingabe der gesuchten f_id --------------------------------------------------
				if (array_key_exists('f_id_input',$_POST) && $_POST['f_id_input'] != ''){
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" value='.$_POST['f_id_input'].'>'	; // Voreinstellung s.o	
				}else{
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" >'									;	
				}
				// 11 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	
				// 12 ---> Auswahlmenü Verknüpfungsart -----------------------------------------------------------
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
				// 12 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
				echo '		</label>'																	;
				echo '		<button type="submit" name="action" value="0">Suche</button>'				;
				echo '	</form>'																		;
				echo '</div>'																			;		
			}


			echo'	<div id="rahmen_3">';
			
					if(	array_key_exists('modus',$_GET) && $_GET['modus'] ==6) {
						echo '<form name="delete" action="fahrt.php" method="POST" >';
						echo	'<fieldset>'	;
					}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7) {
						echo '<form name="edit" action="fahrt.php" method="POST" >';
						echo	'<fieldset>'	;
					}


					echo "<table>";

							// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
							echo "<tr>";

								foreach ($spaltenBezeichnerFahrt as $key => $value)	{
									if(array_key_exists('select',$_GET) && $_GET['select']==1){
										echo '<th class="grau">'. $value ."</th>";
									}elseif(array_key_exists('modus',$_GET) && $_GET['modus']==4){
										echo '<th class="grau">'. $value ."</th>";
									}else{
										echo '<th class="grau">' . '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";
									}
								}
							
							if(	array_key_exists('modus',$_GET) && $_GET['modus'] ==6) {
								echo '<td class="rot"><button type="submit" name="action" value="1">DELETE</button></td>'	;
							}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] ==7) {
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
													");

								$SpeicherDefaultWerte= [
										"f_id" 			=> "1"		,
										"f_name"		=> "2"		,
										"von"			=> "3"		,
										"bis"			=> "4"		,
										"kl_ku"			=> "5"		,
										"f_unterkunft"	=> "6"		,
									];

								$row=pg_fetch_assoc($defaultValuesEdit);

								foreach ($SpeicherDefaultWerte as $key => $val) {
									$SpeicherDefaultWertePuffer[$key] = $row[$key];
								}

								$date3_default = $SpeicherDefaultWertePuffer["von"]; 
								$date4_default = $SpeicherDefaultWertePuffer["bis"]; 

							}		 
							
						echo '<tr>';
						echo 	'<form name="insert" action="fahrt.php" method="POST" >'													;
						echo		'<td	class= "gelb">'																					;
						echo 			'<input id="f_id" name="f_id" type="hidden" value="'. $SpeicherDefaultWertePuffer['f_id'] . '">'	;	
						echo 		'</td>'																									;
						echo 		'<td	class= "gelb">'																					;
						echo 			'<input type="text" name="f_name" size="6" value="' .$SpeicherDefaultWertePuffer['f_name']. '"/>'	;
						echo 		'</td>'																									;
						echo 		'<td class= "gelb">'																					;			


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
						echo 			'<input type="text" name="kl_ku" size="1" value="' .$SpeicherDefaultWertePuffer['kl_ku']. '"/>'		;
						echo 		'</td>'																								;
						echo 		'<td	class= "gelb" colspan="4">'																	;
						
						if(	(array_key_exists(		'modus',$_GET) 		&& $_GET['modus'] 		== 5)){
								echo 			'<button type="submit" name="action" value="0">ADD</button>	'							;
						}elseif((array_key_exists(	'action',$_POST) 	&& $_POST["action"] 	== 2)){
								echo 			'<button type="submit" name="action" value="3">Update</button>	'						;
						}
						echo	 	'</td>'																								;
						echo 	'</form>'																								;	
						echo '</tr>'																									;
					}

						// 14 ---> Zeigt die Datensätze in einer Tabelle an--------------------------------------------------
					
						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								$counter=0;	
								foreach ($attributeFahrt as $value)	{
									if($value == 'f_id') {
										$fahrtID = $row[ $value ]; 
									}
									if (($row[ $value ] == '' )&&( $counter==5)){ 		// Leere Felder werden rosa markiert 
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id='.$fahrtID.'">FÜGE HINZU</a></td>';
									}elseif( $value == "dummy01"){
										echo '<td>' . '<a href="addShowSchuler.php?sort=&f_id='.$fahrtID.'">Show/Add</a></td>';
									}elseif( $value == "dummy02"){
										echo '<td>' . '<a href="addShowLehrer.php?sort=&f_id='.$fahrtID.'">Show/Add</a></td>';
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
						
									
									$counter++;
								}
								if(	array_key_exists('modus',$_GET) && $_GET['modus'] == 6) {
										echo '<td class="rot"><input type="radio" id="'. $fahrtID .'" name="loeschen" value="'. $fahrtID .'">'	;
								}elseif(	array_key_exists('modus',$_GET) && $_GET['modus'] == 7) {
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

