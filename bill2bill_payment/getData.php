<?php
session_start();
require_once('../db.php');

function getExternalPartyName($id)
{
    $name='';
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);
    if(mysqli_num_rows($partyresult)>0)
    {
    	$partyrow = mysqli_fetch_array($partyresult);
    	$name=$partyrow['partyname'];
    }
    return $name;
}
function getTransportName($id)
{
    $name='';
    include('../db.php');
    $party = "select * from transport where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);
    if(mysqli_num_rows($partyresult)>0)
    {
    	$partyrow = mysqli_fetch_array($partyresult);
    	$name=$partyrow['trans_name'];
    }
    return $name;
}




//get list of party table wise
if(isset($_POST['table']) && isset($_POST['getParty']))
{
	$data=array();

	$partyArr=array();

	if($_POST['table']=='pur_bales_payout')
	{
		$sql = "select DISTINCT party from debit_report where firm='".$_SESSION['b2bp_firm_id']."' AND financial_year='".$_SESSION['b2bp_financial_year_id']."'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)>0)
        {
        	$i=0;
        	while ($row = mysqli_fetch_assoc($result)) 
        	{
        		$party_sql="SELECT * FROM external_party WHERE id='".$row['party']."'";
        		$party_result = mysqli_query($conn, $party_sql);

        		$party_name='';
        		if(mysqli_num_rows($party_result)>0)
        		{
        			$party_row = mysqli_fetch_assoc($party_result);
        			$party_name=$party_row['partyname'];
        		}

        		$partyArr[$i]['id']=$row['party'];
        		$partyArr[$i]['party_name']=$party_name;
        		$i++;
        		
        	}
        }

	}
	else if($_POST['table']=='rd_kapas_pur_payment')
	{

		 $sql = "select distinct(external_party) from rd_kapas_report where firm='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)>0)
        {
        	$i=0;
        	while ($row = mysqli_fetch_assoc($result)) 
        	{
        		$party_sql="SELECT * FROM external_party WHERE id='".$row['external_party']."'";
        		$party_result = mysqli_query($conn, $party_sql);

        		$party_name='';
        		if(mysqli_num_rows($party_result)>0)
        		{
        			$party_row = mysqli_fetch_assoc($party_result);
        			$party_name=$party_row['partyname'];
        		}

        		$partyArr[$i]['id']=$row['external_party'];
        		$partyArr[$i]['party_name']=$party_name;
        		$i++;
        		
        	}
        }


	}
	else if($_POST['table']=='transport_payout')
	{

		$sql = "select DISTINCT trans_id from pur_report where firm='".$_SESSION['b2bp_firm_id']."' AND financial_year='".$_SESSION['b2bp_financial_year_id']."' AND trans_pay_type='to_be_pay' AND invoice_no != ''";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)>0)
        {
        	$i=0;
        	while ($row = mysqli_fetch_assoc($result)) 
        	{
        		$party_sql="SELECT * FROM transport WHERE id='".$row['trans_id']."'";
        		$party_result = mysqli_query($conn, $party_sql);

        		$party_name='';
        		if(mysqli_num_rows($party_result)>0)
        		{
        			$party_row = mysqli_fetch_assoc($party_result);
        			$party_name=$party_row['trans_name'];
        		}

        		$partyArr[$i]['id']=$row['trans_id'];
        		$partyArr[$i]['party_name']=$party_name;
        		$i++;
        		
        	}
        }
		
	}
	else if($_POST['table']=='sales_receivable')
	{

		 $sql = "select DISTINCT party_name from sales_report where firm='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";

        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result)>0)
        {
        	$i=0;
        	while ($row = mysqli_fetch_assoc($result)) 
        	{
        		$party_sql="SELECT * FROM external_party WHERE id='".$row['party_name']."'";
        		$party_result = mysqli_query($conn, $party_sql);

        		$party_name='';
        		if(mysqli_num_rows($party_result)>0)
        		{
        			$party_row = mysqli_fetch_assoc($party_result);
        			$party_name=$party_row['partyname'];
        		}

        		$partyArr[$i]['id']=$row['party_name'];
        		$partyArr[$i]['party_name']=$party_name;
        		$i++;
        		
        	}
        }


	}



	$data['party']=$partyArr;
	
	echo json_encode($data);
}





