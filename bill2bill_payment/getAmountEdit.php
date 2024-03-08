<?php
//get record data by table
function getAmtTobePay ($table,$record_id)
{
	include('../db.php');
	$id=$record_id;

	$amount=0;

	if($table=='pur_bales_payout')
	{
		//check if bales payout created - if created the grab amt to be pay. if not created then grab from debit report with manually calculation.
		$sqlCheck="select * from pur_pay where debit_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			$amount=$row2['pay_amt'];

			//check already add bill 2 bill payment in bales payout
			$checkArr=array();
			if(isset($row2['bill2bill_dynamic_data']) && $row2['bill2bill_dynamic_data']!='')
			{
				$arr=json_decode($row2['bill2bill_dynamic_data'],true);
				if(isset($arr))
				{
					foreach ($arr as $key => $item) 
					{
						$checkArr[]=$item['b2b_id'];
					}
					
				}
			}


			//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
			$B2bUsedAmount=0;
			$sqlB2b="select * from bill2bill_sub_data where table_indicator='pur_bales_payout' AND report_id='".$id."'";
			$resultB2b = mysqli_query($conn, $sqlB2b);
			if(mysqli_num_rows($resultB2b)>0)
			{
				while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
				{
					if(!in_array($rowB2b['id'], $checkArr) && $rowB2b['payment']!='')
					{
						$B2bUsedAmount+=$rowB2b['payment'];
					}
				}
			}

			$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
			$amount-=$B2bUsedAmount;




		}
		else
		{
			$sql="select * from debit_report where id='".$id."'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				//$data=$row;

				$gross_amount=0;
				if($row['gross_amt']!='')
				{
					$gross_amount=$row['gross_amt'];
				}

				$final_debit_amount_with_tax=0;
				if($row['gross_amt']!='')
				{
					$final_debit_amount_with_tax=$row['final_debit_amount'];
				}

				$ad_hoc=0;
				if($row['ad_hoc']!='')
				{
					$ad_hoc=$row['ad_hoc'];
				}

				//formula --> Gross Amount + 5% - Final Debit Amount With Tax - Ad-Hoc 
				$amt_to_be_pay=$gross_amount+($gross_amount*5/100)-$final_debit_amount_with_tax-$ad_hoc;
				$amt_to_be_pay=number_format($amt_to_be_pay, 2, '.', '');


				$amount=$amt_to_be_pay;


				//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
				$B2bUsedAmount=0;
				$sqlB2b="select * from bill2bill_sub_data where table_indicator='pur_bales_payout' AND report_id='".$id."'";
				$resultB2b = mysqli_query($conn, $sqlB2b);
				if(mysqli_num_rows($resultB2b)>0)
				{
					while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
					{
						if($rowB2b['payment']!='')
						{
							$B2bUsedAmount+=$rowB2b['payment'];
						}
					}
				}

				$amount-=$B2bUsedAmount;	
						

			}	

		}

		

	}
	else if($table=='rd_kapas_pur_payment')
	{


		//check in RD purchase payment - if created the grab amt to be pay. if not created then grab from rd kapas purchase report
		$sqlCheck="select * from rd_kapas_payment where rd_kapas_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			
			$amount=$row2['pay_amt'];

			//check already add bill 2 bill payment in bales payout
			$checkArr=array();
			if(isset($row2['bill2bill_dynamic_data']) && $row2['bill2bill_dynamic_data']!='')
			{
				$arr=json_decode($row2['bill2bill_dynamic_data'],true);
				if(isset($arr))
				{
					foreach ($arr as $key => $item) 
					{
						$checkArr[]=$item['b2b_id'];
					}
					
				}
			}


			//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
			$B2bUsedAmount=0;
			$sqlB2b="select * from bill2bill_sub_data where table_indicator='rd_kapas_pur_payment' AND report_id='".$id."'";
			$resultB2b = mysqli_query($conn, $sqlB2b);
			if(mysqli_num_rows($resultB2b)>0)
			{
				while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
				{
					if(!in_array($rowB2b['id'], $checkArr) && $rowB2b['payment']!='')
					{
						$B2bUsedAmount+=$rowB2b['payment'];
					}
				}
			}

			$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
			$amount-=$B2bUsedAmount;
			
		}
		else
		{
			$sql="select * from rd_kapas_report where id='".$id."'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				$amount=$row['net_amt'];

				//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
				$B2bUsedAmount=0;
				$sqlB2b="select * from bill2bill_sub_data where table_indicator='rd_kapas_pur_payment' AND report_id='".$id."'";
				$resultB2b = mysqli_query($conn, $sqlB2b);
				if(mysqli_num_rows($resultB2b)>0)
				{
					while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
					{
						if($rowB2b['payment']!='')
						{
							$B2bUsedAmount+=$rowB2b['payment'];
						}
					}
				}

				$amount-=$B2bUsedAmount;
			}
		}

		

	}
	else if($table=='sales_receivable')
	{


		//check in RD purchase payment - if created the grab amt to be pay. if not created then grab from rd kapas purchase report
		$sqlCheck="select * from sales_rcvble where sale_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			$amount=$row2['OSAmount'];

			//check already add bill 2 bill payment in bales payout
			$checkArr=array();
			if(isset($row2['bill2bill_dynamic_data']) && $row2['bill2bill_dynamic_data']!='')
			{
				$arr=json_decode($row2['bill2bill_dynamic_data'],true);
				if(isset($arr))
				{
					foreach ($arr as $key => $item) 
					{
						$checkArr[]=$item['b2b_id'];
					}
					
				}
			}


			//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
			$B2bUsedAmount=0;
			$sqlB2b="select * from bill2bill_sub_data where table_indicator='sales_receivable' AND report_id='".$id."'";
			$resultB2b = mysqli_query($conn, $sqlB2b);
			if(mysqli_num_rows($resultB2b)>0)
			{
				while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
				{
					if(!in_array($rowB2b['id'], $checkArr) && $rowB2b['payment']!='')
					{
						$B2bUsedAmount+=$rowB2b['payment'];
					}
				}
			}

			$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
			$amount-=$B2bUsedAmount;

		}
		else
		{
			$sql="select * from sales_report where id='".$id."'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				//$data=$row;

				$amount=$row['total_value'];

				//check in bill 2 bill payment if any record created with same id the deduct payment amount from amt to bey pay
				$B2bUsedAmount=0;
				$sqlB2b="select * from bill2bill_sub_data where table_indicator='sales_receivable' AND report_id='".$id."'";
				$resultB2b = mysqli_query($conn, $sqlB2b);
				if(mysqli_num_rows($resultB2b)>0)
				{
					while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
					{
						if($rowB2b['payment']!='')
						{
							$B2bUsedAmount+=$rowB2b['payment'];
						}
						
					}
				}
				$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
				$amount-=$B2bUsedAmount;

			}
		}

	}







	
	$amount=number_format($amount, 2, '.', '');
	return $amount;
}

?>