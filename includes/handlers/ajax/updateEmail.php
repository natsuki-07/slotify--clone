<?php
include("../../config.php");


if(!isset($_POST['username'])) {
  echo "ERROR: Could not set username";
  exit();
}

if(isset($_POST['email']) && $_POST['email'] != "") {
   $username = $_POST['username'];
   $email = $_POST['username'];

   if(!filter_var($email,FILTER_VALIDARE_EMAIL)){
     echo "Email is invalid";
     exit();
   }

   $emailCheck = mysqli_query($con,"SELECT email FROM users WHERE email='$email' AND username != '$username'");
   if(mysqli_num_row($emailCheck) >0 ){
     echo "Email is already in use";
     exit();
   }
   $updateQuery = mysqli_query($con,"SELECT users SET email = '$email' WHERE username='$username'");
   echo "update seccessful"; 
} else {
  echo "You must provide a username";
}
?>