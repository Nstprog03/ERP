<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

  if (isset($_POST['Submit'])) {


  	//sales 
  	$sales_conf_no=$_POST['sales_conf_no'];

    $sales_bales=$_POST['sales_bales'];
    $sales_total_bales=$_POST['sales_total_bales'];
    $delivery_at=$_POST['delivery_at'];
    $invoice_raise=$_POST['invoice_raise'];



  	
  	$saleArrFinal=array();

    if(isset($_POST['sales_lot_no']))
    {
        $sales_lot_no=$_POST['sales_lot_no'];
        $sales_lot_bales=$_POST['sales_lot_bales'];
        $sales_invoice_no=$_POST['sales_invoice_no'];
        $sales_veh_id=$_POST['sales_veh_id'];
        $sales_invoice_date=$_POST['sales_invoice_date'];
        $sales_report_id=$_POST['sales_report_id'];

        foreach ($sales_lot_no as $key => $lot_no) 
        {
          $saleArrFinal[$key]['lot_no']=$lot_no;
          $saleArrFinal[$key]['lot_bales']=$sales_lot_bales[$key];
          $saleArrFinal[$key]['invoice_no']=$sales_invoice_no[$key];
          $saleArrFinal[$key]['veh_id']=$sales_veh_id[$key];
          $saleArrFinal[$key]['invoice_date']=$sales_invoice_date[$key];
          $saleArrFinal[$key]['sales_report_id']=$sales_report_id[$key];

        }
    }
  
  	$saleArrFinal=json_encode($saleArrFinal);






  	
    //purchase 
    $PurArrFinal=array();

    if(isset($_POST['pur_lot_no']))
    {
        $pur_lot_no=$_POST['pur_lot_no'];
        $pur_lot_bales=$_POST['pur_lot_qty'];
        $pur_invoice_no=$_POST['pur_invoice_no'];
        $pur_conf_no=$_POST['pconf_no'];
        $purchase_report_id=$_POST['purchase_report_id'];
        $total_dispatch_bales = $_POST['total_dispatch_bales'];
        $used_firm_bales = $_POST['used_firm_bales'];
        $ext_conf_no = $_POST['ext_conf_no'];
     
        foreach ($pur_lot_no as $key => $lot_no) 
        {
          $PurArrFinal[$key]['lot_no']=$lot_no;
          $PurArrFinal[$key]['lot_bales']=$pur_lot_bales[$key];
          $PurArrFinal[$key]['invoice_no']=$pur_invoice_no[$key];
          $PurArrFinal[$key]['conf_no']=$pur_conf_no[$key];
          $PurArrFinal[$key]['purchase_report_id']=$purchase_report_id[$key];
          $PurArrFinal[$key]['used_firm_bales']=$used_firm_bales[$key];
          $PurArrFinal[$key]['total_dispatch_bales']=$total_dispatch_bales[$key];
          $PurArrFinal[$key]['ext_conf_no']=$ext_conf_no[$key];

        }
    }
    
    $PurArrFinal=json_encode($PurArrFinal);


  

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

   
		if(!isset($errorMsg)){
			$sql = "INSERT INTO `comparison_report`(`sales_conf_no`,`sales_bales`,`sales_total_bales`,`sales_data`,`delivery_at`,`invoice_raise`,`purchase_data`, `username`, `created_at`, `updated_at`) VALUES ('".$sales_conf_no."','".$sales_bales."','".$sales_total_bales."','".$saleArrFinal."','".$delivery_at."','".$invoice_raise."','".$PurArrFinal."','".$username."','".$timestamp."', '".$timestamp."')";

			$result = mysqli_query($conn, $sql);

			if($result)
			{

				$successMsg = 'New record added successfully';
				header('Location: index.php');

			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>
