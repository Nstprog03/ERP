<?php
require_once('../db.php');
if (isset($_POST['conf_no'])) 
{
	$data = $_POST['conf_no'];
	$conf_no =explode('/',$data)[1];

	
	$sql="select lot_no,lot_bales from seller_conf where sales_conf = '".$conf_no."'";
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    //check if record exist then fetch lot & bales
	$sql2="select * from sales_conf_split where conf_no = '".$conf_no."'";
	$result2 = mysqli_query($conn, $sql2);
	
	$row2count=mysqli_num_rows($result2);
	if ($row2count>0) 
	{
		$lotno1 = array();
		$lot_bales2 = array();
		while($row21 = mysqli_fetch_assoc($result2))
		{
			$lotno1[]=json_decode($row21['lot_no']);
	    	$lot_bales2[]=json_decode($row21['lot_bales']);
		}

		//check in sales report if any report generated with sale conf
		$sqlSR="select * from sales_report where conf_no='".$conf_no."'";
		$resultSR = mysqli_query($conn, $sqlSR);
		while($rowSR = mysqli_fetch_assoc($resultSR))
		{
			$lotno1[]=json_decode($rowSR['lot_no']);
	    	$lot_bales2[]=json_decode($rowSR['lot_bales']);
		}



		//merge all records in one array
		$newLotArr = array_merge(...$lotno1);
		$newBalesArr=array_merge(...$lot_bales2);



		$finalLot=array();
		$finalBales=array();

		foreach ($newLotArr as $key => $value) {

			//if duplicate lot_no found then it will merge LOT Bales
			$getkey = array_search($value, $finalLot);
		    if($getkey == FALSE)
		    {
		       $finalLot[$value]=$value;
		       $finalBales[$value]=$newBalesArr[$key];
		    }
		    else
		    {
		    	//duplicate LOT found so merge bales in same LOT
		    	$finalBales[$getkey]+=$newBalesArr[$key];
		    	
		    }
			

		}

		//sort by key
		ksort($finalLot);
		ksort($finalBales);

		//rearrange array key from 0
		//$finalLot = array_values($finalLot);
		//$finalBales = array_values($finalBales);

	    $r2lot_no=$finalLot;
	    $r2lotBales=$finalBales;



	    // print_r($r2lot_no);
	    // print_r($r2lotBales);
	    // exit;

	  

		$r1lot_no=json_decode($row['lot_no']);
	    $r1lotBales=json_decode($row['lot_bales']);




    	$bales = array();
	    $lotno = array();
	    foreach ($r1lot_no as $key => $value) 
	    {

    		if(!isset($r2lot_no[$value]))
    		{
    			$lotno[] = $value;
    			$bales[] = $r1lotBales[$key];
    		}
    		if(isset($r2lot_no[$value]) && isset($r2lotBales[$value]) && $r1lotBales[$key] != $r2lotBales[$value])
    		{
    			$lotno[] = $value;
    			$bales[] = (string)($r1lotBales[$key] - $r2lotBales[$value]);
    		}
	    }

		
		$row['lot_no']=$lotno;
	    $row['lot_bales']=$bales;	    


		
		$row['status'] = 'exists';
		echo json_encode($row);



	}
	else
	{

		$bales = json_decode($row['lot_bales']);
	    $lotno = json_decode($row['lot_no']);
		

		//check in sales report if any report generated with sale conf
		$sqlSR="select * from sales_report where conf_no='".$conf_no."'";
		$resultSR = mysqli_query($conn, $sqlSR);

		if(mysqli_num_rows($resultSR)>0)
		{
			$lotno1=array();
			$lot_bales2=array();

			while($rowSR = mysqli_fetch_assoc($resultSR))
			{
				$lotno1[]=json_decode($rowSR['lot_no']);
		    	$lot_bales2[]=json_decode($rowSR['lot_bales']);
			}

			$newLotArr = array_merge(...$lotno1);
			$newBalesArr=array_merge(...$lot_bales2);

			//if lot no. same then merge bales
			$finalLot=array();
			$finalBales=array();
			foreach ($newLotArr as $key => $value) {

				//if duplicate lot_no found then it will merge LOT Bales
				$getkey = array_search($value, $finalLot);
			    if($getkey == FALSE)
			    {
			       $finalLot[$value]=$value;
			       $finalBales[$value]=$newBalesArr[$key];
			    }
			    else
			    {
			    	//duplicate LOT found so merge bales in same LOT
			    	$finalBales[$getkey]+=$newBalesArr[$key];
			    }
			}

			//sort by key
			ksort($finalLot);
			ksort($finalBales);

			$r2lot_no=$finalLot;
		    $r2lotBales=$finalBales;

		        //main lot array
				$r1lot_no=$lotno;
			    $r1lotBales=$bales;

			    $bales = array();
			    $lotno = array();
			    foreach ($r1lot_no as $key => $value) 
			    {

		    		if(!isset($r2lot_no[$value]))
		    		{
		    			$lotno[] = $value;
		    			$bales[] = $r1lotBales[$key];
		    		}
		    		if(isset($r2lot_no[$value]) && isset($r2lotBales[$value]) && $r1lotBales[$key] != $r2lotBales[$value])
		    		{
		    			$lotno[] = $value;
		    			$bales[] = (string)($r1lotBales[$key] - $r2lotBales[$value]);
		    		}
			    }

		}


	    $main_bales=array();
	    foreach ($lotno as $key => $value) {
	    	$main_bales[$value]=$bales[$key];
	    }
	
		
		$row['lot_no']=$lotno;
	    $row['lot_bales']=$bales;


		$response = $row;
		$response['status'] = 'new';
		echo json_encode($response);
	}




}		
?>