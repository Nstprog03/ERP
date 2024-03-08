<?php
require_once('../db.php');

//get Sub Variety
if (isset($_POST['main_variety'])) 
{
	$main_variety=$_POST['main_variety'];
	
	$sql="select sub_variety from product_variety where main_variety = '".$main_variety."'";

   foreach ($conn->query($sql) as $result) 
  {
  	$row=json_decode($result['sub_variety']);
  }
   
	echo json_encode($row);
}

	






?>