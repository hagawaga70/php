<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
		<div id="navigation">
			<div class="naviBoxSelect">
  					<div class="naviInline">Fahrten:												</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Schüler:												</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Lehrer:													</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Orte:													</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Aktivitäten:											</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Anbieter:												</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Unterkünfte:											</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Views 1:												</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
			<div class="naviBox">
  					<div class="naviInline">Views 2:												</div>
  					<div class="naviInline"><a class="ankerNavi"  href="index.php">Zeige</a>		</div>
					<div class="naviInline"><a class="ankerNavi"  href="index.php">Suche</a>	    </div>
			</div>
		</div>

		<div id="rahmen_3">
		<?php
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
    			"f_unterkunft"	=> "Unterkunft ID",
			];

			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel");
			/*$fahrtSequenceNr = pg_query($db,"SELECT nextval ('fahrtSeq'));*/

			switch ($_GET['sort']) {
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
								");
			}
			echo "<table>";
					echo "<tr>";
						foreach ($spaltenBezeichnerFahrt as $key => $value)	{
							echo "<th>". '<a href="index.php?sort='.$key.'">'. $value ."</a></th>";
						}
					echo "</tr>";

				while($row=pg_fetch_assoc($result)){
					echo "<tr>";
						foreach ($attributeFahrt as $value)	{
							if ($row[ $value ] == ''){
								echo '<td class="rosa">' .$row[ $value] . "</td>";
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
