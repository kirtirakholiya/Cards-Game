<?php

// ******************************* Database Configuration *******************************

//  Date                     Developer                       Comments    
//  04-Aug-2020              Nrupeshsinh Rathod              Initial Creation    

// **************************************************************************************


$server_name = "localhost";
$user_name = "BeekayInfotech";
$password = "BeeKay@2020";
// $user_name = "root";
// $password = "";
$db = "cardsgame";

define("SITE_URL","http://103.209.67.200:8181/CardsGame-PHP/V1/");
define("IMG_URL","http://103.209.67.200:8181/CardsGame-PHP/V1/upload/");
$uploadpath =$_SERVER["DOCUMENT_ROOT"]."/CardsGame-PHP/V1/upload/";
$conn = new mysqli($server_name,$user_name,$password,$db);

if($conn->connect_error){
    echo $conn->connect_error;
exit();

}

?>