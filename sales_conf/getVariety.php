<?php
require_once('../db.php');

//get Variety & sub variety
if (isset($_POST['prod_id'])) 
{
	$prod_id=$_POST['prod_id'];
	
	$sql="select * from product_sub_items where product_id = '".$prod_id."'";

  $prod_quality=array();
  $prod_variety=array();
  $prod_sub_variety=array();

  foreach ($conn->query($sql) as $result) 
  {
 
    if($result['indicator']=='1')
    {
      array_push($prod_quality,$result);
    }
    if($result['indicator']=='2')
    {
      array_push($prod_variety,$result);
    }
    if($result['indicator']=='3')
    {
      array_push($prod_sub_variety,$result);
    }

  	
  }

    $row['main_variety']=$prod_variety;
    $row['prod_sub_variety']=$prod_sub_variety;
    $row['prod_quality']=$prod_quality;

	  echo json_encode($row);
}

	






?>