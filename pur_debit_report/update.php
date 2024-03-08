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

  $dir = "/file_storage/"; // file storage in root folder of site
  $unlink_path=$_SERVER['DOCUMENT_ROOT'].$dir;

  if (isset($_POST['Submit'])) {

  	$id=$_POST['id'];

    $sql = "SELECT d.*,p.party_name,p.id FROM debit_report d, party p where d.firm=p.id AND d.id='".$id."'";

    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row = mysqli_fetch_assoc($result);
    }

     $firm = $_POST['firm_id'];

     /* $party = explode("/",$_POST['party'])[0];
      $conf_no = explode("/",$_POST['party'])[1];*/

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
      if (isset($_POST['other_check'])) {
        $other_check= $_POST['other_check'];
      }
      
      $other_reason= $_POST['other_reason'];
      $other_amount= $_POST['other_amount'];

      $int_option = $_POST['interst_option'];
      $int_days = $_POST['int_days'];
      $int_rate = $_POST['int_rate'];

      $int_amount = $_POST['int_amount'];

      $final_int = $_POST['final_int'];

      $rate_diff_candy = $_POST['rate_diff_candy'];
      $rate_diff_amount = $_POST['rate_diff_amt'];


      $is_paid=0;
      if(isset($_POST['is_paid']))
      {
        $is_paid=1;
      }


     
      $final_bal_pay = $_POST['final_bal_pay'];
      $final_deb_amt = $_POST['final_deb_amt'];

      $tax = $_POST['tax'];
      $tax_amount = $_POST['tax_amount'];
      $debit_with_tax= $_POST['final_debit_with_tax'];

      $tds_amount = $_POST['tds_amount'];


      $invoice_no= $_POST['invoice_no'];

    include('../global_function.php'); 
    $data=getFileStoragePath("pur_debit_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path
      

    $imgArr=array();
    $filecount = count($_FILES['docimg']['tmp_name']);  
    foreach ($_FILES['docimg']['tmp_name'] as $key =>  $imges) {

      $img = $_FILES['docimg']['name'][$key];

      $imgTmp = $_FILES['docimg']['tmp_name'][$key];
      $imgSize = $_FILES['docimg']['size'][$key];

  
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
    
    $OldDBImg = explode(',', $row['docimg']); 
    $result1=array_diff($OldDBImg,$finalimg);
        foreach ($result1 as  $item) 
        {
          if($item!='')
          {
            $item=trim($item);             
            unlink($unlink_path.$item); 
          } 
        }


      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");

    $sql=" update debit_report set 
        party='".$party."',
        firm='".$firm."',
        lot_no='".$lot_no."',
        pr_start='".$pr_start."',
        pr_end='".$pr_end."',
        broker='".$broker."',
        gross_amt='".$gross_amt."',
        candy_rate='".$cndy_rate."',
        ad_hoc='".$ad_hoc."',
        weight='".$weight."',
        original_rate='".$original_rate."',
        rd_con='".$rd_con."',
        rd_lab='".$rd_lab."',
        rd_diff='".$rd_diff."',
        rd_cndy='".$rd_cndy."',
        rd_amt='".$rd_amt."',
        len_con='".$len_con."',
        len_lab='".$len_lab."',
        len_diff='".$len_diff."',
        len_cndy='".$len_cndy."',
        len_amt='".$len_amt."',
        mic_con='".$mic_con."',
        mic_lab='".$mic_lab."',
        mic_diff='".$mic_diff."',
        mic_cndy='".$mic_cndy."',
        mic_amt='".$mic_amt."',
        trs_lab='".$trs_lab."',
        trs_con='".$trs_con."',
        trs_diff='".$trs_diff."',
        trs_amt='".$trs_amt."',
        mois_lab='".$mois_lab."',
        mois_con='".$mois_con."',
        mois_diff='".$mois_diff."',
        mois_amt='".$mois_amt."',
        sample_kg='".$smp_kg."',
        sample_amt='".$smp_amt."',
        tare_kg='".$tare_kg."',
        tare_amt='".$tare_amt."',
        brok_bales='".$brok_bales."',
        brok_per_bales='".$brok_per_bales."',
        brok_amt='".$brok_amt."',
        seller_slip='".$wght_seller_slip."',
        our_slip='".$wght_our_slip."',
        slip_diff='".$wght_diff."',
        allowable='".$wght_allow."',
        shortage='".$wght_shortage."',
        shortage_amt='".$wght_shortage_amt."',
        interest='".$final_int."',
        balance_pay='".$final_bal_pay."',
        debit_amount='".$final_deb_amt."',

        is_paid='".$is_paid."',
        
        repress_bales='".$repress_bales."',
        repress_per_bales='".$repress_per_bales."',
        repress_total='".$repress_total."',

        other_check='".$other_check."',
        other_reason='".$other_reason."',
        other_amount='".$other_amount."',

        int_option='".$int_option."',
        int_amount='".$int_amount."',
        int_days='".$int_days."',
        int_rate='".$int_rate."',

        rate_diff_candy='".$rate_diff_candy."',
        rate_diff_amount='".$rate_diff_amount."',

        tax='".$tax."',
        tax_amount='".$tax_amount."',
        final_debit_amount='".$debit_with_tax."',
        invoice_no='".$invoice_no."',

        debit_date='".$debiDate."',
        ad_hoc_date='".$ad_hoc_date."',

        brok_reason='".$brok_reason."',
        brok_option='".$brok_option."',

        tds_amount='".$tds_amount."',

        docimg='".$imgStore."',
        img_title='".$imgTitle."',
        conf_no='".$conf_no."',
        pur_report_id='".$pur_report_id."',
        username='".$username."',

        updated_at='".$timestamp."'
        
        where id='".$id."'";


            // pur_pay start
              
                 $sql_pur_pay = "select * from pur_pay where invoice_no = '".$invoice_no."' AND pur_report_id='".$pur_report_id."' AND financial_year_id='".$_SESSION['pur_financial_year_id']."'";
    
                $result_sql_pur_pay = mysqli_query($conn, $sql_pur_pay);      
                if(mysqli_num_rows($result_sql_pur_pay)){
                  while($row_sql_pur_pay = mysqli_fetch_assoc($result_sql_pur_pay)){
                    
                    $pur_pay_ids = $row_sql_pur_pay['id'];
                   
                    $invoice_amt = $row_sql_pur_pay['invoice_amt'];
                    if ($invoice_amt == '') 
                    {
                      $invoice_amt = 0;
                    }

                    $final_debit_amount =  $debit_with_tax;

                    if ($final_debit_amount == '') {
                      $final_debit_amount = 0;
                    }

                     if ($ad_hoc == '') {
                      $ad_hoc = 0;
                    }

                    if ($tds_amount == '') {
                      $tds_amount = 0;
                    }


                    $net_amt = number_format(($invoice_amt - $final_debit_amount-$ad_hoc-$tds_amount),2,'.','');
                    $net_amt=round($net_amt);

                    $dynamic_field1 = json_decode($row_sql_pur_pay['dynamic_field']);
                    $dynamic_AmtCount = 0;
                    foreach ($dynamic_field1 as $key => $value) {
                      $dynamic_AmtCount += (float)$value->amt;
                    }

                    $bill2billDynamicAmount=0;
                    if(isset($row_sql_pur_pay['bill2bill_dynamic_data']) && $row_sql_pur_pay['bill2bill_dynamic_data']!='')
                    {
                      $b2bArr = json_decode($row_sql_pur_pay['bill2bill_dynamic_data'],true);                      
                      foreach ($b2bArr as $key => $value) {
                        if($value['b2b_amount']!='')
                        {
                          $bill2billDynamicAmount += (float)$value['b2b_amount'];
                        }
                        
                      }

                    }

              

                    $pay_amt = number_format(($net_amt - $dynamic_AmtCount - $bill2billDynamicAmount),2,'.','') ;
                   
 
                    $Pur_Pay_SQLEdit="update pur_pay set
                      invoice_amt = '".$invoice_amt."',
                      ad_hoc_date = '".$ad_hoc_date."',
                      debit_report_date = '".$debiDate."',
                      ad_hoc = '".$ad_hoc."',
                      final_debit_amount = '".$final_debit_amount."',
                      net_amt = '".$net_amt."',
                      tds_amount = '".$tds_amount."',
                      pay_amt = '".$pay_amt."'
                      where 
                      id='".$pur_pay_ids."'
                    ";

                $pur_pay_SQL = mysqli_query($conn, $Pur_Pay_SQLEdit);
                if($pur_pay_SQL){
                  $successMsg = 'New record Updated successfully';
                  
                }else{
                  $errorMsg = 'Error '.mysqli_error($conn);
                    echo $errorMsg;
                }
                  }
                }

            // pur_pay End 



        $check=$conn->query($sql);
		
			
			if ($check) 
			{
			   
			 $successMsg = 'record Updated successfully';

        $page=1;
        if(isset($_POST['page']))
        {
          $page=$_POST['page'];
        }
        header("Location: index.php?page=$page");



			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}

}


			
	
		

?>
