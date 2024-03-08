<?php

require_once('../db.php');



if(isset($_POST['conf_no']))

{

	$response=array();

	$conf_no=$_POST['conf_no'];





	$sql="select * from pur_report where conf_no='".$conf_no."'";

	$result = mysqli_query($conn, $sql);



	$dataArr=array();

	$i=0;

	if(mysqli_num_rows($result)>0)

	{

		while ($row=mysqli_fetch_assoc($result)) 

		{



			$dataArr[$i]['purchase_report_id']=$row['id'];

			$dataArr[$i]['invoice_no']=$row['invoice_no'];

			$dataArr[$i]['lot_no']=$row['lot_no'];

			$dataArr[$i]['lot_bales']=$row['bales'];

			$dataArr[$i]['firm_id']=$row['firm'];



			//get firm name

			$firm_name='';

			$sqlFirm = "select id,party_name from party where id='".$row['firm']."'";

			$resultFirm = mysqli_query($conn, $sqlFirm);

			if(mysqli_num_rows($resultFirm)>0)

			{

			 	$rowFirm=mysqli_fetch_assoc($resultFirm);

			    $firm_name=$rowFirm['party_name'];

			}



			$dataArr[$i]['firm_name']=$firm_name;





			//external party & conf No.

			$ext_party='';

			$sqlEx = "select partyname from external_party where id='".$row['party']."'";

			$resultEx = mysqli_query($conn, $sqlEx);

			if(mysqli_num_rows($resultEx)>0)

			{

			  $rowEx=mysqli_fetch_assoc($resultEx);

			  $ext_party=$rowEx['partyname'];

			}



			$dataArr[$i]['ext_conf_no']=$ext_party." (".$row['conf_no'].")";



					

			$i++;					

		}





		//get already used lot from comparison_report

		$usedArr=array();

		$PurUsedArr=array();



		$sql="select * from comparison_report";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result)>0)

		{

			$i=0;

			while ($row2=mysqli_fetch_assoc($result)) 

			{ 
				$tempUsed = json_decode($row2['purchase_data'],true); 
				if(isset($tempUsed['total_dispatch_bales'])){

					$tempUsed['total_dispatch_bales'] = (int)$tempUsed['total_dispatch_bales'];
				}else{
					$tempUsed['total_dispatch_bales'] =0;
				}

				$PurUsedArr[]=$tempUsed; 	

			}

		}

		

		$usedArr=array_merge(...$PurUsedArr);



		/*print_r($usedArr);

		exit;*/









		//if lot_no and report id is same then merge array into 1 array and addition of bales

		function search1($lot_no, $report_id, $array) 

		{

		  

		   foreach ($array as $key => $val) 

		   {

		       if ($val['lot_no'] == $lot_no && $val['purchase_report_id']==$report_id)

		        {

		           return $key;

		       	}

		       	

		   }

		   

		}







		





		$usedArr1=array();

		if(count($usedArr)>0)

		{

			foreach ($usedArr as $key => $item) 

			{



				$getKey=search1($item['lot_no'],$item['purchase_report_id'],$usedArr1);

				if(isset($getKey))

				{

					$usedArr1[$getKey]['total_dispatch_bales']=(int)$usedArr1[$getKey]['total_dispatch_bales'];

					$usedArr1[$getKey]['total_dispatch_bales']+=(int)$item['total_dispatch_bales'];

				}

				else

				{

					$usedArr1[]=$item;

				}

			}

			$usedArr=$usedArr1;

		}



		







	



		function searchArr($lot_no, $report_id, $array) 

		{

		   foreach ($array as $key => $val) 

		   {

		       if ($val['lot_no'] == $lot_no && $val['purchase_report_id']==$report_id)

		        {

		           return $key;

		       	}

		   }

		   

		}













		//if lot is used then deduct used qty from avl bales

		foreach ($dataArr as $key => $item) 

		{



			$usedLotArrKey=searchArr($item['lot_no'],$item['purchase_report_id'],$usedArr);



			if(isset($usedLotArrKey))

			{

				$dataArr[$key]['lot_bales']-=$usedArr[$usedLotArrKey]['total_dispatch_bales'];

			}





			if($dataArr[$key]['lot_bales']<=0)

			{

				unset($dataArr[$key]);

			}

		

		}

		

		

		$dataArr=array_values($dataArr);



		

	}



	$response=$dataArr;

	echo json_encode($response);

}



?>