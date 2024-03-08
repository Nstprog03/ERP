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

    
    $enteredDate='';
    if($_POST['pur_date']!='')
    {
        $enteredDate = str_replace('/', '-',$_POST['pur_date']);
        $enteredDate = date('Y-m-d', strtotime($enteredDate));
    }



    $dates = explode("/", $_SESSION['pur_financial_year']);
    $startdate = date('Y-m-d', strtotime($dates[0]));
    $enddate = date('Y-m-d', strtotime($dates[1]));
        
    

      $pur_conf = $_POST['pur_conf'].'-'.$_POST['conf_index'];
      $conf_type = $_POST['conf_type'];
      $pur_report_date = $enteredDate;
      $pur_date = $enteredDate;
     
      $party = $_POST['party'];
      $firm = $_POST['firm_id'];
      $broker = $_POST['broker'];
      $pro_length = $_POST['pro_length'];
      $pro_mic = $_POST['pro_mic'];
      $pro_rd = $_POST['pro_rd'];
      $pro_trash = $_POST['pro_trash'];
      $pro_mois = $_POST['pro_mois'];
      $bales = $_POST['bales'];
      $candy_rate = $_POST['candy_rate'];
      $spl_rmrk = $_POST['spl_rmrk'];
      $financial_year = $_POST['financial_year'];


      $dispatch=$_POST['dispatch'];
      $transport_name=$_POST['transport_name'];
      $trans_pay_type=$_POST['trans_pay_type'];
      $no_of_vehicle=$_POST['no_of_vehicle'];


      $vehicle_nos=array();
      if($_POST['veh_nos']!='')
      {
        $vehicle_nos=$_POST['veh_nos'];
      }
      $vehicle_nos=json_encode($_POST['veh_nos']);


      $ins_cmp_name=$_POST['ins_cmp_name'];
      $ins_policy_no=$_POST['ins_policy_no'];
      $pay_term=$_POST['pay_term'];
    
      $laboratory_master=$_POST['laboratory_master'];
      $delivery_date=$_POST['delivery_date'];
      $station=$_POST['station'];
      $product_name=$_POST['product_name'];
      $term_condtion=$_POST['term_condtion'];

      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
        

      if(!isset($errorMsg)){
        $sql = "insert into pur_conf(pur_conf,conf_type,pur_date,pur_report_date,party,firm,broker,pro_length,pro_mic,pro_rd,pro_trash,pro_mois,bales,candy_rate,spl_rmrk,financial_year,dispatch,trans_name,no_of_vehicle,vehicle_no,ins_cmpny,ins_policy_no,pay_term,laboratory_master,delivery_date,station,product_name,term_condtion,username,created_at,updated_at,trans_pay_type) values ('".$pur_conf."','".$conf_type."','".$pur_date."','".$pur_report_date."','".$party."','".$firm."','".$broker."','".$pro_length."','".$pro_mic."','".$pro_rd."','".$pro_trash."','".$pro_mois."','".$bales."','".$candy_rate."','".$spl_rmrk."','".$financial_year."','".$dispatch."','".$transport_name."','".$no_of_vehicle."','".$vehicle_nos."','".$ins_cmp_name."','".$ins_policy_no."','".$pay_term."', '".$laboratory_master."', '".$delivery_date."', '".$station."', '".$product_name."', '".$term_condtion."', '".$username."', '".$timestamp."', '".$timestamp."', '".$trans_pay_type."')";
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
