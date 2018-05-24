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
			require("./attributArrays.php");			/*Die ausgelagerten Arrays mit Attributnamen uns Spaltenbezeichner werden eingefügt*/




			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			/*$fahrtSequenceNr = pg_query($db,"SELECT nextval ('fahrtSeq'));*/

			switch ($_GET['sort']) {
				case "u_uid":
					$result = pg_query($db,

											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.u_id;

								");
					break;
				case "u_name":
					$result = pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.u_name;

								");
					break;
				case "typ":
					$result = pg_query($db,		
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.typ;

								");

												break;
				case "errbar":
					$result = pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.errbar;

								");

					break;
				case "kosten":
					$result = 	pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.kosten;

								");
					break;
				case "entfkern":
					$result = 	pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.entfkern;

								");

					break;
				case "u_ort":
					$result = 	pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			u.u_ort;

								");

					break;
				case "o_name":
					$result = 	pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)
												ORDER BY			o.o_name;

								");

					break;


				default:
					$result = pg_query($db,
											"	SELECT              u.u_id,u.u_name,u.typ,u.errbar,u.kosten, u.entfkern, u.u_ort, o.o_name
  												FROM                unterkunft u
  												LEFT OUTER JOIN     ort o
  												ON                  (o.o_id=u.u_ort)

								");

			}
			echo'	<div id="rahmen_3">';
					echo "<table>";
							echo "<tr>";
								foreach ($spaltenBezeichnerUnterkunft as $key => $value)	{
									echo "<th>". '<a href="addUnterkunft.php?sort='.$key.'">'. $value ."</a></th>";
								}
							echo "</tr>";

						while($row=pg_fetch_assoc($result)){
							echo "<tr>";
								foreach ($attributeUnterkunft as $value)	{
									if($value == 'f_id') {
										$fahrtID = $row[ $value ]; 
									}
									if ($row[ $value ] == ''){
										echo '<td class="rosa">' . '<a href="addUnterkunft.php=f_id='.$fahrtID.'">FÜGE HINZU</a></td>';
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
