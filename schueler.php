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
			$attributeSchueler= [							
    			0	=> "f_id",
    			1	=> "s_id",
    			2	=> "vname",
    			3	=> "nname",
    			4	=> "gebdat",
    			5	=> "m_w",
			];
			$spaltenBezeichnerSchueler= [
    			"f_id" 			=> "ZeigeFahrt",
    			"s_id" 			=> "SchülerID",
    			"vname"			=> "Vorname",
    			"nname"			=> "Nachname",
    			"gebdat"		=> "Geburtstag",
    			"m_w"			=> "Geschlecht",
			];


			

		

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			/*$fahrtSequenceNr = pg_query($db,"SELECT nextval ('fahrtSeq'));*/

			switch ($_GET['sort']) {
				case $attributeSchueler[0]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."n.".$attributeSchueler[0]."
												
								");
					break;

				case $attributeSchueler[1]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."s.".$attributeSchueler[1]."
												
								");
					break;
				case $attributeSchueler[2]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."s.".$attributeSchueler[2]."
												
								");
					break;
				case $attributeSchueler[3]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."s.".$attributeSchueler[3]."
												
								");
					break;
				case $attributeSchueler[4]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."s.".$attributeSchueler[4]."
												
								");
					break;
				case $attributeSchueler[5]:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												ORDER BY            "."s.".$attributeSchueler[5]."
												
								");
					break;
				default:
					$result = pg_query($db,
											"	SELECT 				n.f_id,s.s_id,s.vname,s.nname,s.gebdat,s.m_w	
												FROM 				schueler s,nimmtteil n
												WHERE				s.s_id = n.s_id 
												
								");



			}
			echo'	<div id="rahmen_3">';
					echo "<table>";
							echo "<tr>";
								foreach ($spaltenBezeichnerSchueler as $key => $value)	{
									echo "<th>". '<a href="schueler.php?sort='.$key.'">'. $value ."</a></th>";
								}
							echo "</tr>";

						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								foreach ($attributeSchueler as $value)	{
									if($value == 'f_id') {
										$fahrtID = $row[ $value ]; 
										echo '<td >' . '<a href="fahrt.php?select=1&f_id='.$fahrtID.'">ZEIGE Fahrt</a></td>';
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
