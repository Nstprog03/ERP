<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}

  if (isset($_POST['Submit'])) {
     

     
      $sales_conf = $_POST['conf_form'].'-'.$_POST['conf_index']; 
      $conf_type = $_POST['conf_type'];

      //date convert dd/mm/yyyy to yyyy-mm-dd
      if($_POST['sales_date']!='')
      {
        $sales_date = DateTime::createFromFormat('d/m/Y', $_POST['sales_date']);
        $sales_date=$sales_date->format('Y-m-d');
      }

      if($_POST['dispatch_date']!='')
      {
        $dispatch_date = DateTime::createFromFormat('d/m/Y', $_POST['dispatch_date']);
        $dispatch_date=$dispatch_date->format('Y-m-d');
      }
      

      $external_party = $_POST['external_party'];
      $shipping= $_POST['shipping_ext_party_id'];


      $financial_year_id = $_POST['financial_year_id'];

      $firm = $_POST['firm'];

      $product = $_POST['product'];


      $broker = $_POST['broker'];
      $trans_ins = $_POST['trans_ins'];

      $length = $_POST['length'];
      $strength = $_POST['strength'];
      $mic = $_POST['mic'];
      $rd = $_POST['rd'];
      $trash = $_POST['trash'];
      $moi = $_POST['moi'];


      $tax_type=$_POST['taxtype'];

      if($tax_type=='sgst')
      {
        $sgst = $_POST['sgst'];
        $cgst = $_POST['cgst'];
        $igst='';
      }
      else if($tax_type=='igst')
      {
        $igst = $_POST['igst'];
        $cgst = '';
        $sgst = '';
      }

      $cont_quantity = $_POST['cont_quantity'];
      $no_lot = $_POST['no_lot'];
      $lot_no = json_encode($_POST['lot_no']);
      $lot_bales = json_encode($_POST['lot_bales']);

      $press_no = $_POST['press_no'];

      $candy_rate = $_POST['candy_rate'];
     
      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];

      $station = $_POST['station'];
      $credit_days = $_POST['credit_days'];


      $prod_quality = $_POST['prod_quality'];
      $prod_variety = $_POST['variety'];
      $prod_sub_variety = $_POST['sub_variety'];




      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
     
    
     /* $lot_no_data = array();

      foreach($lot_no as $lot_no_item)
          $lot_no_data[] = "(" . addslashes($lot_no_item) . ":0)";

      $lot_no = implode("," , $lot_no_data);*/

  
  if(!isset($errorMsg)){
	               $sql = "insert into seller_conf(product,sales_conf,conf_type,sales_date,external_party,firm,broker,trans_ins,length,strength,mic,rd,trash,sgst,cgst,igst,cont_quantity,no_lot,lot_no,press_no,candy_rate,bill_inst,spl_rmrk,tax_type,dispatch_date,station,moi,shipping_ext_party_id,credit_days,lot_bales,financial_year_id,prod_quality,variety,sub_variety,username,created_at,updated_at) values('".$product."', '".$sales_conf."', '".$conf_type."', '".$sales_date."', '".$external_party."', '".$firm."', '".$broker."', '".$trans_ins."', '".$length."','".$strength."', '".$mic."', '".$rd."','".$trash."', '".$sgst."', '".$cgst."', '".$igst."', '".$cont_quantity."', '".$no_lot."', '".$lot_no."', '".$press_no."','".$candy_rate."', '".$bill_inst."', '".$spl_rmrk."','".$tax_type."','".$dispatch_date."','".$station."','".$moi."','".$shipping."','".$credit_days."','".$lot_bales."','".$financial_year_id."','".$prod_quality."','".$prod_variety."','".$prod_sub_variety."','".$username."','".$timestamp."','".$timestamp."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
			}
		}
  }
?>
