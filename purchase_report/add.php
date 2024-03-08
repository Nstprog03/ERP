<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}

  	

  if (isset($_POST['Submit'])) {

    $party = explode("/",$_POST['party'])[0];
    $pur_conf_ids = explode("/",$_POST['party'])[1];
    $firm = $_POST['pur_firm'];
    $weight = $_POST['weight'];
    $conf_no=$_POST['pur_conf'];

    $avl_bales=$_POST['avl_bales'];
    $candy_rate=$_POST['candy_rate'];
    $lot_no=$_POST['lot_no'];
    $pr_no_start=$_POST['pr_no_start'];
    $pr_no_end=$_POST['pr_no_end'];
    $broker = $_POST['broker'];
    $bales	=  $_POST['bales'];
    $grs_amt =  $_POST['gross_amount'];
    $tax =  $_POST['tax'];
    $tax_amount =  $_POST['tax_amount'];
    $tcs =  $_POST['tcs'];
    $tcs_amount =  $_POST['tcs_amount'];
    $netpayableamt = $_POST['total_amount'];

    $financial_year=$_SESSION['pur_financial_year_id'];
    $invoice_no=$_POST['invoice_no'];
    $other_amt=$_POST['other_amt'];


    //transport details
    $trans_id=$_POST['trans_id'];
    $trans_pay_type=$_POST['trans_pay_type'];
    $trans_veh_no=$_POST['trans_veh_no'];

    if($trans_pay_type=='to_be_pay')
    {
	    $trans_lr_no=$_POST['trans_lr_no'];
	    $trans_amount=$_POST['trans_amount'];
	 	$trans_lr_date='';
	    if($_POST['trans_lr_date']!='')
	    {
	      $trans_lr_date = str_replace('/', '-', $_POST['trans_lr_date']);
	      $trans_lr_date = date('Y-m-d', strtotime($trans_lr_date));
	    }
    		
    }
    else
    {
	    $trans_lr_no='';
	    $trans_amount='';
	 	$trans_lr_date='';	
    }


 
	$reportDate='';
    if($_POST['report_date']!='')
    {
    	$reportDate = str_replace('/', '-', $_POST['report_date']);
	    $reportDate = date('Y-m-d', strtotime($reportDate));
    }


    include('../global_function.php'); 
    $data=getFileStoragePath("purchase_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path  



    $imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {
    	
    	$img_title = $_POST['img_title'][$key];
    	$img = $_FILES['doc_file']['name'][$key];
			$imgTmp = $_FILES['doc_file']['tmp_name'][$key];
			$imgSize = $_FILES['doc_file']['size'][$key];

	
	    if(!empty($img)){
				array_push($img_titleArr,$img_title);
				$imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

				$allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

				$img = time().'_'.rand(1000,9999).'.'.$imgExt;
				array_push($imgArr,$store_path.$img);

				if(in_array($imgExt, $allowExt)){

					if($imgSize < 5000000){
						move_uploaded_file($imgTmp ,$root_path.$img);
					}else{
						$errorMsg = 'Image too large';
						echo $errorMsg;
					}
				}else{
					$errorMsg = 'Please select a valid image';
					echo $errorMsg;
				}


			}
    }

    $imgTitle = implode(',', $img_titleArr);
    $imgStore = implode(',', $imgArr);

    



	$idvar = 0;
	$idval = $idvar++;

			$username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");

	/*$lotnoArr = $_POST['lotno'];
    $lotqtyArr = $_POST['lotqty'];
    $pressone_startArr = $_POST['presstart'];
    $pressone_endArr = $_POST['pressend'];*/

			$sql = "insert into pur_report (pur_conf_ids, party, firm, conf_no,avl_bales,cndy_rate,lot_no,pr_no_start,pr_no_end,broker,bales,grs_amt,txn,txn_amount,tcs,tcs_amount,netpayableamt,report_date,financial_year,invoice_no,other_amt,doc_file,img_title,weight,username,created_at,updated_at,trans_pay_type,trans_id,trans_veh_no,trans_lr_date,trans_lr_no,trans_amount) 
			values ('".$pur_conf_ids."','".$party."','".$firm."','".$conf_no."', '".$avl_bales."', '".$candy_rate."', '".$lot_no."', '".$pr_no_start."', '".$pr_no_end."', '".$broker."', '".$bales."', '".$grs_amt."', '".$tax."', '".$tax_amount."', '".$tcs."', '".$tcs_amount."','".$netpayableamt."','".$reportDate."','".$financial_year."','".$invoice_no."','".$other_amt."','".$imgStore."', '".$imgTitle."', '".$weight."', '".$username."', '".$timestamp."', '".$timestamp."', '".$trans_pay_type."', '".$trans_id."', '".$trans_veh_no."', '".$trans_lr_date."', '".$trans_lr_no."', '".$trans_amount."')";
			
			if ($conn->query($sql) === TRUE) 
			{
			    $last_id = $conn->insert_id;
			    //echo "New record created successfully. Last inserted ID is: " . $last_id;

			    $successMsg = 'New record added successfully';
				header('Location: index.php');


			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}



	/*	//bales Deduct

		$final_bales=$avl_bales-$bales;

		$sql_update="UPDATE pur_conf set bales='".$final_bales."' where pur_conf='".$conf_no."'";

		$result_update = mysqli_query($conn, $sql_update);

			if($result_update){
				$successMsg = 'bales deduction updated successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo  $errorMsg;
			}
*/





	$lotno = $_POST['lot_no'];
    $lotqty = $_POST['bales'];
    $prstart = $_POST['pr_no_start'];
    $prend = $_POST['pr_no_end'];



$sql = "insert into lotdetails (purId, lotno, lotqty, pressone, presstwo) values ('".$last_id."', '".$lotno."', '".$lotqty."', '".$prstart."', '".$prend."')";

$result = mysqli_query($conn, $sql);

			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo  $errorMsg;
			}

/*			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
			}*/
				
}


			
	
		

?>