//get list of invoice no. table wise
if(isset($_POST['table']) && isset($_POST['party_id']) && isset($_POST['getInvoice']))
{
	$party=$_POST['party_id'];

	$data=array();
	$invoiceArr=array();

	if($_POST['table']=='pur_bales_payout')
	{

		//get list of debit report id which have 0 pay_amt
		$idArr=array();
		$sqlCheck="select debit_report_id from pur_pay where pay_amt IN('0.00','0') AND party='".$party."' AND firm_id='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";
		$resultCheck = mysqli_query($conn, $sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
			{
				$idArr[]=$rowGet['debit_report_id'];
			}
		}


		$sql="select * from debit_report where party='".$party."' AND invoice_no != '' AND firm='".$_SESSION['b2bp_firm_id']."' AND financial_year='".$_SESSION['b2bp_financial_year_id']."'";
		$result = mysqli_query($conn, $sql);
		$i=0;
		while ($rowInvoice=mysqli_fetch_assoc($result)) 
	    {
	    	//check if amount to be pay is not zero 0
	    	if(!in_array($rowInvoice['id'], $idArr))
	    	{
	    		$invoiceArr[$i]['invoice_no'] =  $rowInvoice['invoice_no'];
	    		$invoiceArr[$i]['report_id'] =  $rowInvoice['id']; 
	    		$i++;
	    		
	    	}
	    	

		}
		echo json_encode($invoiceArr);	

	}
	else if($_POST['table']=='rd_kapas_pur_payment')
	{

		//get data from rd kapas payment if full payment is done
		$idArr=array();
		$sqlCheck="select rd_kapas_report_id from rd_kapas_payment where pay_amt IN('0.00','0') AND party='".$party."'";
		$resultCheck = mysqli_query($conn, $sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
			{
				$idArr[]=$rowGet['rd_kapas_report_id'];
			}
		}



		$sql = "select * from rd_kapas_report where external_party='".$party."' AND firm='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";
			$i=0;
		  foreach ($conn->query($sql) as $key=> $result) 
		  {
		 	if(!in_array($result['id'], $idArr))
	    	{
	    		$invoiceArr[$i]['invoice_no'] =  $result['invoice_no'];
	    		$invoiceArr[$i]['report_id'] =  $result['id']; 
	    		$i++;
	    	}
		  	
		  }

		  echo json_encode($invoiceArr);

		


	}
	else if($_POST['table']=='transport_payout')
	{

			//check transport payout lalready created
			$idArr=array();
			$sqlCheck="select * from transport_payout where firm_id='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";
			$resultCheck = mysqli_query($conn, $sqlCheck);
			if(mysqli_num_rows($resultCheck)>0)
			{
				while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
				{
			    	$idArr[]=$rowGet['pur_report_id'];
				}
			}


			//check bill 2 bill payment already created
			if(isset($_POST['curRecordId'])) //edit record
			{
				$sqlB2b="select * from bill2bill_sub_data where table_indicator='transport_payout' AND id!='".$_POST['curRecordId']."'";
			}
			else //create record
			{
				$sqlB2b="select * from bill2bill_sub_data where table_indicator='transport_payout'";
			}
				

			$resultB2b = mysqli_query($conn, $sqlB2b);
			if(mysqli_num_rows($resultB2b)>0)
			{
				while ($rowB2b=mysqli_fetch_assoc($resultB2b)) 
				{
					$idArr[]=$rowB2b['report_id'];
				}
			}



			$sql="select id,trans_lr_no from pur_report where trans_id='".$party."' AND firm='".$_SESSION['b2bp_firm_id']."' AND financial_year='".$_SESSION['b2bp_financial_year_id']."' AND trans_pay_type='to_be_pay' AND trans_lr_no!= '' ";

			$result = mysqli_query($conn, $sql);
			$i=0;
			foreach ($conn->query($sql) as $key => $row) 
		    {
		    		//remove already created transport payout entry in transport payout & also in bill 2 bill payment
		    		if(!in_array($row['id'], $idArr))
		    		{
		    			$invoiceArr[$i]['invoice_no'] =  $row['trans_lr_no'];
			    		$invoiceArr[$i]['report_id'] =  $row['id']; 
			    		$i++;
		    		}
			}
			echo json_encode($invoiceArr);

		
	}

	else if($_POST['table']=='sales_receivable')
	{

		//get data from rd kapas payment if full payment is done
		$idArr=array();
		$sqlCheck="select sale_report_id from sales_rcvble where OSAmount IN('0.00','0') AND pur_party='".$party."'";
		$resultCheck = mysqli_query($conn, $sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
			{
				$idArr[]=$rowGet['sale_report_id'];
			}
		}



		$sql = "select * from sales_report where party_name='".$party."' AND invice_no != '' AND firm='".$_SESSION['b2bp_firm_id']."' AND financial_year_id='".$_SESSION['b2bp_financial_year_id']."'";
			$i=0;
		  foreach ($conn->query($sql) as $key=> $result) 
		  {
		 	if(!in_array($result['id'], $idArr))
	    	{
	    		$invoiceArr[$i]['invoice_no'] =  $result['invice_no'];
	    		$invoiceArr[$i]['report_id'] =  $result['id']; 
	    		$i++;
	    	}
		  	
		  }

		  echo json_encode($invoiceArr);

		


	}

	
}






