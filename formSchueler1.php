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
		<h2>Enter information regarding book</h2>
		<ul>
			<form name="insert" action="insertShow.php" method="POST" >
				<li>Nachname:</li>
				<li><input type="text" name="nachname" /></li>

				<li>Vorname:</li>
				<li><input type="text" name="vorname" /></li>

				<li>Geschlecht:</li>
				<li><input type="text" name="geschlecht" /></li>

				<li><input type="submit" /></li>
			</form>
		</ul>
	</body>
</html>
>
