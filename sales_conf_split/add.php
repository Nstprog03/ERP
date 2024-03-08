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
  if (isset($_POST['submit'])) {
     
      $data = $_POST['conf_no'];
      $conf_no =explode('/',$data)[1];
      $sale_conf_id = explode('/',$data)[0];

     

      $financial_year_id = $_POST['financial_year_id'];
      $conf_split_no = $_POST['conf_split_no'];
      $conf_type = $_POST['conf_type'];
      if (isset($_POST['split_party_name'])) {
        $split_party_name = $_POST['split_party_name'];
      }
      $conf_split_date = DateTime::createFromFormat('d/m/Y', $_POST['conf_split_date']);
      $conf_split_date=$conf_split_date->format('Y-m-d');
      
      $firm = $_POST['firm'];
      $length = $_POST['length'];
      $strength = $_POST['strength'];    
      $mic = $_POST['mic'];
      $rd = $_POST['rd'];
      $trash = $_POST['trash'];
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

      $no_of_bales = $_POST['no_of_bales'];
      $avl_bales = $_POST['avl_bales'];
   
      $lot_no = json_encode($_POST['lot_no']);
      $press_no = $_POST['press_no'];

      $price = $_POST['price'];
     
      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];

      $external_party = $_POST['external_party'];
      $firm = $_POST['firm'];

      $product = $_POST['product'];


      $broker = $_POST['broker'];
      $trans_ins = $_POST['trans_ins'];

      $length = $_POST['length'];
      $strength = $_POST['strength'];
      $mic = $_POST['mic'];
      $rd = $_POST['rd'];
      $trash = $_POST['trash'];


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
      $lot_no = json_encode($_POST['lot_no']);
      $lot_bales = json_encode($_POST['lot_bales']);
      $press_no = $_POST['press_no'];
      $variety = $_POST['variety'];
      $sub_variety = $_POST['sub_variety'];
      $price = $_POST['price'];

     

      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];

      $shipping_ext_party_id = $_POST['shipping_ext_party_id'];
      $moi = $_POST['moi'];
      $credit_days = $_POST['credit_days'];

      $station = $_POST['station'];
      $dispatch_date = $_POST['dispatch_date'];
      $prod_quality = $_POST['prod_quality'];

       date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
      $username=$_SESSION['username'];

      

     
    
     
  
  if(!isset($errorMsg)){
	              $sql = "insert into sales_conf_split(conf_no,conf_split_no,conf_type,split_party_name,conf_split_date,firm,broker,trans_ins,length,strength,mic,rd,trash,sgst,cgst,igst,no_of_bales,no_lot,lot_no,press_no,variety,sub_variety,price,bill_inst,spl_rmrk,tax_type,avl_bales,external_party,product,lot_bales,shipping_ext_party_id,moi,credit_days,station,dispatch_date,prod_quality,username,created_at,updated_at,financial_year_id,sale_conf_id) values('".$conf_no."', '".$conf_split_no."', '".$conf_type."', '".$split_party_name."', '".$conf_split_date."', '".$firm."', '".$broker."', '".$trans_ins."', '".$length."','".$strength."', '".$mic."', '".$rd."','".$trash."', '".$sgst."', '".$cgst."', '".$igst."', '".$no_of_bales."', '".$no_lot."', '".$lot_no."', '".$press_no."', '".$variety."', '".$sub_variety."', '".$price."','".$bill_inst."', '".$spl_rmrk."','".$tax_type."','".$avl_bales."','".$external_party."','".$product."','".$lot_bales."','".$shipping_ext_party_id."','".$moi."','".$credit_days."','".$station."','".$dispatch_date."','".$prod_quality."','".$username."','".$timestamp."','".$timestamp."','".$financial_year_id."','".$sale_conf_id."')";
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
