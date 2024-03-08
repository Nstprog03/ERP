<?php
require_once('../db.php');
if (isset($_POST['conf_no'])) 
{
	$data = $_POST['conf_no'];
	$conf_no =explode('/',$data)[1];
	$record_id=$_POST['record_id'];	

	
	$sql="select lot_no,lot_bales from seller_conf where sales_conf = '".$conf_no."'";
	
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);




    //check if record exist then fetch lot & bales
	$sql2="select id,lot_no,lot_bales from sales_conf_split where conf_no = '".$conf_no."'";
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
		       $finalLot[$key]=$value;
		       $finalBales[$key]=$newBalesArr[$key];
		    }
		    else
		    {
		    	//duplicate LOT found so merge bales in same LOT
		    	$finalBales[$getkey]+=$newBalesArr[$key];
		    	
		    }
			

		}

		//rearrange array key from 0
		$finalLot = array_values($finalLot);
		$finalBales = array_values($finalBales);

		// print_r($finalLot);
		// print_r($finalBales);
		
	 //    exit;


		$r1lot_no=json_decode($row['lot_no']);
	    $r1lotBales=json_decode($row['lot_bales']);


		/*$r2lot_no=json_decode($row2['lot_no']);
	    $r2lotBales=json_decode($row2['lot_bales']);*/

	    $r2lot_no=$finalLot;
	    $r2lotBales=$finalBales;

	    //print_r($r2lot_no);
	   $arr1=array();
	   $arr2=array();
	foreach ($r2lot_no as $key => $value) 
	{
		   $arr1[$value]=$value;
		   $arr2[$value]=$r2lotBales[$key];
	}	
	

	$r2lot_no=$arr1;   
	$r2lotBales=$arr2; 




    	$bales = array();
	    $lotno = array();

	  

	    foreach ($r1lot_no as $key => $value) 
	    {

    		if(!isset($r2lot_no[$value])){
    			$lotno[] = $value;
    			$bales[] = $r1lotBales[$key];

    			//echo $value;
    		}


    		
    		if(isset($r2lot_no[$value]) && isset($r2lotBales[$value]) && $r1lotBales[$key] != $r2lotBales[$value])
    		{


    			$lotno[] = $value;
    			$bales[] = (string)($r1lotBales[$key] - $r2lotBales[$value]);

    			//echo $value;

    		}

	    }

	  

	    //add current record lot No & bales in total
	    if(isset($record_id) && $conf_no==$_POST['curRecord_conf_no'])
	    {

	    	   //check if record exist then fetch lot & bales
				$sql4="select lot_no,lot_bales from sales_conf_split where id = '".$record_id."'";
				$result4 = mysqli_query($conn, $sql4);
				
				$row4count=mysqli_num_rows($result4);
				if ($row4count>0) 
				{
					$lot_no4 = array();
					$lot_bales4 = array();

					$row4 = mysqli_fetch_assoc($result4);
					$lot_no4=json_decode($row4['lot_no'],true);
		    		$lot_bales4=json_decode($row4['lot_bales'],true);


		    		/*$mainArr=array();
		    		foreach ($lot_bales4 as $key => $bales) {
		    				$mainArr[$lot_no4[$key]]=$bales;
		    		}*/

		    		$lot_no4=array_merge($lotno,$lot_no4);
		    		$lot_bales4=array_merge($bales,$lot_bales4);


		    		$lot_no=array();
		    		$lot_bales=array();

		    		foreach ($lot_no4 as $key => $lotNo) {

		    			 $LastKey=array_search($lotNo, $lot_no);

		    			 if($LastKey!='')
		    			 {
		    			 	$lot_bales[$LastKey]+=$lot_bales4[$key];
		    			 }
		    			 else
		    			 {
		    			 	$lot_no[$lotNo]=$lotNo;
		    			 	$lot_bales[$lotNo]=$lot_bales4[$key];
		    			 }

		    		}


		    		ksort($lot_no);
		    		ksort($lot_bales);

		    		$lot_no=array_values($lot_no);
		    		$lot_bales=array_values($lot_bales);


		    		$lotno=$lot_no;
		    		$bales=$lot_bales;

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



	        //add current record lot No & bales in total
	    if(isset($record_id) && $conf_no==$_POST['curRecord_conf_no'])
	    {

	    	   //check if record exist then fetch lot & bales
				$sql4="select lot_no,lot_bales from sales_conf_split where id = '".$record_id."'";
				$result4 = mysqli_query($conn, $sql4);
				
				$row4count=mysqli_num_rows($result4);
				if ($row4count>0) 
				{
					$lot_no4 = array();
					$lot_bales4 = array();

					$row4 = mysqli_fetch_assoc($result4);
					$lot_no4=json_decode($row4['lot_no'],true);
		    		$lot_bales4=json_decode($row4['lot_bales'],true);


		    		/*$mainArr=array();
		    		foreach ($lot_bales4 as $key => $bales) {
		    				$mainArr[$lot_no4[$key]]=$bales;
		    		}*/

		    		$lot_no4=array_merge($lotno,$lot_no4);
		    		$lot_bales4=array_merge($bales,$lot_bales4);


		    		$lot_no=array();
		    		$lot_bales=array();

		    		foreach ($lot_no4 as $key => $lotNo) {

		    			 $LastKey=array_search($lotNo, $lot_no);

		    			 if($LastKey!='')
		    			 {
		    			 	$lot_bales[$LastKey]+=$lot_bales4[$key];
		    			 }
		    			 else
		    			 {
		    			 	$lot_no[$lotNo]=$lotNo;
		    			 	$lot_bales[$lotNo]=$lot_bales4[$key];
		    			 }

		    		}


		    		ksort($lot_no);
		    		ksort($lot_bales);

		    		$lot_no=array_values($lot_no);
		    		$lot_bales=array_values($lot_bales);


		    		$lotno=$lot_no;
		    		$bales=$lot_bales;

				}


	    }




		
		$row['lot_no']=$lot_no;
	    $row['lot_bales']=$bales;

		$response = $row;
		$response['status'] = 'new';
		echo json_encode($response);
	}




}		
?>