<?php
session_start();
if(isset($_POST['submit']))
{
$_SESSION['qis_form']=$_POST['form'];
$_SESSION['qis_firm']=$_POST['firm'];
$_SESSION['qis_quarter']=$_POST['quarter'];
$_SESSION['qis_year']=$_POST['year'];
header("location: index1.php");
}
?>