//get record data by table
if(isset($_POST['table']) && isset($_POST['record_id']) && isset($_POST['getRecord']))
{
	$id=$_POST['record_id'];

	$data=array();

	if($_POST['table']=='pur_bales_payout')
	{

		//check if bales payout created - if created the grab amt to be pay. if not created then grab from debit report with manually calculation.
		$sqlCheck="select * from pur_pay where debit_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			$data['invoice_no']=$row2['invoice_no'];
			$data['party_id']=$row2['party'];
			$data['party_name']=getExternalPartyName($row2['party']);
			$data['table']=$_POST['table'];
			$data['report_id']=$row2['debit_report_id'];


			$data['amt_to_be_pay']=$row2['pay_amt'];


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
			$data['amt_to_be_pay']-=$B2bUsedAmount;



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

				$data['amt_to_be_pay']=$amt_to_be_pay;
				$data['invoice_no']=$row['invoice_no'];
				$data['party_id']=$row['party'];
				$data['party_name']=getExternalPartyName($row['party']);
				$data['table']=$_POST['table'];
				$data['report_id']=$row['id'];

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

				$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
				$data['amt_to_be_pay']-=$B2bUsedAmount;


			}	

		}

		


		//if record is EDIT and report id match the add current record amount to amt to be pay
		$curRecordAmount=0;
		if(isset($_POST['curRecordId']))
		{

			$sqlCur="select * from bill2bill_sub_data where bill2bill_id='".$_POST['curRecordId']."' AND table_indicator='pur_bales_payout' AND report_id='".$id."'";
			$resultCur=mysqli_query($conn,$sqlCur);
			if(mysqli_num_rows($resultCur)>0)
			{
				$rowCur=mysqli_fetch_assoc($resultCur);

				if($rowCur['payment']!='')
				{
					$curRecordAmount+=$rowCur['payment'];
				}

			}
		}
		$curRecordAmount=number_format($curRecordAmount, 2, '.', '');
		$data['amt_to_be_pay']+=$curRecordAmount;
		

		
		echo json_encode($data);	

	}
	else if($_POST['table']=='rd_kapas_pur_payment')
	{


		//check in RD purchase payment - if created the grab amt to be pay. if not created then grab from rd kapas purchase report
		$sqlCheck="select * from rd_kapas_payment where rd_kapas_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			$data['amt_to_be_pay']=$row2['pay_amt'];
			$data['invoice_no']=$row2['invoice_no'];
			
			$data['party_id']=$row2['party'];
			$data['party_name']=getExternalPartyName($row2['party']);
			$data['table']=$_POST['table'];
			$data['report_id']=$row2['rd_kapas_report_id'];

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
			$data['amt_to_be_pay']-=$B2bUsedAmount;

		}
		else
		{
			$sql="select * from rd_kapas_report where id='".$id."'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				//$data=$row;

				$data['amt_to_be_pay']=$row['net_amt'];
				$data['invoice_no']=$row['invoice_no'];
				$data['party_id']=$row['external_party'];
				$data['party_name']=getExternalPartyName($row['external_party']);
				$data['table']=$_POST['table'];
				$data['report_id']=$row['id'];


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
				$B2bUsedAmount=number_format($B2bUsedAmount, 2, '.', '');
				$data['amt_to_be_pay']-=$B2bUsedAmount;

			}
		}

		


		//if record is EDIT and report id match the add current record amount to amt to be pay
		$curRecordAmount=0;
		if(isset($_POST['curRecordId']))
		{

			$sqlCur="select * from bill2bill_sub_data where bill2bill_id='".$_POST['curRecordId']."' AND table_indicator='rd_kapas_pur_payment' AND report_id='".$id."'";
			$resultCur=mysqli_query($conn,$sqlCur);
			if(mysqli_num_rows($resultCur)>0)
			{
				$rowCur=mysqli_fetch_assoc($resultCur);
				if($rowCur['payment']!='')
				{
					$curRecordAmount+=$rowCur['payment'];
				}

			}
		}
		$curRecordAmount=number_format($curRecordAmount, 2, '.', '');
		$data['amt_to_be_pay']+=$curRecordAmount;



		

		echo json_encode($data);

	}
	else if($_POST['table']=='transport_payout')
	{

		$sql="select * from pur_report where id='".$id."'";
		$result = mysqli_query($conn, $sql);
		if(mysqli_num_rows($result)>0)
		{
			$row=mysqli_fetch_assoc($result);
			//$data=$row;

			$data['amt_to_be_pay']=$row['trans_amount'];
			$data['invoice_no']=$row['trans_lr_no'];
			$data['party_id']=$row['trans_id'];
			$data['party_name']=getTransportName($row['trans_id']);
			$data['table']=$_POST['table'];
			$data['report_id']=$row['id'];
		}

			
		echo json_encode($data);
	}
	else if($_POST['table']=='sales_receivable')
	{


		//check in RD purchase payment - if created the grab amt to be pay. if not created then grab from rd kapas purchase report
		$sqlCheck="select * from sales_rcvble where sale_report_id='".$id."'";
		$resultCheck=mysqli_query($conn,$sqlCheck);
		if(mysqli_num_rows($resultCheck)>0)
		{
			$row2=mysqli_fetch_assoc($resultCheck);
			$data['amt_to_be_pay']=$row2['OSAmount'];
			$data['invoice_no']=$row2['pur_invoice_no'];
			
			$data['party_id']=$row2['pur_party'];
			$data['party_name']=getExternalPartyName($row2['pur_party']);
			$data['table']=$_POST['table'];
			$data['report_id']=$row2['sale_report_id'];

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
			$data['amt_to_be_pay']-=$B2bUsedAmount;

		}
		else
		{
			$sql="select * from sales_report where id='".$id."'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result)>0)
			{
				$row=mysqli_fetch_assoc($result);
				//$data=$row;

				$data['amt_to_be_pay']=$row['total_value'];
				$data['invoice_no']=$row['invice_no'];
				$data['party_id']=$row['party_name'];
				$data['party_name']=getExternalPartyName($row['party_name']);
				$data['table']=$_POST['table'];
				$data['report_id']=$row['id'];


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
				$data['amt_to_be_pay']-=$B2bUsedAmount;

			}
		}

		


		//if record is EDIT and report id match the add current record amount to amt to be pay
		$curRecordAmount=0;
		if(isset($_POST['curRecordId']))
		{

			$sqlCur="select * from bill2bill_sub_data where bill2bill_id='".$_POST['curRecordId']."' AND table_indicator='sales_receivable' AND report_id='".$id."'";
			$resultCur=mysqli_query($conn,$sqlCur);
			if(mysqli_num_rows($resultCur)>0)
			{
				$rowCur=mysqli_fetch_assoc($resultCur);
				if($rowCur['payment']!='')
				{
					$curRecordAmount+=$rowCur['payment'];
				}

			}
		}
		$curRecordAmount=number_format($curRecordAmount, 2, '.', '');
		$data['amt_to_be_pay']+=$curRecordAmount;



		

		echo json_encode($data);

	}

	
}




?>