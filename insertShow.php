<!DOCTYPE html>
<html>
	<head>
		<title>Insert data to PostgreSQL with php - creating a simple web application</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style>
			li {listt-style: none;}
		</style>
	</head>
	<body>
		<h1>List of all actors in the table</h1>
		<?php
			$db = pg_connect("host=localhost port=5432 dbname=fahraway user=parallels password=niewel")
			$query = "INSERT INTO schueler1 VALUES 
					('7','$_POST[nachname]','$_POST[vorname]','$_POST[geschlecht]')";
			$result = pg_query($query); 

			$result = pg_query($db,"SELECT * FROM schueler1");
			echo "<table>";
				while($row=pg_fetch_assoc($result)){
					echo "<tr>";
						echo "<td align='center' width='200'>" . $row['schueler1_id'] 	. "</td>";
						echo "<td align='center' width='200'>" . $row['nachname'] 		. "</td>";
						echo "<td align='center' width='200'>" . $row['vorname'] 		. "</td>";
						echo "<td align='center' width='200'>" . $row['geschlecht'] 	. "</td>";
				echo "</tr>";}
			echo "</table>";
		?>
	</body>
</html>

