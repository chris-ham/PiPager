<?php
 //Session management
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!(isset($_SESSION['loginval']) && ($_SESSION['loginval'] == true))){
	header("Location: login.php");
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Team Pager</title>
	<style>
	body {font-family: "Lato", sans-serif;}
	
	/*https://www.w3schools.com/howto/howto_js_tabs.asp*/

	/* Style the tab */
	div.tab {
	    overflow: hidden;
	    border: 1px solid #ccc;
	    background-color: #f1f1f1;
	}
	
	/* Style the buttons inside the tab */
	div.tab button {
	    background-color: inherit;
	    float: left;
	    border: none;
	    outline: none;
	    cursor: pointer;
	    padding: 14px 16px;
	    transition: 0.3s;
	    font-size: 2em;
	}

	tr.border_bottom td {
		border-bottom:1pt solid black;
	}
	
	.main{
		font-size:2em;
	}

	/* Change background color of buttons on hover */
	div.tab button:hover {
	    background-color: #ddd;
	}
	
	/* Create an active/current tablink class */
	div.tab button.active {
	    background-color: #ccc;
	}
	
	/* Style the tab content */
	.tabcontent {
	    display: none;
	    padding: 6px 12px;
	    border: 1px solid #ccc;
	    border-top: none;
            height: 800px;
	    overflow: auto;
	}

	</style>
  </head>

<body>
<div>
<?php

	$team = $_GET["team"];
	$aDivision = $_GET["division"];
//	echo 'Clicked on = Division #'.$aDivision.' Match #'.$match;
//	echo "<br>";
//	echo date('Y-m-d H:i:s'); 
//	echo "<br>";
//	echo "<br>";
	//MySQL Details
	$mysqlserver = "localhost";
	$mysqlusername = "pager";
	$mysqlpassword = "raspberry";
	$mysqldb = "VEX_TM";
	$conn = new mysqli($mysqlserver, $mysqlusername, $mysqlpassword, $mysqldb);

	//insert the message to be sent to the ToPage table
	//this will result in it being sent
	if(strlen($aDivision) != 0){
		$query = "INSERT INTO ToPage (division, type, id, done, tmp, UID) VALUES ('".$aDivision."','T','".$team."','N','','".$aDivision.$team."')";
		$result = $conn->query($query);
	}

	//get the divisions that are loaded on the USB fob
	$query = "SELECT DISTINCT `Division` FROM `Teams`";
	$result = $conn->query($query);

	//create the division tabs
	echo '<div class="tab">'.PHP_EOL;
	while(list($division) = $result->fetch_row()){
		if (strcmp($division,$aDivision) == 0){
			echo '<button class="tablinks" id="defaultOpen" onclick="openCity(event, \''.$division.'\')">'.$division.'</button>'.PHP_EOL;
		}else{
			echo '<button class="tablinks" onclick="openCity(event, \''.$division.'\')">'.$division.'</button>'.PHP_EOL;
		}
	}
	echo '</div>';


	//Load the teams from the database
	$divisionOld = 'start';
	//Open the MySQL Connection
	global $mysqldb, $mysqlpassword, $mysqlprefix, $mysqlserver, $mysqlusername;
	$query = "SELECT DISTINCT Teams.`Division`,Teams.`TeamNumber`, Teams.`Name` ,`done` 
			FROM `Teams` 
			LEFT JOIN ToPage ON Teams.UID = ToPage.UID 
			ORDER BY Teams.`Division`, Teams.`TeamNumber` ASC";


	$result = $conn->query($query);
	
	while(list($division, $TeamNumber, $TeamName, $done) = $result->fetch_row()){
	
		if (strcmp($division, $divisionOld) != 0){
			if (strcmp($divisionOld, 'start') != 0){
				echo '</table></div>';
			};
			$divisionOld = $division;
			echo '<div id="'.$division.'" class="tabcontent">';
			echo '<table width ="90%" class="main table-striped">'.PHP_EOL;
			echo '<tr class="border_bottom">';
			echo '<td width ="20%"><strong>Team #</strong></td>';
			echo '<td width ="60%"><strong>Team Name</strong></td>';
			echo '</tr>'.PHP_EOL;

		}
		echo '<tr  class="border_bottom" id="'.$division.$Team.'" style="height:30px">';

		//draw the color of the box according to the sent state
		$BoxColor = '#ff0000';
		if ((strcmp($aDivision,$division) == 0) && (strcmp($team,$TeamNumber) == 0)){
			$BoxColor = '#00ff00';
		}

		echo '<td><strong><a href="Teams.php?division='.$division.'&amp;team='.$TeamNumber.'"  style="background-color:'.$BoxColor.'">'.
			str_pad($TeamNumber,15,"_",STR_PAD_BOTH).
			'</a></strong></td>';

		echo '<td>'.$TeamName.'</td>';
		echo '</tr>'.PHP_EOL;

	}
	if (strcmp($divisionOld,'start') != 0){
		echo '</table></div>';
		$divisionOld = $division;
	}
	$conn->close();

	
	echo '<script>'.PHP_EOL;
	echo '// Get the element with id="defaultOpen" and click on it'.PHP_EOL;
	echo 'document.getElementById("defaultOpen").click();'.PHP_EOL;

	echo 'var element = document.getElementById("'.$aDivision.$team.'");'.PHP_EOL;
	echo 'element.scrollIntoView();'.PHP_EOL;

?>

function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}


</script>

</div>
</body>
</html>

