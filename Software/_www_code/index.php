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
    <title>VEX Pager Default Page</title>
    <style type="text/css" media="screen">
  * {
    margin: 0px 0px 0px 0px;
    padding: 0px 0px 0px 0px;
  }

  body, html {
    padding: 3px 3px 3px 3px;

    background-color: #D8DBE2;

    font-family: Verdana, sans-serif;
    font-size: 11pt;
    text-align: center;
  }

  div.main_page {
    position: relative;
    display: table;

    width: 800px;

    margin-bottom: 3px;
    margin-left: auto;
    margin-right: auto;
    padding: 0px 0px 0px 0px;

    border-width: 2px;
    border-color: #212738;
    border-style: solid;

    background-color: #FFFFFF;

    text-align: center;
  }

  div.page_header {
    height: 99px;
    width: 100%;

    background-color: #F5F6F7;
  }

  div.page_header span {
    margin: 15px 0px 0px 50px;

    font-size: 180%;
    font-weight: bold;
  }

  div.page_header img {
    margin: 3px 0px 0px 40px;

    border: 0px 0px 0px;
  }

  div.table_of_contents {
    clear: left;

    min-width: 200px;

    margin: 3px 3px 3px 3px;

    background-color: #FFFFFF;

    text-align: left;
  }

  div.table_of_contents_item {
    clear: left;

    width: 100%;

    margin: 4px 0px 0px 0px;

    background-color: #FFFFFF;

    color: #000000;
    text-align: left;
  }

  div.table_of_contents_item a {
    margin: 6px 0px 0px 6px;
  }

  div.content_section {
    margin: 3px 3px 3px 3px;

    background-color: #FFFFFF;

    text-align: left;
  }

  div.content_section_text {
    padding: 4px 8px 4px 8px;

    color: #000000;
    font-size: 100%;
  }

  div.content_section_text pre {
    margin: 8px 0px 8px 0px;
    padding: 8px 8px 8px 8px;

    border-width: 1px;
    border-style: dotted;
    border-color: #000000;

    background-color: #F5F6F7;

    font-style: italic;
  }

  div.content_section_text p {
    margin-bottom: 6px;
  }

  div.content_section_text ul, div.content_section_text li {
    padding: 4px 8px 4px 16px;
  }

  div.section_header {
    padding: 3px 6px 3px 6px;

    background-color: #8E9CB2;

    color: #FFFFFF;
    font-weight: bold;
    font-size: 112%;
    text-align: center;
  }

  div.section_header_red {
    background-color: #CD214F;
  }

  div.section_header_grey {
    background-color: #9F9386;
  }

  .floating_element {
    position: relative;
    float: left;
  }

  div.table_of_contents_item a,
  div.content_section_text a {
    text-decoration: none;
    font-weight: bold;
  }

  div.table_of_contents_item a:link,
  div.table_of_contents_item a:visited,
  div.table_of_contents_item a:active {
    color: #000000;
  }

  div.table_of_contents_item a:hover {
    background-color: #000000;

    color: #FFFFFF;
  }

  div.content_section_text a:link,
  div.content_section_text a:visited,
   div.content_section_text a:active {
    background-color: #DCDFE6;

    color: #000000;
  }

  div.content_section_text a:hover {
    background-color: #000000;

    color: #DCDFE6;
  }

  div.validator {
  }
    </style>
  </head>
  <body>

    <div class="main_page">
      <div class="page_header floating_element">
         <span class="floating_element">
          VEX Pager Default Page
        </span>
      </div>
      <div class="content_section floating_element">


        <div class="section_header section_header_red">
          <div id="about"></div>
          It works!
        </div>
        <div class="content_section_text">
          <p>
                This is the default welcome page for the Kiwibots paging system. It shows the correct 
                operation of the VEX Pager server after installation.
                If you can read this page, it means that the Apache HTTP server installed at
                this site is working properly. <br>
		If you can see events listed in the 'Found Events' section then it means
		that the event directories on the USB fob have been found and read.
          </p>

        </div>
        <div class="section_header">
          <div id="changes"></div>
                Overview
        </div>
        <div class="content_section_text">
          <p>
                The VEX pager server allows the user to page either individual teams or
		all of the teams in a match.Select the operation you want to carry out below:

          </p>
		<div class="tab"></br>
<?php
	echo		'<a class="button" href="http://'.$_SERVER['SERVER_NAME'].'/Matches.php">Page Matches</a></br></br>';
	echo		'<a class="button" href="http://'.$_SERVER['SERVER_NAME'].'/Teams.php">Page Teams</a></br></br>';
?>
		</div>
        </div>

        <div class="section_header">
            <div id="docroot"></div>
                Found Events
        </div>

        <div class="content_section_text">
            <p>
<?php

	//MySQL Details
	$mysqlserver = "localhost";
	$mysqlusername = "pager";
	$mysqlpassword = "raspberry";
	$mysqldb = "VEX_TM";
	$conn = new mysqli($mysqlserver, $mysqlusername, $mysqlpassword, $mysqldb);

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
?>

            </p>
        </div>


      </div>
    </div>
    <div class="validator">
    </div>
  </body>
</html>

