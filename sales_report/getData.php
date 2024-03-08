<?php
require_once('../db.php');
if (isset($_POST['conf_no']) && isset($_POST['table'])) {
	
	if($_POST['table'] == 'conf') 
	{

		$conf_no=$_POST['conf_no'];
		$sql="select * from seller_conf where sales_conf = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();


	//------------check existing record in confirmation split START ----------

		// get all record from sales conf split with same conf no.
		$sql_split="select lot_no,lot_bales,no_of_bales from sales_conf_split where conf_no = '".$conf_no."'";
		$result_split = mysqli_query($conn, $sql_split);
		$row_count_split=mysqli_num_rows($result_split);

		
		$finalSplitLot=array();
		$finalSplitBales=array();
		$splitUsedBales=0;

		if($row_count_split>0)
		{
			$lotno_split = array();
		    $lot_bales_split = array();
			while($row_split = mysqli_fetch_assoc($result_split))
			{
				$lotno_split[]=json_decode($row_split['lot_no']);
		    	$lot_bales_split[]=json_decode($row_split['lot_bales']);
		    	$splitUsedBales+=$row_split['no_of_bales'];

			}

			//merge all records in one array
			$newSplitLotArr = array_merge(...$lotno_split);
			$newSplitBalesArr=array_merge(...$lot_bales_split);

		

			foreach ($newSplitLotArr as $key => $value) 
			{
				//if duplicate lot_no found then it will merge LOT Bales
				$getkey_split = array_search($value, $finalSplitLot);
			    if($getkey_split == FALSE)
			    {
			       $finalSplitLot[$value]=$value;
			       $finalSplitBales[$value]=$newSplitBalesArr[$key];
			    }
			    else
			    {
			    	//duplicate LOT found so merge bales in same LOT
			    	$finalSplitBales[$getkey_split]+=$newSplitBalesArr[$key];
			    }
			}

			

			//sort by key
			ksort($finalSplitLot);
			ksort($finalSplitBales);

		


		/*	//rearrange array key from 0
			$finalSplitLot = array_values($finalSplitLot);
			$finalSplitBales = array_values($finalSplitBales);


			$a1SplitArr=array();
		    $b2SplitArr=array();
		    foreach ($finalSplitLot as $key => $value) {
		    	$a1SplitArr[$value]=$value;
		    	$b2SplitArr[$value]=$finalSplitBales[$key];
		    }

		    ksort($a1SplitArr);
		    ksort($b2SplitArr);

		    $finalSplitLot=$a1SplitArr;
		    $finalSplitBales=$b2SplitArr;*/

		   


			

		}




		

	//------------check existing record in confirmation split END ----------


		


		$sql2="SELECT SUM(noOFBales) as used_bales FROM sales_report WHERE conf_no='".$conf_no."'";	
		$result2 = mysqli_query($conn, $sql2);
		$row2check=mysqli_fetch_array($result2);
		if($row2check['used_bales']=='' || $row2check['used_bales']==null )
		{
			if($splitUsedBales!=0)
			{
				$r1lot_no=json_decode($row['lot_no']);
				$r1lotBales=json_decode($row['lot_bales']);

				 //$r3lot_no=array_values($finalSplitLot);
	    		 //$r3lotBales=array_values($finalSplitBales);

	    		 $r3lot_no=$finalSplitLot;
	    		 $r3lotBales=$finalSplitBales;

	    		
				    $bales = array();
				    $lotno = array();
				    foreach ($r1lot_no as $key => $value) 
				    {
			    		if(!isset($r3lot_no[$value]))
			    		{
			    			$lotno[] = $value;
			    			$bales[] = $r1lotBales[$key];
			    		}
			    		if(isset($r3lot_no[$value]) && isset($r3lotBales[$value]) && $r1lotBales[$key] != $r3lotBales[$value])
			    		{
			    			$lotno[] = $value;
			    			$bales[] = (string)($r1lotBales[$key] - $r3lotBales[$value]);
			    		}
				    }

				    /* print_r($lotno);
		    		 print_r($bales);
		    		 exit;*/

				    
				    $row['lot_no']=json_encode($lotno);
				    $row['lot_bales']=json_encode($bales);

				$row += array("used_bales"=>$splitUsedBales);
			}
			else
			{
				$row += array("used_bales"=>0);
			}
			
		}
		else
		{



			// get all record from sales report with same conf no.
			$sql3="select lot_no,lot_bales from sales_report where conf_no = '".$conf_no."'";
			$result3 = mysqli_query($conn, $sql3);

		    $lotno1 = array();
			$lot_bales2 = array();
			while($row31 = mysqli_fetch_assoc($result3))
			{
				$lotno1[]=json_decode($row31['lot_no']);
		    	$lot_bales2[]=json_decode($row31['lot_bales']);

			}




			//merge all records in one array
			$newLotArr = array_merge(...$lotno1);
			$newBalesArr=array_merge(...$lot_bales2);


			$finalLot=array();
			$finalBales=array();


			foreach ($newLotArr as $key => $value) 
			{
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



		/*//rearrange array key from 0
		$finalLot = array_values($finalLot);
		$finalBales = array_values($finalBales);*/


		$r1lot_no=json_decode($row['lot_no']);
		$r1lotBales=json_decode($row['lot_bales']);



		/*$a1Arr=array();
	    $b2Arr=array();
	    foreach ($finalLot as $key => $value) 
	    {
	    	$a1Arr[$value]=$value;
	    	$b2Arr[$value]=$finalBales[$key];
	    }

	    ksort($a1Arr);
	    ksort($b2Arr);

	    $r3lot_no=array_values($a1Arr);
	    $r3lotBales=array_values($b2Arr);*/

	    $r3lot_no=$finalLot;
	    $r3lotBales=$finalBales;


	 
	    





	    if(count($finalSplitLot)!=0 || count($finalSplitBales)!=0)
	    {
	    	
	    	$a=array_merge($finalSplitLot,$r3lot_no);
	    	$b=array_merge($finalSplitBales,$r3lotBales);

	    	

	    	$finalLotArr=array();
	    	$finalBalesArr=array();

	    	foreach ($a as $key => $value) 
			{
				//if duplicate lot_no found then it will merge LOT Bales
				$getkey1 = array_search($value, $finalLotArr,TRUE);
			    if($getkey1 == FALSE)
			    {
			       $finalLotArr[$value]=$value;
			       $finalBalesArr[$value]=$b[$key];
			    }
			    else
			    {
			    	//duplicate LOT found so merge bales in same LOT
			    	$finalBalesArr[$getkey1]+=$b[$key];
			    }
			}

			ksort($finalLotArr);
			ksort($finalBalesArr);

			$r3lot_no=$finalLotArr;
			$r3lotBales=$finalBalesArr;

			//$r3lot_no=array_values($finalLotArr);
			//$r3lotBales=array_values($finalBalesArr);
			/*
			print_r($r3lot_no);
	    	print_r($r3lotBales);

	    	exit;*/
	    	
	    }


	   /* $r3lot_no=json_decode($row3['lot_no']);
	    $r3lotBales=json_decode($row3['lot_bales']);*/

		   

		    $bales = array();
		    $lotno = array();
		    foreach ($r1lot_no as $key => $value) 
		    {
	    		if(!isset($r3lot_no[$value]))
	    		{
	    			$lotno[] = $value;
	    			$bales[] = $r1lotBales[$key];
	    		}
	    		if(isset($r3lot_no[$value]) && isset($r3lotBales[$value]) && $r1lotBales[$key] != $r3lotBales[$value])
	    		{
	    			$lotno[] = $value;
	    			$bales[] = (string)($r1lotBales[$key] - $r3lotBales[$value]);
	    		}
		    }



		  //if record is edit....add current record lot No & bales in main arr
	    if(isset($_POST['curRecordId']) && $conf_no==$_POST['curRecordConfNo'])
	    {
	    		$record_id=$_POST['curRecordId'];

	    	   //check if record exist then fetch lot & bales
				$sql4="select lot_no,lot_bales from sales_report where id = '".$record_id."'";
				$result4 = mysqli_query($conn, $sql4);
				
				$row4count=mysqli_num_rows($result4);
				if ($row4count>0) 
				{
					$lot_no4 = array();
					$lot_bales4 = array();

					$row4 = mysqli_fetch_assoc($result4);
					$lot_no4=json_decode($row4['lot_no'],true);
		    		$lot_bales4=json_decode($row4['lot_bales'],true);
		    		

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


		    
		    $row['lot_no']=json_encode($lotno);
		    $row['lot_bales']=json_encode($bales);




			$row += array(
				"used_bales"=>$row2check['used_bales']+$splitUsedBales
			);
		}		
		
		echo json_encode($row);
		
    }
	else if ($_POST['table'] == 'confsplit') 
	{

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



		    $lotno1 = array();
			$lot_bales2 = array();
			while($row31 = mysqli_fetch_assoc($result3))
			{
				$lotno1[]=json_decode($row31['lot_no']);
		    	$lot_bales2[]=json_decode($row31['lot_bales']);

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

			ksort($finalLot);
			ksort($finalBales);

			/*print_r($finalLot);
			print_r($finalBales);
			exit;*/

		//rearrange array key from 0
		// $finalLot = array_values($finalLot);
		// $finalBales = array_values($finalBales);


		$r1lot_no=json_decode($row['lot_no']);
		$r1lotBales=json_decode($row['lot_bales']);



	/*	$a1Arr=array();
	    $b2Arr=array();
	    foreach ($finalLot as $key => $value) {
	    	$a1Arr[$value]=$value;
	    	$b2Arr[$value]=$finalBales[$key];
	    }



	    ksort($a1Arr);
	    ksort($b2Arr);*/

	    $r3lot_no=$finalLot;
	    $r3lotBales=$finalBales;

	   

		   /* $r3lot_no=json_decode($row3['lot_no']);
		    $r3lotBales=json_decode($row3['lot_bales']);*/

		    $r1lot_no=json_decode($row['lot_no']);
		    $r1lotBales=json_decode($row['lot_bales']);

		    $bales = array();
		    $lotno = array();
		    foreach ($r1lot_no as $key => $value) 
		    {
	    		if(!isset($r3lot_no[$value])){
	    			$lotno[] = $value;
	    			$bales[] = $r1lotBales[$key];
	    		}
	    		if(isset($r3lot_no[$value]) && isset($r3lotBales[$value]) && $r1lotBales[$key] != $r3lotBales[$value]){
	    			$lotno[] = $value;
	    			$bales[] = (string)($r1lotBales[$key] - $r3lotBales[$value]);
	    		}
		    }




		      //if record is edit....add current record lot No & bales in main arr
	    if(isset($_POST['curRecordId']) && $conf_no==$_POST['curRecordConfNo'])
	    {
	    		$record_id=$_POST['curRecordId'];

	    	   //check if record exist then fetch lot & bales
				$sql4="select lot_no,lot_bales from sales_report where id = '".$record_id."'";
				$result4 = mysqli_query($conn, $sql4);
				
				$row4count=mysqli_num_rows($result4);
				if ($row4count>0) 
				{
					$lot_no4 = array();
					$lot_bales4 = array();

					$row4 = mysqli_fetch_assoc($result4);
					$lot_no4=json_decode($row4['lot_no'],true);
		    		$lot_bales4=json_decode($row4['lot_bales'],true);
		    		

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