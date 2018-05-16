<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./html_2016.css">
	</head>
	<body>
		<div id="rahmen_2">
			<ul >
				<li><a href="index.html">Fahrten</a></li>
				<li><a href="marken.html">MARKEN</a></li>
			</ul>	
		</div>
		<div id="rahmen_3">
		<?php
			$attributeFahrt = [
    			0	=> "f_id",
    			1	=> "f_name",
    			2	=> "von",
    			3	=> "bis",
    			4	=> "kl_ku",
    			5	=> "f_unterkunft",
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
			switch ($i) {
				case "Apfel":
					echo "i ist Apfel";
					break;
				case "Balken":
					echo "i ist Balken";
					break;
				case "Kuchen":
					echo "i ist Kuchen";
					break;
			}
			$result = pg_query($db,"SELECT * FROM fahrt");
			echo "<table>";
					echo "<tr>";
						foreach ($spaltenBezeichnerFahrt as $key => $value)	{
							echo "<th>". '<a href="index.php?sort='.$key.'">'. $value ."</a></th>";
						}
					echo "</tr>";

				while($row=pg_fetch_assoc($result)){
					echo "<tr>";
						foreach ($attributeFahrt as $value)	{
							echo "<td>" .$row[ $value] . "</td>";
						}
					echo "</tr>";
				}
			echo "</table>";
		?>
		</div>
	</body>
</html>
