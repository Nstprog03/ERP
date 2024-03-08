<?php
session_start();
require_once('../db.php');
if(isset($_POST['updateSalesData']))
{

    $id=$_POST['record_id'];
    $sales_conf_no=$_POST['sales_conf_no'];

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

 
    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

 
    $sql = "UPDATE `comparison_report` SET 
    `sales_conf_no`='".$sales_conf_no."',
    `sales_data`='".$saleArrFinal."',
    `delivery_at`='".$delivery_at."',
    `invoice_raise`='".$invoice_raise."',
    `username`='".$username."',
    `updated_at`='".$timestamp."'
      WHERE id=".$id;
    $result = mysqli_query($conn, $sql);
  
    if($result)
    {
        $response['success']=true;
    }
    else
    {
        $response['success']=false;
    }


    echo json_encode($response);
    exit;
}

?>