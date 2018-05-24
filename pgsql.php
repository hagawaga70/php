<!-- Datei pgsql.php zum Verbindungstest auf postgreSQL -->
<!DOCTYPE html>
<html lang="de">
	<body> <?php
		echo "<p>Verbindungstest um " . Date("H:i:s") . " Uhr </p>\n";
        try {
       		$con = new PDO (’pgsql:host=10.211.55.9;dbname=fahraway’,’parallels’,’DMia37171’);
            echo "<p style=’color:green’>Verbindung erfolgreich!</p>\n";
            $con = null;
         } catch (PDOException $err) {
 			echo "Fehler: " . $err->getMessage ();
         }
		
?> </body>
</html>
