<?php
/**
 * This PHP file contains the login for KIWIBOTS Team Pager
 */
 
 //Session management
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//See if we have already logged in
	if ((isset($_SESSION['loginval']) && ($_SESSION['loginval'] == true))){
		header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");
	}


//See if the form has been submitted
if (filter_input(INPUT_POST,"hashkey") != null){
	$returnedHash = substr(filter_input(INPUT_POST,"hashkey"),0,10);
	$sessionHash = substr($_SESSION['hashkeylogin'],0,10);
	//Cool we're good
	if (filter_input(INPUT_POST,"pwd") == "PageMe"){
		$_SESSION['loginval'] = true;
		header("Location: http://".$_SERVER['SERVER_NAME']."/index.php");
	}
	//Cool we're good
	if (filter_input(INPUT_POST,"pwd") == "ResetMe-killer"){
		$_SESSION['loginval'] = true;
		header("Location: http://".$_SERVER['SERVER_NAME']."/ResetAllData.php?PWD=killer");
	}
}


//Generate the form token so that the system is safe
$hashkey = sha1(mt_rand());
$_SESSION['hashkeylogin'] = $hashkey;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>KIWIBOTS Pager Server</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="jumbotron text-center">
  <h1>KIWIBOTS Pager Login</h1>
  <p>Please enter the pager password to continue</p> 
</div>
<div class="container">
  <div class="col-md-4"></div>
  <div class="col-md-4">
	<form method="post">
		<input type="hidden" name="hashkey" value="<?php echo $hashkey ?>">
		Password: <input type="password" name="pwd"><br>
		<input type="submit" value="Login">
	</form>
  </div>
  <div class="col-md-4"></div>
</div>

</body>
</html>
