<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8"/>	
		<title>Fahraway</title>
		<link rel="stylesheet" href="./fahraway.css">
	</head>
	<body>
				<?php
					  define("L_LANG", "de_DE");
					
						 require('calendar/tc_calendar.php');
					
					  $date3_default = "2018-05-28";
					  $date4_default = "2018-06-03";

					  $myCalendar = new tc_calendar("date3", true, false);
					  $myCalendar->setIcon("calendar/images/iconCalendar.gif");
					  $myCalendar->setDate(date('d', strtotime($date3_default))
							, date('m', strtotime($date3_default))
							, date('Y', strtotime($date3_default)));
					  $myCalendar->setPath("calendar/");
					  $myCalendar->setYearInterval(1970, 2020);
					  $myCalendar->setAlignment('left', 'bottom');
					  $myCalendar->setDatePair('date3', 'date4', $date4_default);
					  $myCalendar->writeScript();	  
					  
					  $myCalendar = new tc_calendar("date4", true, false);
					  $myCalendar->setIcon("calendar/images/iconCalendar.gif");
					  $myCalendar->setDate(date('d', strtotime($date4_default))
						   , date('m', strtotime($date4_default))
						   , date('Y', strtotime($date4_default)));
					  $myCalendar->setPath("calendar/");
					  $myCalendar->setYearInterval(1970, 2020);
					  $myCalendar->setAlignment('left', 'bottom');
					  $myCalendar->setDatePair('date3', 'date4', $date3_default);
					  $myCalendar->writeScript();	  
					  ?>
	</body>
</html>
