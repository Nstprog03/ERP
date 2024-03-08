<?php
session_start();
require_once('../db.php');
if(isset($_POST['updatePurchaseData']))
{

    $id=$_POST['record_id'];
    

  
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

 
    $sql = "UPDATE `comparison_report` SET 
    `purchase_data`='".$PurArrFinal."',
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