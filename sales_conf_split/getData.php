<?php
require_once('../db.php');
if (isset($_POST['conf_no'])) 
{
	// $conf_no=$_POST['conf_no'];

	$data = $_POST['conf_no'];
	$conf_no =explode('/',$data)[1];
	
	$sql="select * from seller_conf where sales_conf = '".$conf_no."'";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_assoc($result);


	$used_bales=0;
	
	$sql2="SELECT IFNULL(SUM(no_of_bales), 0) as used_bales FROM sales_conf_split WHERE conf_no='".$conf_no."'";
	$result2 = mysqli_query($conn, $sql2);
	$rowScs2=$result2->fetch_assoc();
	$used_bales+=(int)$rowScs2['used_bales'];


    $sqlSR="select IFNULL(SUM(noOFBales), 0) as used_bales from sales_report where conf_no='".$conf_no."'";
	$resultSR = mysqli_query($conn, $sqlSR);
	$rowSR=$resultSR->fetch_assoc();
	$used_bales+=(int)$rowSR['used_bales'];

	$row['used_bales']=$used_bales;





	    //External Party
	    $party = "select * from external_party where id='".$row['external_party']."'";
		$partyresult = mysqli_query($conn, $party);
	  	$partyrow = mysqli_fetch_assoc($partyresult);
	  	$ext['ext_id']=$partyrow['id'];
	  	$ext['ext_name']=$partyrow['partyname'];

    	$row +=$ext;

    	//Broker
		
		$broker = "select * from broker where id='".$row['broker']."'";
		$broker_result = mysqli_query($conn, $broker);
	  	$broker_row = mysqli_fetch_assoc($broker_result);
	  	$brok['broker_id'] = $broker_row['id']; 
	  	$brok['broker_name'] = $broker_row['name'];
    	$row += $brok;

    	//products
		
		$products = "select * from products where id='".$row['product']."'";
		$products_result = mysqli_query($conn, $products);
	  	$products_row = mysqli_fetch_assoc($products_result);
	  	$prod['product_id'] = $products_row['id']; 
    	$prod['product_name'] = $products_row['prod_name'];
    	$row += $prod;



    	//get Product, Quality Variety & Sub Variety
      $sql="select * from product_sub_items where product_id = '".$products_row['id']."'";
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
	    $row['main_prod_sub_variety']=$prod_sub_variety;
	    $row['main_prod_quality']=$prod_quality;

	 echo json_encode($row);
	
}
?>