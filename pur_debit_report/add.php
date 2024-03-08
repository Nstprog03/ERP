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
 
  
  if (isset($_POST['Submit'])) 
  {
      $firm = $_POST['firm_id'];

      //$party = explode("/",$_POST['party'])[0];
      //$conf_no = explode("/",$_POST['party'])[1]; 

      $party = $_POST['party'];
      $conf_no = $_POST['pur_conf_no'];

      $pur_report_id = $_POST['pur_report_id'];

      $debiDate='';
      if($_POST['debit_date']!='')
      {
        $debiDate = str_replace('/', '-',$_POST['debit_date']);
        $debiDate = date('Y-m-d', strtotime($debiDate));
      }


      $ad_hoc_date='';
      if($_POST['ad_hoc_date']!='')
      {
        $ad_hoc_date = str_replace('/', '-',$_POST['ad_hoc_date']);
        $ad_hoc_date = date('Y-m-d', strtotime($ad_hoc_date));
      }
      
      $lot_no=$_POST['lot_no'];
      $pr_start=$_POST['pr_start'];
      $pr_end=$_POST['pr_end'];
      $broker = $_POST['broker'];
      $gross_amt = $_POST['gross_amt'];
      $cndy_rate = $_POST['cndy_rate'];
      $bill_no = $_POST['bill_no'];
      $ad_hoc = $_POST['ad_hoc'];
      $weight = $_POST['weight'];
      $original_rate = $_POST['original_rate'];

      $rd_con = $_POST['rd_con'];
      $rd_lab = $_POST['rd_lab'];
      $rd_diff = $_POST['rd_diff'];
      $rd_cndy = $_POST['rd_cndy'];
      $rd_amt = $_POST['rd_amt'];

      $len_con = $_POST['len_con'];
      $len_lab = $_POST['len_lab'];
      $len_diff = $_POST['len_diff'];
      $len_cndy = $_POST['len_cndy'];
      $len_amt = $_POST['len_amt'];


      $mic_con = $_POST['mic_con'];
      $mic_lab = $_POST['mic_lab'];
      $mic_diff = $_POST['mic_diff'];
      $mic_cndy = $_POST['mic_cndy'];
      $mic_amt = $_POST['mic_amt'];

      $trs_con = $_POST['trs_con'];
      $trs_lab = $_POST['trs_lab'];
      $trs_diff = $_POST['trs_diff'];
      $trs_amt = $_POST['trs_amt'];

      $mois_con = $_POST['mois_con'];
      $mois_lab = $_POST['mois_lab'];
      $mois_diff = $_POST['mois_diff'];
      $mois_amt = $_POST['mois_amt'];


      $smp_kg = $_POST['smp_kg'];
      $smp_amt = $_POST['smp_amt'];
      $tare_kg = $_POST['tare_kg'];
      $tare_amt = $_POST['tare_amt'];



      $brok_option = $_POST['brokerage_option'];
      $brok_bales = $_POST['brok_bales'];
      $brok_per_bales = $_POST['brok_per_bales'];
      $brok_reason=$_POST['brok_reason'];
      $brok_amt = $_POST['brok_amt'];

      if($brok_option=='manual')
      {
          
          $brok_per_bales = '';
      }
      else if($brok_option=='dynamic')
      {
        $brok_reason='';
      }

      
      

      $wght_seller_slip = $_POST['wght_seller_slip'];
      $wght_our_slip = $_POST['wght_our_slip'];
      $wght_diff = $_POST['wght_diff'];
      $wght_allow = $_POST['wght_allow'];
      $wght_shortage = $_POST['wght_shortage'];
      $wght_shortage_amt = $_POST['wght_shortage_amt'];

      $repress_bales= $_POST['repress_no_of_bales'];
      $repress_per_bales= $_POST['repress_per_bales'];
      $repress_total= $_POST['repress_total'];

     $other_check = '';
      $other_reason ='';
      $other_amount = '';
      if (isset($_POST['other_check'])) {
          $other_check= $_POST['other_check'];
          $other_reason= $_POST['other_reason'];
          $other_amount= $_POST['other_amount'];        
      }

      $int_option = $_POST['interst_option'];
      $int_days = $_POST['int_days'];
      $int_rate = $_POST['int_rate'];
      $int_amount = $_POST['int_amount'];


      $final_int = $_POST['final_int'];



      $rate_diff_candy = $_POST['rate_diff_candy'];
      $rate_diff_amount = $_POST['rate_diff_amt'];


      
      $final_bal_pay = $_POST['final_bal_pay'];
      $final_deb_amt = $_POST['final_deb_amt'];

      $is_paid=0;
      if(isset($_POST['is_paid']))
      {
        $is_paid=1;
      }



      $tax = $_POST['tax'];
      $tax_amount = $_POST['tax_amount'];
      $debit_with_tax= $_POST['final_debit_with_tax'];

      $tds_amount= $_POST['tds_amount'];


      $financial_year=$_SESSION['pur_financial_year_id'];
      
      $invoice_no= $_POST['invoice_no'];


    include('../global_function.php'); 
    $data=getFileStoragePath("pur_debit_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path


      $imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {
      
      $img_title = $_POST['img_title'][$key];
      $img = $_FILES['docimg']['name'][$key];
      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
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

    
      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");

  
        $sql = "INSERT INTO debit_report(firm, party, lot_no, pr_start, pr_end, broker, gross_amt, candy_rate, bill_no, ad_hoc, weight, original_rate, rd_con, rd_lab, rd_diff, rd_cndy, rd_amt, len_con, len_lab, len_diff, len_cndy, len_amt, mic_con, mic_lab, mic_diff, mic_cndy, mic_amt, trs_lab, trs_con, trs_diff, trs_amt, mois_lab, mois_con, mois_diff, mois_amt, sample_kg, sample_amt, tare_kg, tare_amt, brok_bales, brok_per_bales, brok_amt, seller_slip, our_slip, slip_diff, allowable, shortage, shortage_amt, interest, balance_pay, debit_amount,financial_year,repress_bales,repress_per_bales,repress_total,other_check,other_reason,other_amount,int_option,int_days,int_rate,rate_diff_candy,rate_diff_amount,tax,tax_amount,final_debit_amount,invoice_no,brok_reason,brok_option,docimg,img_title,conf_no,username,created_at,updated_at,pur_report_id,debit_date,ad_hoc_date,tds_amount,int_amount,is_paid) values (
        	'".$firm."','".$party."','".$lot_no."','".$pr_start."','".$pr_end."','".$broker."','".$gross_amt."','".$cndy_rate."','".$bill_no."','".$ad_hoc."','".$weight."','".$original_rate."','".$rd_con."','".$rd_lab."','".$rd_diff."','".$rd_cndy."','".$rd_amt."','".$len_con."','".$len_lab."','".$len_diff."','".$len_cndy."','".$len_amt."','".$mic_con."','".$mic_lab."','".$mic_diff."','".$mic_cndy."','".$mic_amt."','".$trs_lab."','".$trs_con."','".$trs_diff."','".$trs_amt."','".$mois_lab."','".$mois_con."','".$mois_diff."','".$mois_amt."','".$smp_kg."','".$smp_amt."','".$tare_kg."','".$tare_amt."','".$brok_bales."','".$brok_per_bales."','".$brok_amt."','".$wght_seller_slip."','".$wght_our_slip."','".$wght_diff."','".$wght_allow."','".$wght_shortage."','".$wght_shortage_amt."','".$final_int."','".$final_bal_pay."','".$final_deb_amt."','".$financial_year."','".$repress_bales."','".$repress_per_bales."','".$repress_total."','".$other_check."','".$other_reason."','".$other_amount."','".$int_option."','".$int_days."','".$int_rate."','".$rate_diff_candy."','".$rate_diff_amount."','".$tax."','".$tax_amount."','".$debit_with_tax."','".$invoice_no."','".$brok_reason."','".$brok_option."','".$imgStore."','".$imgTitle."','".$conf_no."', '".$username."', '".$timestamp."', '".$timestamp."', '".$pur_report_id."', '".$debiDate."', '".$ad_hoc_date."', '".$tds_amount."', '".$int_amount."','".$is_paid."')";

        $result = mysqli_query($conn, $sql);
        if($result){

         
          $successMsg = 'New record added successfully';
          header('Location: index.php');
        }else{
          $errorMsg = 'Error '.mysqli_error($conn);
                  echo $errorMsg;
        }
      
    
  }

    
?>
