<?php
require_once('../db.php');
if (isset($_POST['conf_no']) && isset($_POST['table'])) {
	


	if($_POST['table'] == 'conf') {

		$conf_no=$_POST['conf_no'];
		$sql="select * from seller_conf where sales_conf = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();

		
		
			$sql3="select lot_no,lot_bales from sales_report where conf_no = '".$conf_no."'";
			$result3 = mysqli_query($conn, $sql3);
		    $row3=mysqli_fetch_array($result3);

		    $r3lot_no=json_decode($row3['lot_no']);
		    $r3lotBales=json_decode($row3['lot_bales']);

		    $r1lot_no=json_decode($row['lot_no']);
		    $r1lotBales=json_decode($row['lot_bales']);

		    $bales = array();
		    $lotno = array();
		    foreach ($r1lot_no as $key => $value) 
		    {
	    		if(!isset($r3lot_no[$key])){
	    			$lotno[] = $value;
	    			$bales[] = $r1lotBales[$key];
	    		}
	    		if(isset($r3lot_no[$key]) && isset($r3lotBales[$key]) && $r1lotBales[$key] != $r3lotBales[$key]){
	    			$lotno[] = $value;
	    			$bales[] = (string)($r1lotBales[$key] - $r3lotBales[$key]);
	    		}
		    }

		    
		    

			
		
		echo json_encode($row);
		
	}elseif ($_POST['table'] == 'confsplit') {

		$conf_no=$_POST['conf_no'];
		$sql="select sp.*,s.candy_rate from sales_conf_split sp, seller_conf s where sp.conf_no=s.sales_conf AND sp.conf_split_no = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();

		$sql2="SELECT SUM(noOFBales) as used_bales FROM sales_report WHERE conf_no='".$conf_no."'";	
		$result2 = mysqli_query($conn, $sql2);
		$row2check=mysqli_fetch_array($result2);
		if($row2check['used_bales']=='' || $row2check['used_bales']==null )
		{
			$row += array("used_bales"=>0);

		}
		else
		{
			$sql3="select lot_no,lot_bales from sales_report where conf_no = '".$conf_no."'";
			$result3 = mysqli_query($conn, $sql3);
		    $row3=mysqli_fetch_array($result3);

		    $r3lot_no=json_decode($row3['lot_no']);
		    $r3lotBales=json_decode($row3['lot_bales']);

		    $r1lot_no=json_decode($row['lot_no']);
		    $r1lotBales=json_decode($row['lot_bales']);

		    $bales = array();
		    $lotno = array();
		    foreach ($r1lot_no as $key => $value) 
		    {
	    		if(!isset($r3lot_no[$key])){
	    			$lotno[] = $value;
	    			$bales[] = $r1lotBales[$key];
	    		}
	    		if(isset($r3lot_no[$key]) && isset($r3lotBales[$key]) && $r1lotBales[$key] != $r3lotBales[$key]){
	    			$lotno[] = $value;
	    			$bales[] = (string)($r1lotBales[$key] - $r3lotBales[$key]);
	    		}
		    }

		    
		      $row['lot_no']=json_encode($lotno);
		    $row['lot_bales']=json_encode($bales);


			$row += array(
				"used_bales"=>$row2check['used_bales']
			);
		}


		echo json_encode($row);
	}

	


}		




?>