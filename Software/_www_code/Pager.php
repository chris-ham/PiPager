
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

	$match = $_GET["match"];
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
		$query = "INSERT INTO ToPage (division, type, id, done, tmp, UID) VALUES ('".$aDivision."','M','".$match."','N','','".$aDivision.$match."')";
		$result = $conn->query($query);
	}

	//get the divisions that are loaded on the USB fob
	$query = "SELECT DISTINCT `Division` FROM `Matches`";
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


	//Load the matches from the database
	$divisionOld = 'start';
	//Open the MySQL Connection
	global $mysqldb, $mysqlpassword, $mysqlprefix, $mysqlserver, $mysqlusername;
	$query = "SELECT Matches.`Division`,Matches.`MatchNumber`,`Red1`,`Red2`,`Red3`,`Blue1`,`Blue2`,`Blue3`,`done`
		FROM `Matches`
		LEFT JOIN ToPage ON Matches.UID = ToPage.UID";

	$result = $conn->query($query);
	while(list($division,$MatchNumber,$Red1,$Red2,$Red3, $Blue1, $Blue2, $Blue3, $done) = $result->fetch_row()){
		if (strcmp($division, $divisionOld) != 0){
			if (strcmp($divisionOld, 'start') != 0){
				echo '</table></div>';
			};
			$divisionOld = $division;
			echo '<div id="'.$division.'" class="tabcontent">';
			echo '<table width ="90%" class="main table-striped">'.PHP_EOL;
			echo '<tr class="border_bottom">';
			echo '<td width ="30%"><strong>Match #</strong></td>';
			echo '<td width ="8%"><strong>R1</strong></td>';
			echo '<td width ="8%"><strong>R2</strong></td>';
			echo '<td width ="8%"><strong>R3</strong></td>';
			echo '<td width ="8%"><strong>B1</strong></td>';
			echo '<td width ="8%"><strong>B2</strong></td>';
			echo '<td width ="8%"><strong>B3</strong></td>';
			echo '</tr>'.PHP_EOL;

		}
		echo '<tr  class="border_bottom" id="'.$division.$MatchNumber.'" style="height:30px">';

		//draw the color of the box according to the sent state
		$BoxColor = '#ff0000';
		if ((strcmp($aDivision,$division) == 0) && (strcmp($match,$MatchNumber) == 0)){
			$BoxColor = '#cccc00';
		}
		if (strcmp($done,'Y') == 0){
			$BoxColor = '#00ff00';
		};
		echo '<td><strong><a href="Pager.php?division='.$division.'&amp;match='.$MatchNumber.'"  style="background-color:'.$BoxColor.'">'.
			str_pad($MatchNumber,15,"_",STR_PAD_BOTH).
			'</a></strong></td>';

		echo '<td>'.$Red1.'</td>';
		echo '<td>'.$Red2.'</td>';
		echo '<td>'.$Red3.'</td>';
		echo '<td>'.$Blue1.'</td>';
		echo '<td>'.$Blue2.'</td>';
		echo '<td>'.$Blue3.'</td>';
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


	echo 'var element = document.getElementById("'.$aDivision.$match.'");'.PHP_EOL;
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

