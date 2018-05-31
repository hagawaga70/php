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

			// 01---> Array mit den Attributen der Relation "Aktivitaeten"
			$attributeAktivitaeten = [							
    			0	=> "an_name",
    			1	=> "bewert",
    			2	=> "bezng",
    			3	=> "art",
    			4	=> "katgrie",
    			5	=> "fabezg",
    			6	=> "ort",
    			7	=> "vorstzg",
    			8	=> "jahrz1",
    			9	=> "jahrz2",
    			10	=> "jahrz3",
    			11	=> "jahrz4",
    			12	=> "mialt",
    			13	=> "dauer",
    			14	=> "akpreis",
    			15	=> "ak_id",
			];


			// 01<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			// 02---> Array mit den Übersetzungen der Attribte  der Relation "Aktivitaeten"
			$spaltenBezeichnerAktivitaeten = [
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
				"akpreis" 	=>	"Prais"			,
				"ak_id" 	=>	"Fahrt"		,
			];
			// 02<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

			$suchFenster = 0;  // Hat die Varibale suchFenster den Wert 1 öffnet sich ein zusätzliches Suchfenster
			
			
			// 03---> Datenbankanbindung  ------------------------------------------------------------------------
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			// 03<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			/*$fahrtSequenceNr = pg_query($db,"SELECT nextval ('fahrtSeq'));*/

			// 04 --->  Verhinderung von Fehlermeldungen ---------------------------------------------------------

			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// 04 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			
			// 05 ---> Erweiterung der SELECT-Anweisung: Abhängig vom Modus -------------------------------------
			
			if 		($_GET['modus'] == 1){					// Zeige alle Datensätze

				$suchfenster = 0;	
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
				
				
			}

			

			// 05 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



			// 06 ---> Sortiert die Datensätze abhängig vom Attribut aufsteigend -------------------------------
			if ($schalter == ""){
					$result = pg_query($db,
											"	SELECT 				ak.ak_id,ak.bewert,ak.bezng,ak.art,ak.katgrie,ak.fabezg,ak.ort,
																	ak.vorstzg,ak.jahrz1,ak.jahrz2,ak.jahrz3,ak.jahrz4,ak.mialt,
																	ak.dauer,ak.akpreis,an.an_name
												FROM 				aktivitaet ak, anbieter an 
												WHERE 				ak.anbietr = an.an_id;
								");
			}elseif( $schalter == "anbieter"){
					$result = pg_query($db,
											"	SELECT 				ak.ak_id,ak.bewert,ak.bezng,ak.art,ak.katgrie,ak.fabezg,ak.ort,
																	ak.vorstzg,ak.jahrz1,ak.jahrz2,ak.jahrz3,ak.jahrz4,ak.mialt,
																	ak.dauer,ak.akpreis,an.an_name
												FROM 				aktivitaet ak
												LEFT OUTER JOIN 	anbieter an 
												ON 					(ak.anbietr = an.an_id)
												ORDER BY			an.an_name;
								");

			}else{
					$result = pg_query($db,
											"	SELECT 				ak.ak_id,ak.bewert,ak.bezng,ak.art,ak.katgrie,ak.fabezg,ak.ort,
																	ak.vorstzg,ak.jahrz1,ak.jahrz2,ak.jahrz3,ak.jahrz4,ak.mialt,
																	ak.dauer,ak.akpreis,an.an_name
												FROM 				aktivitaet ak
												LEFT OUTER JOIN 	anbietr an 
												ON 					(ak.anbieter = an.an_id)
												ORDER BY			ak.".$schalter.";
								");



			}
		// 06 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


			if ($suchfenster == 1){   // Der Link "suche" im Menue "Aktivitaetenen" wurde angeklickt


				// 07 ---> Zunächste werden alle Datensätze angezeigt
				$formSelect = pg_query($db,
											"	SELECT 				ak.ak_id,ak.bewert,ak.bezng,ak.art,ak.katgrie,ak.fabezg,ak.ort,
																	ak.vorstzg,ak.jahrz1,ak.jahrz2,ak.jahrz3,ak.jahrz4,ak.mialt,
																	ak.dauer,ak.akpreis,an.an_name
												FROM 				aktivitaet ak
												LEFT OUTER JOIN 	anbieter an 
												ON 					(ak.anbieter = an.an_id)
								");
				// 07 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
			}	



			echo'	<div id="rahmen_3">';
					echo "<TABLE>";

						// 13 ---> Spaltenkopf/ -bezeichner -----------------------------------------------------------
						echo "<THEAD>";
							echo "<TR>";

								foreach ($spaltenBezeichnerAktivitaeten as $key => $value)	{
									if(array_key_exists('select',$_GET) && $_GET['select']==1){
										echo "<th>". $value ."</th>";
									}else{
										echo "<th>". '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";
									}
								}
							echo "</tr>";
						echo "</THEAD>";
						// 13 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

						echo "<TBODY>";
						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								
								foreach ($attributeAktivitaeten as $value)	{
									if ($row[ $value ] == ''){ 		// Leere Felder werden rosa markiert 
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id=">FÜGE HINZU</a></td>';
									}elseif ($value  == "ak_id"){ 	
										echo '<td>' . '<a href="fahrt.php?modus=4&ak_id="'. $row[$value].'">SHOW</a></td>';
									}elseif($row[ $value ] == f){
										echo "<td>nein</td>";
									}elseif($row[ $value ] == t){
										echo "<td>ja</td>";
									}else{
										echo "<td>". $row[ $value ] ."</td>";
										
									}
								}
							echo "</tr>";
						// 14 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
						}
						echo "</TBODY>";
			echo "</table>";
		?>
		</div>
	</body>
</html>
