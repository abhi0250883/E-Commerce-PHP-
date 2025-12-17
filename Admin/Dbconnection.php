<?php
$server = "localhost";
$user = "root";
$pass = "";
$db = "cloth";
$port = 3307; 

$con = mysqli_connect("$server", "$user", "$pass" , "$db","$port");

if($con){
// echo "Connection successful";
}
else {
echo "Error". mysqli_connect_error();
}
?>