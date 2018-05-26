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
			$attributeFahrt = [							
    			0	=> "f_id",
    			1	=> "f_name",
    			2	=> "von",
    			3	=> "bis",
    			4	=> "kl_ku",
    			5	=> "u_name",
			];
			$spaltenBezeichnerFahrt = [
    			"f_id" 			=> "Fahrt ID",
    			"f_name"		=> "Fahrtname",
    			"von"			=> "von",
    			"bis"			=> "bis",
    			"kl_ku"			=> "Klasse_Kurs",
    			"f_unterkunft"	=> "Unterkunft",
			];
			$suchFenster = 0;
			
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			/*$fahrtSequenceNr = pg_query($db,"SELECT nextval ('fahrtSeq'));*/
			if(	!array_key_exists('select',$_GET) ){
				$where = "";
			}
			elseif (array_key_exists('select', $_GET) && array_key_exists('f_id',$_GET) ){
				$where = "WHERE f.f_id =".$_GET['f_id'];
			}
			// START 01: Verhinderung von Fehlermeldungen ---------------------------------------------------------------------->	

			if(array_key_exists('sort',$_GET)){
				$schalter = $_GET['sort'];
			}else{
				$schalter="";
			}

			// STOP 01 <---------------------------------------------------------------------------------------------------------
			// <---------------------------------------------------------------------------------------------------------
			
			// START 02: Erweiterung der SELECT-Anweisung ---------------------------------------------------------------------->	
			
			if 		($_GET['modus'] == 1){					// Zeige alle Datensätze

				$where = '';

			}elseif ($_GET['modus'] == 2){					// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster = 1;	
				$where = '';
				
			}elseif ($_GET['modus'] == 3){					// Öffne Suchfenster und zeige alle Datensäte

				$suchfenster = 1									;
				if (is_numeric($_POST["f_id_input"])){
					$f_id = $_POST["f_id_input"]					;
				}else{
					$f_id = $_POST["f_id"]							;
				}
				$where = 'WHERE f_id'.$_POST["f_id_operator"] . $f_id;
				
				
			}

			

			// STOP  02 <---------------------------------------------------------------------------------------------------------
			switch ($schalter) {
				case "f_id":
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			f.f_id;
								");
					break;
				case "f_name":
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			f.f_name;
								");
					break;
				case "von":
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			f.von;
								");
					break;
				case "bis":
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			f.bis;
								");
					break;
				case "kl_ku":
					$result = 	pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			f.kl_ku;
								");
					break;
				case "f_unterkunft":
					$result = 	pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												ORDER BY			u.u_name;
								");
					break;
				default:
					$result = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												".$where."
												;
								");
			}


			if ($suchfenster == 1){


		


				$formSelect = pg_query($db,
											"	SELECT 				f.f_id, f.f_name, f.von, f.bis,f.kl_ku, u.u_name 
												FROM 				fahrt f 
												LEFT OUTER JOIN 	unterkunft u 
												ON 					(f.f_unterkunft=u.u_id)
												;
								");
	


				$index=0;

				while($row=pg_fetch_assoc($formSelect)){
					$fahrtID[$index] = $row[$attributeFahrt[0]]		;
					$index++										;
				}

				$operator[0]=">"	;
				$operator[1]=">="	;
				$operator[2]="="	;
				$operator[3]="<="	;
				$operator[4]="<"	;


				$verknuepfung[0] ="AND";
				$verknuepfung[1] ="OR";

				asort($fahrtID);	
				echo'	<div id="suche">'																;
				echo		'<form action="./fahrt.php?modus=3" method="post" autocomplete="off">'		;
				echo			'<label>FahrtID:'														;
				echo			'<select name="f_id_operator">'											;
				echo '				<option>---				</option>'									;
				foreach ($operator as $key => $val) {	
					if (array_key_exists('f_id_operator',$_POST) && $_POST['f_id_operator']== $val){
						echo '				<option selected >'.$val.'</option>'											;
					}else{
						echo '				<option >'.$val.'</option>'											;
					}
				}
				echo '			</select>'		;;	
//-------------------------------------------------------------------------------------------------------------------------------------
				echo			'<select name="f_id">'													;
				echo '				<option>---</option>'												;

				foreach ($fahrtID as $key => $val) {	
					if (array_key_exists('f_id',$_POST) && $_POST['f_id']== $val){
						echo '				<option selected >'.$val.'</option>'											;
					}else{
						echo '				<option>'.$val.'</option>'											;
					}
				}
				
				echo '			</select>'																;

//-------------------------------------------------------------------------------------------------------------------------------------
				if (array_key_exists('f_id_input',$_POST) && $_POST['f_id_input'] != ''){
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" value='.$_POST['f_id_input'].'>'		;	
				}else{
					echo '			<input id="f_id_input" name="f_id_input" maxlength="5" size="5" >'		;	
				}
	
//-------------------------------------------------------------------------------------------------------------------------------------
				echo			'<select name="f_id_verknuepfung">'										;
				echo '				<option>---				</option>'									;
				foreach ($verknuepfung as $key => $val) {	
					if (array_key_exists('f_id_verknuepfung',$_POST) && $_POST['f_id_verknuepfung']== $val){
						echo '				<option selected >'.$val.'</option>'											;
					}else{
						echo '				<option>'.$val.'</option>'											;
					}
				}

				echo '			</select>'																;
				echo '		</label>'																	;
				echo '		<button type="submit" name="action" value="0">Suche</button>'				;
				echo '	</form>'																		;
				echo '</div>'																			;		
			}

//-------------------------------------------------------------------------------------------------------------------------------------
/*
				echo '			<input type="button" id="btnReset" value="Reset" onclick="Reset();" />'	;
				echo '				<script type="text/javascript">'											;
				echo '    				function Reset() {'													;
        		echo '						var dropDown = document.getElementById("ddlFruits");'						;
        		echo '						dropDown.selectedIndex = 0;'												;
    			echo '					}'																		;
				echo '				</script>"'																;

				*/
/*


					echo "<table>";
							echo "<tr>";
								foreach ($spaltenBezeichnerFahrt as $key => $value)	{
									if(array_key_exists('select',$_GET) && $_GET['select']==1){
										echo "<th>". $value ."</th>";
									}else{
										echo "<th>". '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";
									}
								}
							echo "</tr>";

						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								foreach ($attributeFahrt as $value)	{
									if($value == 'f_id') {
										$fahrtID = $row[ $value ]; 
									}
									if ($row[ $value ] == ''){
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id='.$fahrtID.'">FÜGE HINZU</a></td>';
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
								}
							echo "</tr>";
						}
			echo "</table>";
				
			}
*/

			echo'	<div id="rahmen_3">';


					echo "<table>"													;
							
							foreach ($_POST as $key => $value)	{
									echo "<tr>"										;
									echo "<td>".$key."</dt><td>".$value."</dt>"		;	
									echo "</tr>"									;
							}
					echo "</table>";
					echo "<table>"													;
							
							foreach ($_GET as $key => $value)	{
									echo "<tr>"										;
									echo "<td>".$key."</dt><td>".$value."</dt>"		;	
									echo "</tr>"									;
							}
									echo "<tr>"										;
									echo "<td>Suchfenster</dt><td>".$suchfenster."</dt>"		;	
									echo "<td>WHERE</dt><td>".$where."</dt>"		;	
									echo "</tr>"									;
					echo "</table>";


					echo "<table>";
							echo "<tr>";
								foreach ($spaltenBezeichnerFahrt as $key => $value)	{
									if(array_key_exists('select',$_GET) && $_GET['select']==1){
										echo "<th>". $value ."</th>";
									}else{
										echo "<th>". '<a href="fahrt.php?sort='.$key.'">'. $value ."</a></th>";
									}
								}
							echo "</tr>";


						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								foreach ($attributeFahrt as $value)	{
									if($value == 'f_id') {
										$fahrtID = $row[ $value ]; 
									}
									if ($row[ $value ] == ''){
										echo '<td class="rosa">' . '<a href="addUnterkunft.php?sort=&f_id='.$fahrtID.'">FÜGE HINZU</a></td>';
									}else{
										echo "<td>" .$row[ $value] . "</td>";
									}
								}
							echo "</tr>";
						}
			echo "</table>";
		?>
		</div>
	</body>
</html>
