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



      $id = $_POST['id'];
      $pur_conf = $_POST['pur_conf'];

      $conf_type = $_POST['conf_type'];
      $firm = $_POST['pur_firm'];
      $financial_year = $_POST['financial_year'];
      $pur_report_date = $enteredDate;
      $pur_date = $enteredDate;
      
      $party = $_POST['party'];
      $broker = $_POST['broker'];
      $pro_length = $_POST['pro_length'];
      $pro_mic = $_POST['pro_mic'];
      $pro_rd = $_POST['pro_rd'];
      $pro_trash = $_POST['pro_trash'];
      $pro_mois = $_POST['pro_mois'];
      $bales = $_POST['bales'];
      $candy_rate = $_POST['candy_rate'];
      $spl_rmrk = $_POST['spl_rmrk'];

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
      $term_condtion=$_POST['term_condtion'];
      $product_name=$_POST['product_name'];

      $org_rate=$_POST['candy_rate'];

      $username= $_SESSION["username"];
      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
      
      
      

      
      // ...............Pur_report edit Start...........................
          
       $sql_pur_report = "select * from pur_report where pur_conf_ids = '".$id."'";

        $result_pur_report = mysqli_query($conn, $sql_pur_report);      
        if(mysqli_num_rows($result_pur_report)){
          while($row_pur_report = mysqli_fetch_assoc($result_pur_report)){
            $pur_report_ids = $row_pur_report['id'];

            $pur_paySQL="update pur_pay set
                  
                  party='".$party."'
                  where pur_report_id='".$pur_report_ids."'";
               
              $reportSQL = mysqli_query($conn, $pur_paySQL);
              if($reportSQL){
                $successMsg = 'New record Updated successfully';
                
              }else{
                $errorMsg = 'Error '.mysqli_error($conn);
                  echo $errorMsg;
              }





            // transport payout update

              $trans_payoutSQL="update transport_payout set
                  trans_id='".$transport_name."',
                  ext_party_id='".$party."'
                  where pur_report_id='".$pur_report_ids."'";
               
              $trans_payoutResult = mysqli_query($conn, $trans_payoutSQL);
              if($trans_payoutResult){
                $successMsg = 'New record Updated successfully';
                
              }else{
                $errorMsg = 'Error '.mysqli_error($conn);
                  echo $errorMsg;
              }








              // ...............debit_report edit Start..........

                $sql_debit_report = "select * from debit_report where conf_no = '".$pur_conf."' AND financial_year='".$_SESSION['pur_financial_year_id']."'";

                $result_debit_report = mysqli_query($conn, $sql_debit_report);      
                if(mysqli_num_rows($result_debit_report)){
                  while($row_debit_report = mysqli_fetch_assoc($result_debit_report)){
                    $debit_report_ids = $row_debit_report['id'];

                    if ($row_debit_report['gross_amt'] == '') {
                      $grs_amt = 0;
                    }else{

                      $grs_amt = $row_debit_report['gross_amt'];
                    }

                    if ($row_debit_report['weight'] == '') {
                      $weight = 0;
                    }else{
                      $weight = $row_debit_report['weight'];
                    }

                    //............RD-Start.................
                      if ($pro_rd == '') {
                        $pro_rd = 0;
                      }
                      if ($row_debit_report['rd_lab'] == '') {
                        $rd_lab = 0;
                      }else{
                        $rd_lab = $row_debit_report['rd_lab'];
                      }
                      $rd_diff = $pro_rd - $rd_lab;
                      if ($rd_diff<0) {
                        $rd_diff = 0;
                      }

                        // pur_report 
                        if ($row_debit_report['rd_cndy'] == '') {
                          $rd_cndy = 0;
                        }else{
                          $rd_cndy = $row_debit_report['rd_cndy'];
                        }
                        

                        $rd_amt = number_format($grs_amt-($weight*(($org_rate-$rd_cndy)*0.2812/100)),2,'.', '');
                       

                    //............RD-END.................
              
                    //............Length-Start..............

                      if ($row_debit_report['len_lab'] == '') {
                        $len_lab = 0;
                      }else{

                       $len_lab =$row_debit_report['len_lab'];

                      }
                      if ($pro_length == '') {
                        $pro_length = 0;
                      }
                      $len_diff = $pro_length - $len_lab;
                      if($len_diff<0){
                          $len_diff = 0;

                      }

                      // pur_report 
                      if ($row_debit_report['len_cndy'] == '') {
                        $len_cndy = 0;
                      }else{
                        $len_cndy = $row_debit_report['len_cndy'];
                      }
                      $len_amt = number_format($grs_amt-($weight*(($candy_rate-$len_cndy)*0.2812/100)),2,'.', '');

                      

                    //............Length-END..............


                    //............MIC-Start..............

                      if ($row_debit_report['mic_lab'] == '') {
                        $mic_lab = 0;
                      }else{

                       $mic_lab =$row_debit_report['mic_lab'];

                      }
                      if ($pro_mic == '') {
                        $pro_mic = 0;
                      }
                      $mic_diff = $pro_mic - $mic_lab;
                      if($mic_diff<0){
                          $mic_diff = 0;
                      }

                      // pur_report

                        if ($row_debit_report['mic_cndy'] == '') {
                          $mic_cndy = 0;
                        }else{
                          $mic_cndy = $row_debit_report['mic_cndy'];
                        }
                        $mic_amt = number_format($grs_amt-($weight*(($candy_rate-$mic_cndy)*0.2812/100)),2,'.', '');
                    //............MIC-END..............


                    //............Trash-Start..............

                      if ($row_debit_report['trs_lab'] == '') {
                        $trs_lab = 0;
                      }else{

                       $trs_lab =$row_debit_report['trs_lab'];

                      }
                      if ($pro_trash == '') {
                        $pro_trash = 0;
                      }
                      $trs_diff = number_format(($trs_lab)-($pro_trash),2);
                      if($trs_diff<0){
                          $trs_diff = 0;
                      }

                      // pur_report
                      // if ($row_debit_report['trs_diff'] == '') {
                      //   $trs_diff = 0;
                      // }else{
                      //   $trs_diff = $row_debit_report['trs_diff'];
                      // }
                      $trs_amt = number_format(($grs_amt*$trs_diff/100),2,'.', '');
                      
                    //............Trash-END..............

                    //............Moisture -Start..............

                      if ($row_debit_report['mois_lab'] == '') {
                        $mois_lab = 0;
                      }else{

                       $mois_lab =$row_debit_report['mois_lab'];

                      }
                      if ($pro_mois == '') {
                        $pro_mois = 0;
                      }
                      $mois_diff = number_format(($mois_lab)-($pro_mois),2);
                      if($mois_diff<0){
                          $mois_diff = 0;
                      }
                      $mois_amt = number_format(($grs_amt*$mois_diff/100),2,'.', '');
                      
                    //............Moisture -END..............

                    // #Sample START............................................
                      if ($row_debit_report['sample_kg'] == '') {
                        $sample_kg = 0;
                      }else{
                        $sample_kg = $row_debit_report['sample_kg'];
                      }
                      $smp_amt = number_format($sample_kg*($org_rate*0.2812/100),2,'.', '');
                    // ENDSample ............................................

                    // #Extra Tare START............................................
                      if ($row_debit_report['tare_kg'] == '') {
                        $tare_kg = 0;
                      }else{
                        $tare_kg = $row_debit_report['tare_kg'];
                      }
                      $tare_amt = number_format($tare_kg*($org_rate*0.2812/100),2,'.', '');
                    // END Extra Tare ............................................

                    // #Brokerage START............................................
                      if ($row_debit_report['brok_option'] == 'dynamic') {
                          if ($row_debit_report['brok_per_bales'] == '') {
                            $brok_per_bales = 0;
                          }else{
                            $brok_per_bales = $row_debit_report['brok_per_bales'];
                          }
                          if ($row_debit_report['brok_bales'] == '') {
                            $brok_bales = 0;
                          }else{
                            $brok_bales = $row_debit_report['brok_bales'];
                          }
                          $brok_amt = number_format(($brok_per_bales*$brok_bales),2,'.', '');  
                      }
                    // ENDBrokerage ............................................

                    //Weight Shoratage : 
                    
                      if ($row_debit_report['weight'] == '') {
                        $wght_seller_slip = 0;
                      }else{
                        $wght_seller_slip = $row_debit_report['weight'];
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
                    
                      $shortage_amt = number_format($shortage*($org_rate*0.2812/100),2,'.', '');
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
                     
                      $rate_diff_amount = number_format($grs_amt-($weight*($org_rate-$rate_diff_candy)*0.2812/100),2,'.', '');



                    // rate difference End.................................

                    // ins option................................................
                      
                      if ($row_debit_report['ad_hoc'] == '') {
                        $ad_hoc = 0;
                      }else{

                        $ad_hoc = $row_debit_report['ad_hoc'];
                      }
                      
                      $interest ='';
                     
                      if($row_debit_report['int_option'] == 'manual'){
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


                        $interest = number_format(($int_amount*$ad_hoc*$int_days*$int_rate/36000),2,'.', '');
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

                    
                    // Balance Payable end 

                    $ReportSQLEdit="update debit_report set
                      rd_con='".$pro_rd."',
                      party='".$party."',
                      conf_no='".$pur_conf."',
                      len_con='".$pro_length."',
                      mic_con='".$pro_mic."',
                      trs_con= '".$pro_trash."',
                      mois_con= '".$pro_mois."',
                      rd_diff = '".$rd_diff."',
                      len_diff = '".$len_diff."',
                      mic_diff = '".$mic_diff."',
                      trs_diff = '".$trs_diff."',
                      rd_amt = '".$rd_amt."',
                      len_amt = '".$len_amt."',
                      mic_amt = '".$mic_amt."',
                      trs_amt = '".$trs_amt."',
                      mois_diff = '".$mois_diff."',
                      mois_amt = '".$mois_amt."',
                      sample_amt = '".$smp_amt."',
                      tare_amt = '".$tare_amt."',
                      brok_amt = '".$brok_amt."',
                      slip_diff = '".$slip_diff."',
                      shortage = '".$shortage."',
                      broker= '".$broker."',
                      candy_rate='".$candy_rate."',
                      original_rate='".$org_rate."',
                      shortage_amt = '".$shortage_amt."',
                      interest = '".$interest."',
                      repress_total = '".$repress_total."',
                      debit_amount = '".$debit_amount."',
                      rate_diff_amount = '".$rate_diff_amount."',
                      tax_amount = '".$debit_tax_amount."',
                      balance_pay = '".$Balance_Payable."',
                      final_debit_amount = '".$final_debit_amount_with_tax."',
                      tds_amount = '".$tds_amount."'

                      where 
                      id='".$debit_report_ids."'
                    ";

                    $reportSQL = mysqli_query($conn, $ReportSQLEdit);
                    if($reportSQL){
                      $successMsg = 'New record Updated successfully';
                      
                    }else{
                      $errorMsg = 'Error '.mysqli_error($conn);
                        echo $errorMsg;
                    }



                    // pur_pay start
                    
                      $sql_pur_pay = "select * from pur_pay where invoice_no = '".$row_debit_report['invoice_no']."' AND pur_report_id ='".$row_pur_report['id']."' AND financial_year_id='".$_SESSION['pur_financial_year_id']."'";
            
                        $result_sql_pur_pay = mysqli_query($conn, $sql_pur_pay);


                        if(mysqli_num_rows($result_sql_pur_pay)){
                          while($row_sql_pur_pay = mysqli_fetch_assoc($result_sql_pur_pay)){
                            
                            $pur_pay_ids = $row_sql_pur_pay['id'];
                          
                            $invoice_amt = $row_sql_pur_pay['invoice_amt'];

                            $final_debit_amount =  $final_debit_amount_with_tax;

                            if ($invoice_amt == '') {
                              $invoice_amt = 0;
                            }else{

                              $invoice_amt = $row_sql_pur_pay['invoice_amt'];

                            }

                            if ($final_debit_amount == '') {

                              $final_debit_amount = 0;
                            }else{

                              $final_debit_amount = $final_debit_amount_with_tax;

                            }

                            $ad_hoc = $row_sql_pur_pay['ad_hoc'];
                            if ($ad_hoc == '') 
                            {
                              $ad_hoc = 0;
                            }else{

                              $ad_hoc = $row_sql_pur_pay['ad_hoc'];

                            }


                            if($tds_amount=='')
                            {
                              $tds_amount=0;
                            }





                            $net_amt = number_format(($invoice_amt - $final_debit_amount-$ad_hoc-$tds_amount),2,'.','');
                            $net_amt=round($net_amt);




                            $dynamic_field1 = json_decode($row_sql_pur_pay['dynamic_field']);
                            $dynamic_AmtCount = 0;
                            foreach ($dynamic_field1 as $key => $value) {
                              $dynamic_AmtCount +=(float)$value->amt;
                               
                            }

                            $bill2billDynamicAmount=0;
                          if(isset($row_sql_pur_pay['bill2bill_dynamic_data']) && $row_sql_pur_pay['bill2bill_dynamic_data']!='')
                          {
                            $b2bArr = json_decode($row_sql_pur_pay['bill2bill_dynamic_data'],true);                      
                            foreach ($b2bArr as $key => $value) {
                              if($value['b2b_amount']!='')
                              {
                                $bill2billDynamicAmount +=(float)$value['b2b_amount'];
                              }
                              
                            }
                          }



                            $pay_amt = number_format(($net_amt - $dynamic_AmtCount - $bill2billDynamicAmount),2,'.','') ; 

                            $Pur_Pay_SQLEdit="update pur_pay set
                              invoice_amt = '".$invoice_amt."',
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

            }
          }

      // ...............debit_report edit End...........................


             //transport details
            $trans_id=$transport_name;
            $trans_pay_type=$_POST['trans_pay_type'];
            $trans_veh_no=$row_pur_report['trans_veh_no'];

              if($trans_pay_type=='to_be_build')
              {
                  
                  $trans_lr_no='';
                  $trans_amount='';
                  $trans_lr_date='';  
              }
              else
              {
                
                 
                  $trans_lr_no=$row_pur_report['trans_lr_no'];
                  $trans_amount=$row_pur_report['trans_amount'];
                  $trans_lr_date=$row_pur_report['trans_lr_date'];
                
              }



            $avl_bales = $bales - $row_pur_report['bales']; 
                      
                $ReportSQLEdit="update pur_report set
                  conf_no='".$pur_conf."',
                  party='".$party."',
                  avl_bales='".$avl_bales."',
                  cndy_rate='".$candy_rate."',
                  broker= '".$broker."',
                  trans_id='".$trans_id."',
                  trans_pay_type='".$trans_pay_type."',
                  trans_veh_no='".$trans_veh_no."',
                  trans_lr_no='".$trans_lr_no."',
                  trans_amount='".$trans_amount."',
                  trans_lr_date='".$trans_lr_date."'
                  where 
                  id='".$pur_report_ids."'
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

      // ...............Pur_report edit End...........................
        if(!isset($errorMsg))
        {

          $sql="update pur_conf set 
          pur_conf='".$pur_conf."',
          conf_type='".$conf_type."',
          pur_date='".$pur_date."', 
          pur_report_date='".$pur_report_date."',
          party='".$party."',
          firm='".$firm."',
          broker='".$broker."',
          pro_length='".$pro_length."',
          pro_mic='".$pro_mic."',
          pro_rd='".$pro_rd."',
          pro_trash='".$pro_trash."',
          pro_mois='".$pro_mois."',
          bales='".$bales."',
          spl_rmrk='".$spl_rmrk."',
          financial_year='".$financial_year."',
          candy_rate='".$candy_rate."',

          dispatch='".$dispatch."',
          trans_name='".$transport_name."',
          trans_pay_type='".$trans_pay_type."',
          no_of_vehicle='".$no_of_vehicle."',
          vehicle_no='".$vehicle_nos."',
          ins_cmpny='".$ins_cmp_name."',
          ins_policy_no='".$ins_policy_no."',
          pay_term='".$pay_term."',
         
          laboratory_master = '".$laboratory_master."',
          delivery_date = '".$delivery_date."',
          station = '".$station."',
          term_condtion = '".$term_condtion."',
          product_name = '".$product_name."',
          
          username = '".$username."',
          updated_at = '".$timestamp."'
           

      
          where id='".$id."'";


          
          $result = mysqli_query($conn, $sql);
          if($result){
            $successMsg = 'Record Updated successfully';
           $page=1;
            if(isset($_POST['page']))
            {
              $page=$_POST['page'];
            }
            header("Location: index.php?page=$page");
          }else{
            $errorMsg = 'Error '.mysqli_error($conn);
                    echo $errorMsg;
          }
        }
        }else{
          echo '<h1>Please enter date between financial date('.$startdate.' to '.$enddate.')</h1>';
        }
  

    
?>
