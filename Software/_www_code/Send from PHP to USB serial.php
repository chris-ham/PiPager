
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Pager Page</title>
  </head>
  <body>
<?php



//***************************
function sendMsg(){
  $bbSerialPort;
  $portName = "/dev/ttyACM0";
  $baudRate = 9600;
  $bits= 8;
  $spotBit = 1;

  if (!extension_loaded('dio')){
    echo("DIO error");
    exit;
  }

  $bbSerialPort = dio_open($portName, O_RDWR | O_NOCTTY | O_NONBLOCK);
  dio_fcntl($bbSerialPort, F_SETFL, O_SYNC);
  dio_tcsetattr($bbSerialPort, array(
     'baud' => $baudRate,
     'bits' => $bits,
     'stop' => $spotBit,
     'parity' => 0
  ));

  if(!$bbSerialPort){
    echo("Could not open serial port");
    exit;
  }

  $dataToSend = "?61010100000111011010000000\n";
  $bytesSent = dio_write($bbSerialPort, $dataToSend);
  echo "Sent: ".$bytesSent. " bytes";;
  echo "$dio_close($bbSerialPort);";
  echo "4<br>";

}


  sendMsg();

  echo date('Y-m-d H:i:s'); 
?>


  </body>
</html>

