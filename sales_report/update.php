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

 $dir = "/file_storage/"; // file storage in root folder of site
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if(isset($_POST['Submit'])){


        include('../global_function.php'); 
        $data=getFileStoragePath("sales_report",$_SESSION['sales_financial_year_id']);  //function from global_function file
        $root_path=$data[0]; // file move path
        $store_path=$data[1]; // db store path


        $id= $_POST['id'];  
        $party_data= explode('/', $_POST['party_data']);
        $party_name = $party_data[0];
        $conf_no = $party_data[1];

        $firm= $_POST['firm'];
       $delivery_city= $_POST['delivery_city'];
       
        $truck= $_POST['truck'];

        if($_POST['invoice_date']!='')
        {
          $date = str_replace('/', '-', $_POST['invoice_date']);
          $invoice_date = date('Y-m-d', strtotime($date));
        }
        $invice_no= $_POST['invice_no'];
        $avl_bales= $_POST['avl_bales'];
        $noOFBales= $_POST['noOFBales'];
        $net_weight= $_POST['net_weight'];
        $candy_rate= $_POST['candy_rate'];
        $grs_amt= $_POST['grs_amt'];
        $txn= $_POST['txn'];
        $txn_amt= $_POST['txn_amt'];
        $Other= $_POST['Other'];
        $other_amt_tcs= $_POST['other_amt_tcs'];
        $total_value= $_POST['total_value'];
        $start_pr= $_POST['start_pr'];
        $end_pr= $_POST['end_pr']; 
        $lot_no = json_encode($_POST['lot_no']);
        $lot_bales = json_encode($_POST['lot_bales']); 
        if ($_POST['parakh_date'] != '') {
          $parakh_date = DateTime::createFromFormat('d/m/Y', $_POST['parakh_date']);
          $parakh_date=$parakh_date->format('Y-m-d');
          
        }

        $shipping_ext_party_id=$_POST['shipping_ext_party_id'];
        $variety=$_POST['variety'];
        $sub_variety=$_POST['sub_variety'];
        $length=$_POST['length'];
        $strength=$_POST['strength'];
        $mic=$_POST['mic'];
        $rd=$_POST['rd'];
        $trash=$_POST['trash'];
        $moi=$_POST['moi'];
        $credit_days=$_POST['credit_days'];


         $imgArr=array();
    $filecount = count($_FILES['doc_file']['tmp_name']);  
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['doc_file']['name'][$key];

      $imgTmp = $_FILES['doc_file']['tmp_name'][$key];
      $imgSize = $_FILES['doc_file']['size'][$key];

  
      if(!empty($img)){
        
        $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

        $img = time().'_'.rand(1000,9999).'.'.$imgExt;
        // array_push($imgArr,$img);
        $imgArr[$key] = $img;
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

      }else{
        $imgArr[$key] = '';
      }
    }
    
    $finalimg = array();
    if(count($imgArr) > 0){
      foreach($imgArr as $k => $v){
        if($v == "" && isset($_POST['oldfile'][$k])){
          $finalimg[] = $_POST['oldfile'][$k];
        }else{
          if($v!='' && $v!=null)
          {
            $finalimg[] = $store_path.$v;
          }
        }
      }
    }


    $img_title = $_POST['img_title'];
    $imgTitle = implode(',', $img_title);
    $imgStore = implode(',', $finalimg);


    $sql="select * from sales_report where id='".$id."'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result)>0)
    {
      $row=mysqli_fetch_assoc($result);
      $OldDBImg = explode(',', $row['doc_file']); 
      $result1=array_diff($OldDBImg,$finalimg);
      foreach ($result1 as  $item) {
        if($item!='')
        {
          $item=trim($item);             
          unlink($unlink_path.$item); 
        }
      }

    }

    
   
        
         //......................Edit sales_rcvble Start........................
       

      $sales_rcvble = "select * from sales_rcvble where conf_no = '".$conf_no."' AND pur_invoice_no = '".$invice_no."'";

      $result_sales_rcvble = mysqli_query($conn, $sales_rcvble);      
      if(mysqli_num_rows($result_sales_rcvble)){

      

        while($row_sales_rcvble = mysqli_fetch_assoc($result_sales_rcvble)){
          $sales_rcvble_ids = $row_sales_rcvble['id'];
          
              $ReportSQLEdit="update sales_rcvble set
                pur_party='".$party_name."',
                pur_invoice_no='".$invice_no."',
                total_value= '".$total_value."',
                gross_amt= '".$grs_amt."',
                tax_amt= '".$txn_amt."',
                delivery_city= '".$delivery_city."',
                other_amt_tcs= '".$other_amt_tcs."'
                where 
                id='".$sales_rcvble_ids."'
              ";

            $reportSQL = mysqli_query($conn, $ReportSQLEdit);
            if($reportSQL){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }

        }
      }
      
      //......................Edit sales_rcvble END..........................
    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    $user= $_SESSION["username"];
  
    if(!isset($errorMsg)){
          $sql = "update sales_report set 
                    conf_no ='".$conf_no."',
                    party_name ='".$party_name."',
                    firm ='".$firm."',
                    delivery_city = '".$delivery_city."',                    
                    invoice_date = '".$invoice_date."',
                    invice_no = '".$invice_no."',                    
                    avl_bales = '".$avl_bales."',
                    noOFBales = '".$noOFBales."',
                    net_weight = '".$net_weight."',                    
                    candy_rate = '".$candy_rate."',
                    grs_amt = '".$grs_amt."',
                    txn = '".$txn."',                    
                    txn_amt = '".$txn_amt."',
                    Other = '".$Other."',

                    start_pr = '".$start_pr."',
                    end_pr = '".$end_pr."',

                    lot_no = '".$lot_no."',
                    lot_bales = '".$lot_bales."',

                    other_amt_tcs = '".$other_amt_tcs."',                    
                    total_value = '".$total_value."',
                    truck = '".$truck."',

                    shipping_ext_party_id = '".$shipping_ext_party_id."',
                    variety = '".$variety."',
                    sub_variety = '".$sub_variety."',
                    length = '".$length."',
                    strength = '".$strength."',
                    mic = '".$mic."',
                    rd = '".$rd."',
                    trash = '".$trash."',
                    moi = '".$moi."',
                    username = '".$user."',
                    updated_at = '".$timestamp."',
                    parakh_date= '".$parakh_date."',
                    credit_days = '".$credit_days."',
                    doc_file= '".$imgStore."',
                    img_title= '".$imgTitle."'


              where id=".$id;

              $result = mysqli_query($conn, $sql);
              if($result){
                $successMsg = 'New record updated successfully';
               
              }else{
                $errorMsg = 'Error '.mysqli_error($conn);
              }

              // sales_rcvble start
                $sql_sales_rcvble = "select * from sales_rcvble where sale_report_id ='".$id."' AND financial_year_id='".$_SESSION['sales_financial_year_id']."'";
                  $result_sql_sales_rcvble = mysqli_query($conn, $sql_sales_rcvble);
                  
                 
                  if(mysqli_num_rows($result_sql_sales_rcvble))
                  {
                    while($row_sql_sales_rcvble = mysqli_fetch_assoc($result_sql_sales_rcvble)){

                      $sales_rcvble_ids = $row_sql_sales_rcvble['id'];

                      

                      if ($total_value == '') {
                        $total_value = 0;
                      }

                      $total_value=round($total_value);

                      $credit_amt = $row_sql_sales_rcvble['credit_amt'];
                      if ($credit_amt == '') {
                        $credit_amt = 0;
                      }else{
                        $credit_amt = $row_sql_sales_rcvble['credit_amt'];
                      }

                      $net_amt= number_format(($total_value+$credit_amt),2,'.', '');
                      
                     

                      
                      //adhoc_data
                      if ($row_sql_sales_rcvble['adhoc_data']=='') {
                          $adhoc_data=0;
                      }{
                        $adhoc_data = json_decode($row_sql_sales_rcvble['adhoc_data']);
                      }
                      $adhoc_amountCount = 0;
    
                      foreach ($adhoc_data as $key => $value) {
                        $adhoc_amountCount += $value->adhoc_amount;
                      }


                      //Debit Amount
                      if ($row_sql_sales_rcvble['debit_data']=='') {
                          $debit_data=0;
                      }{
                        $debit_data = json_decode($row_sql_sales_rcvble['debit_data']);
                      }
                      $debit_dataCount = 0;
    
                      foreach ($debit_data as $key => $value) {
                        $debit_dataCount += $value->debit_amount;
                      }


                      //GST Amount
                      if ($row_sql_sales_rcvble['gst_data']=='') {
                          $gst_data=0;
                      }{
                        $gst_data = json_decode($row_sql_sales_rcvble['gst_data']);
                      }
                      $gst_dataCount = 0;
    
                      foreach ($gst_data as $key => $value) {
                        $gst_dataCount += $value->gst_amount;
                      }

                      //TCS Amount
                      if ($row_sql_sales_rcvble['tcs_data']=='') {
                          $tcs_data=0;
                      }{
                        $tcs_data = json_decode($row_sql_sales_rcvble['tcs_data']);
                      }
                      $tcs_dataCount = 0;
    
                      foreach ($tcs_data as $key => $value) {
                        $tcs_dataCount += $value->tcs_amount;
                      }


                      //TDS Amount
                      if ($row_sql_sales_rcvble['tds_data']=='') {
                          $tds_data=0;
                      }{
                        $tds_data = json_decode($row_sql_sales_rcvble['tds_data']);
                      }
                      $tds_dataCount = 0;
    
                      foreach ($tds_data as $key => $value) {
                        $tds_dataCount += $value->tds_amount;
                      }

                      //Other Amount
                      if ($row_sql_sales_rcvble['other_data']=='') {
                          $other_data=0;
                      }{
                        $other_data = json_decode($row_sql_sales_rcvble['other_data']);
                      }
                      $other_dataCount = 0;
    
                      foreach ($other_data as $key => $value) {
                        $other_dataCount += $value->other_amount;
                      }


                      //dynamic bill2bill section
                      $bill2billDynamicAmount=0;
                      if(isset($row_sql_sales_rcvble['bill2bill_dynamic_data']) && $row_sql_sales_rcvble['bill2bill_dynamic_data']!='')
                      {
                        $b2bArr = json_decode($row_sql_sales_rcvble['bill2bill_dynamic_data'],true);                      
                        foreach ($b2bArr as $key => $value) {
                          if($value['b2b_amount']!='')
                          {
                            $bill2billDynamicAmount +=(float)$value['b2b_amount'];
                          }
                          
                        }
                      }


                      $total_received = number_format(($adhoc_amountCount+$debit_dataCount+$gst_dataCount+$tcs_dataCount+$tds_dataCount+$other_dataCount+$bill2billDynamicAmount),2,'.', '');

                      $OSAmount = number_format(($net_amt-$total_received),2,'.','');

                      date_default_timezone_set('Asia/Kolkata');
                      $curDate=date('Y-m-d');
                      if ($credit_days != '' ) {
                         $Final_credit_day=$credit_days-1;

                        $due_date1 = date('Y-m-d', strtotime($parakh_date. " + $Final_credit_day day"));


                        $due_date=date("Y-m-d", strtotime($due_date1));

                     
                      }

                    }
                   
                    $SQLEdit ="update sales_rcvble set
                      total_value = '".$total_value."',
                      credit_days = '".$credit_days."',
                      credit_amt = '".$credit_amt."',
                      net_amt = '".$net_amt."',
                      total_received='".$total_received."',
                      OSAmount='".$OSAmount."',
                      conf_no = '".$conf_no."',
                      due_date = '".$due_date."',
                      parakh_date='".$parakh_date."'
                      where 
                      id='".$sales_rcvble_ids."'
                    ";

                    $sales_rcvble_result = mysqli_query($conn, $SQLEdit);
                    if($sales_rcvble_result){

                      $successMsg = 'New record updated successfully';
                       $page=1;
                    if(isset($_POST['page_no']))
                    {
                      $page=$_POST['page_no'];
                    }
                    header("Location: index.php?page=$page");
                    
                    }else{
                      $errorMsg = 'Error '.mysqli_error($conn);
                    }
                  }else{

                    $page=1;
                    if(isset($_POST['page_no']))
                    {
                      $page=$_POST['page_no'];
                    }
                    header("Location: index.php?page=$page");

                  }

                  

                // sales_rcvble END

      
    }

    }



 ?>