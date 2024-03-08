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
    $org_rate=$_POST['candy_rate'];
  	$id=$_POST['id'];

    $party = explode("/",$_POST['party'])[0];

    $pur_conf_ids = explode("/",$_POST['party'])[1];
    $weight_post = $_POST['weight'];
    
    $conf_no=$_POST['pur_conf'];
    $avl_bales=$_POST['avl_bales'];
    $candy_rate=$_POST['candy_rate'];
    $lot_no=$_POST['lot_no'];
    $pr_no_start=$_POST['pr_no_start'];
    $pr_no_end=$_POST['pr_no_end'];
    $broker = $_POST['broker'];
    $bales	=  $_POST['bales'];
    $grs_amt_post =  $_POST['gross_amount'];
    $tax =  $_POST['tax'];
    $tax_amount =  $_POST['tax_amount'];
    $tcs =  $_POST['tcs'];
    $tcs_amount =  $_POST['tcs_amount'];
    $netpayableamt = $_POST['total_amount'];
    $other_amt_post = $_POST['other_amt'];
    $invoice_no=$_POST['invoice_no'];


    $reportDate='';
    if($_POST['report_date']!='')
    {
      $reportDate = str_replace('/', '-', $_POST['report_date']);
      $reportDate = date('Y-m-d', strtotime($reportDate));
    }



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







   

	$idvar = 0;
	$idval = $idvar++;


    $pur_report_bales=$_POST['pur_report_bales'];

    // if($pur_report_bales+$bales>$avl_bales)
    // {
    //     echo "<h2>No. Of Bales Should Not Be Greater Then Available Bales...</h2>";
    //     exit;
    // }

	/*$lotnoArr = $_POST['lotno'];
    $lotqtyArr = $_POST['lotqty'];
    $pressone_startArr = $_POST['presstart'];
    $pressone_endArr = $_POST['pressend'];*/


    //$b=$pur_report_bales+$bales;


    include('../global_function.php'); 
    $data=getFileStoragePath("purchase_report",$_SESSION['pur_financial_year_id']);  //function from global_function file
    $root_path=$data[0]; // file move path
    $store_path=$data[1]; // db store path  


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


    $sql = "select * from pur_report where id=".$id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
    {
      $row1 = mysqli_fetch_assoc($result);
    }
    
    $OldDBImg = explode(',', $row1['doc_file']); 
    $result1=array_diff($OldDBImg,$finalimg);
    foreach ($result1 as  $item) {
        if($item!='')
        {
          $item=trim($item);             
          unlink($unlink_path.$item); 
        }  
    }

      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");

   
    // Purchase Payout Database
       $pur_paySQL="update pur_pay set
                invoice_no='".$invoice_no."',
                party='".$party."',
                gross_amt='".$grs_amt_post."',
                tax_amt='".$tax_amount."',
                tcs_amt='".$tcs_amount."',
                other_amt='".$other_amt_post."'
                where pur_report_id='".$id."'";
             
            $reportSQL = mysqli_query($conn, $pur_paySQL);
            if($reportSQL){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }



       




        // ...............Pur debit report edit Start...........................
      $oldInvoiceNo = $_POST['oldInvoiceNo'];
     
     $sql_debit_report = "select * from debit_report where invoice_no = '".$oldInvoiceNo."' AND pur_report_id = '".$id."'";
    
      $result_debit_report = mysqli_query($conn, $sql_debit_report);      
      if(mysqli_num_rows($result_debit_report)){
        while($row_debit_report = mysqli_fetch_assoc($result_debit_report)){
          $debit_report_ids = $row_debit_report['id'];
          

          
          if ($candy_rate == '') {
            $candy_rate = 0;
          }
          
          if ($weight_post == '') {
            $weight_post = 0;
          }
          
          
          $grs_amt = number_format($weight_post*($candy_rate*(0.2812/100)),2,'.', '');

          

          //Gross Amnt 
          // $gross_amt = number_format($weight*($candy_rate*0.2812/100),2);
          // print_r($gross_amt);

          // START RD..............................................
            
            if ($row_debit_report['rd_con'] == '') {
              $rd_con = 0;
            }else{
              $rd_con = $row_debit_report['rd_con'];
            }
            if ($row_debit_report['rd_lab'] == '') {
              $rd_lab = 0;
            }else{
              $rd_lab = $row_debit_report['rd_lab'];
            }
            $rd_diff = number_format(($rd_con - $rd_lab),2,'.', '');
            if ($rd_diff<0) {
              $rd_diff = 0;
            }
        
            

            if ($row_debit_report['weight'] == '') {
              $weight = 0;
            }else{

              $weight = $row_debit_report['weight'];

            }

            if ($row_debit_report['rd_cndy'] == '') {
              $rd_cndy = 0;
            }else{
              $rd_cndy = $row_debit_report['rd_cndy'];
            }

            $rd_amt = number_format($grs_amt-($weight*(($candy_rate-$rd_cndy)*0.2812/100)),2,'.', '');
          // END RD..............................................

          // #Length............................................

            if ($row_debit_report['len_lab'] == '') {
              $len_lab = 0;
            }else{

             $len_lab =$row_debit_report['len_lab'];

            }
            if ($row_debit_report['len_con'] == '') {
              $len_con = 0;
            }else{
              $len_con = $row_debit_report['len_con'];
            }
            $len_diff = number_format(($len_con - $len_lab),2,'.', '');
            if($len_diff<0){
                $len_diff = 0;

            }

            if ($row_debit_report['len_cndy'] == '') {
              $len_cndy = 0;
            }else{
              $len_cndy = $row_debit_report['len_cndy'];
            }
            $len_amt = number_format($grs_amt-($weight*(($candy_rate-$len_cndy)*0.2812/100)),2,'.', '');
          // ENDLength............................................

          // #micSTART............................................

            if ($row_debit_report['mic_lab'] == '') {
              $mic_lab = 0;
            }else{

             $mic_lab =$row_debit_report['mic_lab'];

            }
            if ($row_debit_report['mic_con'] == '') {
              $mic_con = 0;
            }else{
              $mic_con = $row_debit_report['mic_con'];
            }
            $mic_diff = number_format(($mic_con - $mic_lab),2,'.', '');
            if($mic_diff<0){
                $mic_diff = 0;
            }

           


            if ($row_debit_report['mic_cndy'] == '') {
              $mic_cndy = 0;
            }else{
              $mic_cndy = $row_debit_report['mic_cndy'];
            }
            $mic_amt = number_format($grs_amt-($weight*(($candy_rate-$mic_cndy)*0.2812/100)),2,'.', '');
          // ENDmicSTART............................................

          // #trshSTART............................................

            if ($row_debit_report['trs_lab'] == '') {
              $trs_lab = 0;
            }else{

             $trs_lab =$row_debit_report['trs_lab'];

            }
            if ($row_debit_report['trs_con'] == '') {
              $trs_con = 0;
            }else{
              $trs_con = $row_debit_report['trs_con'];
            }
            $trs_diff = number_format(($trs_lab)-($trs_con),2,'.', '');
            if($trs_diff<0){
                $trs_diff = 0;
            }

           
            if ($row_debit_report['trs_diff'] == '') {
              $trs_diff = 0;
            }else{
              $trs_diff = $row_debit_report['trs_diff'];
            }
            $trs_amt = number_format(($grs_amt*$trs_diff/100),2,'.', '');
          // ENDtrsh............................................

          // #Moisture START............................................
            if ($row_debit_report['mois_diff'] == '') {
              $mois_diff = 0;
            }else{
              $mois_diff = $row_debit_report['mois_diff'];
            }
            $mois_amt = number_format(($grs_amt*$mois_diff/100),2,'.', '');
          // ENDMoisture ............................................

          // #Sample START............................................
            if ($row_debit_report['sample_kg'] == '') {
              $sample_kg = 0;
            }else{
              $sample_kg = $row_debit_report['sample_kg'];
            }
            $smp_amt = number_format($sample_kg*($candy_rate*0.2812/100),2,'.', '');
          // ENDSample ............................................

          // #Extra Tare START............................................
            if ($row_debit_report['tare_kg'] == '') {
              $tare_kg = 0;
            }else{
              $tare_kg = $row_debit_report['tare_kg'];
            }
            $tare_amt = number_format($tare_kg*($candy_rate*0.2812/100),2,'.', '');
          // END Extra Tare ............................................

          // #Brokerage START............................................
            if ($row_debit_report['brok_option'] == 'dynamic') {
                if ($row_debit_report['brok_per_bales'] == '') {
                  $brok_per_bales = 0;
                }else{
                  $brok_per_bales = $row_debit_report['brok_per_bales'];
                }
                if ($bales == '') {
                  $bales = 0;
                }
                $brok_amt = number_format(($brok_per_bales*$bales),2,'.', '');  
            }
          // ENDBrokerage ............................................

          //Weight Shoratage : 


            if ($weight_post == '') {
              $wght_seller_slip = 0;
            }
            else{
              $wght_seller_slip = $weight_post;
            }
            if ($row_debit_report['our_slip'] == '') {
              $our_slip = 0;
            }else{
              $our_slip = $row_debit_report['our_slip'];
            }

            $slip_diff = number_format(($wght_seller_slip - $our_slip),2,'.', '');
            if(isset($slip_diff) && $slip_diff < 0){
                  $slip_diff = 0;
              }

            
            if ($row_debit_report['allowable'] == '') {
              $allowable = 0;
            }else{
              $allowable = $row_debit_report['allowable'];
            }
            $shortage = number_format(($slip_diff - $allowable),2,'.', '');
          
            $shortage_amt = number_format($shortage*($candy_rate*0.2812/100),2,'.', '');
            
          // END Weight Shoratage..............................................
          
          // Re-pressing Start .........................................

            if ($row_debit_report['repress_bales'] == '') {
              $repress_bales = 0;
            }else{
              $repress_bales = $row_debit_report['repress_bales'];
            }

            if ($row_debit_report['repress_per_bales'] == '') {
              $repress_per_bales = 0;
            }else{
              $repress_per_bales = $row_debit_report['repress_per_bales'];
            }

            $repress_total = number_format(($repress_bales*$repress_per_bales),2,'.', '');
          // Re-pressing End .........................................

          // rate difference Start.................................

            if ($row_debit_report['rate_diff_candy'] == '') {
              $rate_diff_candy = 0;
            }else{
              $rate_diff_candy= $row_debit_report['rate_diff_candy'];
            }
           
            $rate_diff_amount = number_format($grs_amt-($weight*($candy_rate-$rate_diff_candy)*0.2812/100),2,'.', '');



          // rate difference End.................................

          // ins option................................................
            
            if ($row_debit_report['ad_hoc'] == '') {
              $ad_hoc = 0;
            }else{

              $ad_hoc = $row_debit_report['ad_hoc'];
            }
            
            $interest ='';
           
            if($row_debit_report['int_option'] == 'manual')
            {
              if ($row_debit_report['int_days'] == '') {
              $int_days = 0;
              }else{

                $int_days = $row_debit_report['int_days'];
              }
              if ($row_debit_report['int_rate'] == '') {
              $int_rate = 0;
              }else{

                $int_rate = $row_debit_report['int_rate'];
              }

              if ($row_debit_report['int_amount'] == '') {
              $int_amount = 0;
              }else{

                $int_amount = $row_debit_report['int_amount'];
              }

              $interest = number_format(($int_amount*$int_days*$int_rate/36000),2,'.', '');
            }else{

              $interest = number_format(($ad_hoc * 15 * 15 / 36000),2,'.', '');

            }

          // END ins option................................................

          // Final Calculations
            $other_amount = 0;
            if ($row_debit_report['other_check']=='true') {
              if ($row_debit_report['other_amount'] == '') {
                $other_amount = 0;
              }else{
                $other_amount = $row_debit_report['other_amount'];
              }
            }


            $debit_amount = number_format(($interest+$shortage_amt+$brok_amt+$tare_amt+$smp_amt+$mois_amt+$trs_amt+$len_amt+$rd_amt+$rate_diff_amount+$repress_total+$mic_amt+$other_amount),2,'.', '');

          
          // End Debit Amount ...........................................
          
          // tex_amt start...............................................
            $debit_tax_amount = 0;
            if ($row_debit_report['tax'] == '') {
              $tax = 0;
            }else{

              $tax = $row_debit_report['tax'];
            }
            $debit_tax_amount = number_format((($debit_amount*$tax)/100),2,'.', '');
            // final_debit_amount_with_tax strat........................
              
              $final_debit_amount_with_tax = number_format(($debit_amount+$debit_tax_amount),2,'.', ''); 
             
            // final_debit_amount_with_tax end....................

          // tex_amt end...............................................


              //tds amount
              $tds_amount=0;
              if($row_debit_report['tds_amount']!='')
              {
                $tds_amount=$row_debit_report['tds_amount'];
              }

          // Balance Payable start
              
              $Balance_Payable = number_format(($grs_amt-$ad_hoc-$final_debit_amount_with_tax-$tds_amount),2,'.', '');
             

            
          // Balance Payable End                    
             $ReportSQLEdit="update debit_report set
                invoice_no='".$invoice_no."',
                lot_no='".$lot_no."',
                pr_start='".$pr_no_start."',
                pr_end= '".$pr_no_end."',
                broker= '".$broker."',
                gross_amt= '".$grs_amt."',
                candy_rate= '".$candy_rate."',
                original_rate = '".$candy_rate."',
                brok_bales = '".$bales."',

                rd_diff = '".$rd_diff."',
                len_diff = '".$len_diff."',
                mic_diff = '".$mic_diff."',
                trs_diff = '".$trs_diff."',
                weight = '".$weight_post."',

                rd_amt = '".$rd_amt."',
                len_amt = '".$len_amt."',
                mic_amt = '".$mic_amt."',
                trs_amt = '".$trs_amt."',
                mois_amt = '".$mois_amt."',
                sample_amt = '".$smp_amt."',
                tare_amt = '".$tare_amt."',
                brok_amt = '".$brok_amt."',
                seller_slip = '".$wght_seller_slip."',
                slip_diff = '".$slip_diff."',
                shortage = '".$shortage."',
                shortage_amt = '".$shortage_amt."',
                interest = '".$interest."',
                repress_total = '".$repress_total."',
                debit_amount = '".$debit_amount."',
                rate_diff_amount = '".$rate_diff_amount."',
                tax_amount = '".$debit_tax_amount."',
                balance_pay = '".$Balance_Payable."',
                final_debit_amount = '".$final_debit_amount_with_tax."',
                tds_amount = '".$tds_amount."'
                where id='".$debit_report_ids."'";


                // pur_pay start
                

                $sql_pur_pay = "select * from pur_pay where invoice_no = '".$row_debit_report['invoice_no']."' AND pur_report_id='".$id."'
                AND financial_year_id='".$_SESSION['pur_financial_year_id']."'
                ";
    
                $result_sql_pur_pay = mysqli_query($conn, $sql_pur_pay);      
                if(mysqli_num_rows($result_sql_pur_pay)){
                  while($row_sql_pur_pay = mysqli_fetch_assoc($result_sql_pur_pay)){
                    
                    $pur_pay_ids = $row_sql_pur_pay['id'];
                    $invoice_amt = $netpayableamt;
                    
                    $final_debit_amount =  $final_debit_amount_with_tax;
                    

                    if ($invoice_amt == '') {
                      $invoice_amt = 0;
                    }





                    if ($final_debit_amount == '') {

                      $final_debit_amount = 0;
                    }else{

                      $final_debit_amount = $final_debit_amount_with_tax;

                    }

                    if ($ad_hoc == '') {
                      $ad_hoc = 0;
                    }

                    if ($tds_amount == '') {
                      $tds_amount = 0;
                    }

                  

                    $net_amt = number_format(($invoice_amt - $final_debit_amount- $ad_hoc-$tds_amount),2,'.','');

                    $net_amt=round($net_amt);

                    $dynamic_field1 = json_decode($row_sql_pur_pay['dynamic_field']);
                    $dynamic_AmtCount = 0;
                    foreach ($dynamic_field1 as $key => $value) {
                      $dynamic_AmtCount +=(float) $value->amt;
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
                      ad_hoc = '".$ad_hoc."',
                      final_debit_amount = '".$final_debit_amount."',
                      net_amt = '".$net_amt."',
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
             
            $reportSQL = mysqli_query($conn, $ReportSQLEdit);
            if($reportSQL){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }


            

        }
      }
      // exit();
      // ...............Pur_report edit End...........................



      //transport payout update START----------------------------


        $sql_trans_update = "select * from transport_payout where pur_report_id='".$id."'
                ";
    
          $result_trans_update = mysqli_query($conn, $sql_trans_update);      
          if(mysqli_num_rows($result_trans_update))
          {
            while($row_trans = mysqli_fetch_assoc($result_trans_update))
            {


              $tpayout_id=$row_trans['id'];


               $t_transid=$trans_id;
               $t_transveh=$trans_veh_no;
                $t_lr_date=$trans_lr_date;
                $t_lr_no=$trans_lr_no;
                $t_transAmt=$trans_amount;

                if($t_transid=='')
                {
                  $t_transid=$row_trans['trans_id'];
                }
                if($t_transveh=='')
                {
                  $t_transveh=$row_trans['trans_veh_no'];
                }
                if($t_lr_date=='')
                {
                  $t_lr_date=$row_trans['trans_lr_date'];
                }
                if($t_lr_no=='')
                {
                  $t_lr_no=$row_trans['trans_lr_no'];
                }
                if($t_transAmt=='')
                {
                  $t_transAmt=$row_trans['trans_amount'];
                }


                $t_tds_amt=($t_transAmt*$row_trans['tds_per'])/100;

                $t_total_amt=$t_transAmt-$t_tds_amt;



                  $sql_trans_update2="update transport_payout set
                       invoice_no='".$invoice_no."',
                       ext_party_id='".$party."',
                       trans_id='".$t_transid."',
                       trans_veh_no='".$t_transveh."',
                       trans_lr_date='".$t_lr_date."',
                       trans_lr_no='".$t_lr_no."',
                       trans_amount='".$t_transAmt."',
                       tds_amount='".$t_tds_amt."',
                       total_amount='".$t_total_amt."'
                        where 
                        id='".$tpayout_id."'
                      ";

                     

                  $tupdate_result = mysqli_query($conn, $sql_trans_update2);
                  if($tupdate_result){
                    $successMsg = 'New record Updated successfully';
                    
                  }else{
                    $errorMsg = 'Error '.mysqli_error($conn);
                      echo $errorMsg;
                  }
            }
          }

     //transport payout update END----------------------------



         

      


    $sql=" update pur_report set 
        party='".$party."',
        pur_conf_ids='".$pur_conf_ids."',
        conf_no='".$conf_no."',
        avl_bales='".$avl_bales."',
        cndy_rate='".$candy_rate."',
        lot_no='".$lot_no."',
        pr_no_start='".$pr_no_start."',
        pr_no_end='".$pr_no_end."',
        broker='".$broker."',
        bales='".$bales."',
        grs_amt='".$grs_amt_post."',
        txn='".$tax."',
        txn_amount='".$tax_amount."',
        tcs='".$tcs."',
        tcs_amount='".$tcs_amount."',
        other_amt='".$other_amt_post."',
        netpayableamt='".$netpayableamt."',
        report_date='".$reportDate."',
        invoice_no='".$invoice_no."',
        doc_file = '".$imgStore."',
        weight = '".$weight_post."',
        img_title = '".$imgTitle."',
        trans_pay_type= '".$trans_pay_type."',
        trans_id= '".$trans_id."',
        trans_veh_no= '".$trans_veh_no."',
        trans_lr_date= '".$trans_lr_date."',
        trans_lr_no= '".$trans_lr_no."',
        trans_amount= '".$trans_amount."',


        username = '".$username."',
        updated_at = '".$timestamp."'
        where id='".$id."'";


        $check=$conn->query($sql);
		
			
			if ($check) 
			{
			    //$last_id = $conn->insert_id;
			    //echo "New record created successfully. Last inserted ID is: " . $last_id;

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
