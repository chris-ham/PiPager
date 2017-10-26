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
  </head>

<body>
<div>
<?php

	$pwd = $_GET["PWD"];

	//MySQL Details
	$mysqlserver = "localhost";
	$mysqlusername = "pager";
	$mysqlpassword = "raspberry";
	$mysqldb = "VEX_TM";
	$conn = new mysqli($mysqlserver, $mysqlusername, $mysqlpassword, $mysqldb);

	//insert the message to be sent to the ToPage table
	//this will result in it being sent
	if(strcmp($pwd,'killer') == 0){
		$query = "INSERT INTO ToPage (division, type, id, done, tmp, UID) VALUES
			 ('none','X','0','N','','none')";
		$result = $conn->query($query);
	}
	$conn->close();
	echo 'All reset!';
?>

</div>
</body>
</html>

