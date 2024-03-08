<?php
require_once('../db.php');

if(isset($_POST['conf_no']))
{
	$response=array();
	$conf_no=$_POST['conf_no'];


	$sql="select * from sales_report where conf_no='".$conf_no."'";
	$result = mysqli_query($conn, $sql);

	$dataArr=array();
	$i=0;
	if(mysqli_num_rows($result)>0)
	{
		while ($row=mysqli_fetch_assoc($result)) 
		{
			$lotArr=json_decode($row['lot_no'],true);
			$lotBales=json_decode($row['lot_bales'],true);

			if(count($lotArr)>0)
			{
				
				foreach ($lotArr as $key => $lot_no) 
				{
					$dataArr[$i]['sales_report_id']=$row['id'];
					$dataArr[$i]['invoice_no']=$row['invice_no'];
					$dataArr[$i]['invoice_date']=$row['invoice_date'];
					$dataArr[$i]['lot_no']=$lot_no;
					$dataArr[$i]['lot_bales']=$lotBales[$key];
					$dataArr[$i]['veh_id']=$row['truck'];

					//get vehicle no. based on truck_id from truck master
					$veh_no='';
					$sqlTruck="select * from truck_master where id='".$row['truck']."'";
					$resultTruck = mysqli_query($conn, $sqlTruck);
					if(mysqli_num_rows($resultTruck)>0)
					{
						$rowTruck=mysqli_fetch_assoc($resultTruck);
						$veh_no=$rowTruck['truck_no'];
					}

					$dataArr[$i]['veh_no']=$veh_no;





					//get delivery at
					$dataArr[$i]['delivery_at']=$row['delivery_city'];




					//get invoice rais name (firm)
					$firm_id='';
					$firm_name='';
					$sqlFirm="select * from party where id='".$row['firm']."'";
					$resultFirm = mysqli_query($conn, $sqlFirm);
					if(mysqli_num_rows($resultFirm)>0)
					{
						$rowFirm=mysqli_fetch_assoc($resultFirm);
						$firm_name=$rowFirm['party_name'];
						$firm_id=$rowFirm['id'];
					}
					$dataArr[$i]['invoice_raise_id']=$firm_id;
					$dataArr[$i]['invoice_raise_name']=$firm_name;




					$i++;					
				}
			}

		}





		//get already used lot from comparison_report
		$usedArr=array();
		$sql="select * from comparison_report where sales_conf_no='".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result)>0)
		{
			while ($row2=mysqli_fetch_assoc($result)) 
			{
			   $usedArr[]=json_decode($row2['sales_data'],true);
			}
		}
		$usedArr=array_merge(...$usedArr);
		

		function searchForId($lot_no, $report_id, $array) 
		{
		   foreach ($array as $key => $val) 
		   {
		       if ($val['lot_no'] == $lot_no && $val['sales_report_id']==$report_id)
		        {
		           return $key;
		       	}
		   }
		   return null;
		}


		//remove already used lot from $dataArr
		foreach ($dataArr as $key => $item) 
		{
			$serach=searchForId($item['lot_no'],$item['sales_report_id'],$usedArr);

			if(isset($serach))
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