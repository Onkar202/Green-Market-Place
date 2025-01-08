<?php
    $con = new mysqli('localhost','root','','gmp');
    if(!$con){
        
        die(mysqli_error($con));
    }
?>