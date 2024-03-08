<?php

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

function searchArr($report_id, $array) 

{

   foreach ($array as $key => $val) 

   {

       if ($val['purchase_report_id']==$report_id)

        {

           return $key;

       	}

   }

   

}



function getAvlBales($report_id,$curUsedBales)

{

	include('../db.php');



	$response=array();





	$total_bales=0;

	$avl_bales=0;

	$curRecordUsedBales=0;



	if($curUsedBales!='')

	{

		$curRecordUsedBales=$curUsedBales;

	}







	//get total bales from purchase report

	$sql="select * from pur_report where id='".$report_id."'";

	$result = mysqli_query($conn, $sql);

	if(mysqli_num_rows($result)>0)

	{

		$purRow=mysqli_fetch_assoc($result);

		$total_bales=$purRow['bales'];

	}



	





		//get already used bales from comparison_report

		$usedArr=array();

		$PurUsedArr=array();



		$sql="select * from comparison_report";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result)>0)

		{

			$i=0;

			while ($row2=mysqli_fetch_assoc($result)) 

			{ 

				$PurUsedArr[]=json_decode($row2['purchase_data'],true); 	

			}

			$usedArr=array_merge(...$PurUsedArr);

		}

		

		





		//if lot_no and report id is same then merge array into 1 array and addition of bales

		

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



	



		







		//deduct used bales from total_bales

		if(count($usedArr)>0)

		{

			$usedLotArrKey=searchArr($report_id,$usedArr);

			if(isset($usedLotArrKey))

			{

				$total_bales-=$usedArr[$usedLotArrKey]['total_dispatch_bales'];

			}

		}

	



		//add cur record bales in total bales

		$total_bales+=$curRecordUsedBales;





		return $total_bales;

}



?>