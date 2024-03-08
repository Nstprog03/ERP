<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}



function getExternalPartyDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from external_party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

    $arr[0]=$partyrow['partyname'];

    $arr[1]=$partyrow['address'].', '.$partyrow['city'].', '.$partyrow['district'].', '.$partyrow['state'].' - '.$partyrow['pincode'];

    return $arr;

}

function getBrokerDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from broker where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

    $arr[0]=$partyrow['name'];

    $arr[1]=$partyrow['address'];

    return $arr;

}

function getTransportDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from transport where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

    $arr[0]=$partyrow['trans_name'];
    $arr[1]=$partyrow['trans_addr'];

    return $arr;

}


function getFarmerName($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from farmer where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $name='';
    if(mysqli_num_rows($partyresult)>0)
    {
       $partyrow = mysqli_fetch_array($partyresult);
       $arr[]=$partyrow['farmer_name'];
       $arr[]='Village -'.$partyrow['vlg_name'].', Ta. - '.$partyrow['tal_name'].', Dist. - '.$partyrow['dist_name'];
    }
 
    return $arr;

}


function getFirmDetails($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from party where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $arr[]=$partyrow['party_name'];

    return $arr;

}




//covert To dd/mm/yyy
function convertDate($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('-', '/', $date);
    $final_date = date('d/m/Y', strtotime($final_date));
  }
    return $final_date;

}


//covert To yyyy-mm-dd
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('/', '-', $date);
    $final_date = date('Y-m-d', strtotime($final_date));
  }
    return $final_date;
}


function array_sort_by_column(&$array, $column, $direction = SORT_DESC) {
    $reference_array = array();

    foreach($array as $key => $row) {
        $reference_array[$key] = $row[$column];
    }

    array_multisort($reference_array, $direction, $array);
}

function convertDateArr($mainArr)
{
      foreach ($mainArr as $key => $item) 
      {
         $mainArr[$key]['date']=convertDate($item['date']);
      }
      return $mainArr;

}





if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}



  if(isset($_POST['submit']))
  {
      $start_date='';
      $end_date='';
 
     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

      }



      $mainArr  = array();
      $i=0;
    
      if($start_date!='' && $end_date!='')
      {


         // bales_payout
        if(isset($_POST['tbl_bales_payout']))
        {
          $balesSQL="select * from pur_pay ORDER by ad_hoc_date DESC";
           $balesResult = mysqli_query($conn, $balesSQL);

         while($bales_row = mysqli_fetch_assoc($balesResult))
         {

            if($bales_row['ad_hoc_date']>=$start_date && $bales_row['ad_hoc_date']<=$end_date)
            {
              $mainArr[$i]['date'] = $bales_row['ad_hoc_date'];
             $mainArr[$i]['invoice_no'] = $bales_row['invoice_no'];
             $mainArr[$i]['firm'] = getFirmDetails($bales_row['firm_id'])[0];
             $mainArr[$i]['party_name'] = getExternalPartyDetails($bales_row['party'])[0];
             $mainArr[$i]['party_address'] = getExternalPartyDetails($bales_row['party'])[1];
             $mainArr[$i]['amount'] = $bales_row['ad_hoc'];
             $mainArr[$i]['remark'] = 'Purchase Bales Payout, AD-Hoc';  
            }

           if($bales_row['dynamic_field']!='')
           {
                $dynamic_field=json_decode($bales_row['dynamic_field']);
                if(count($dynamic_field)>0)
                {
                    foreach ($dynamic_field as $key => $item) 
                    {

                      if($item->date>=$start_date && $item->date<=$end_date)
                      {
                           $i+=1;
                           $mainArr[$i]['date'] = $item->date;
                           $mainArr[$i]['invoice_no'] = $bales_row['invoice_no'];
                           $mainArr[$i]['firm'] = getFirmDetails($bales_row['firm_id'])[0];
                           $mainArr[$i]['party_name'] = getExternalPartyDetails($bales_row['party'])[0];
                           $mainArr[$i]['party_address'] = getExternalPartyDetails($bales_row['party'])[1];
                           $mainArr[$i]['amount'] = $item->amt;
                           $mainArr[$i]['remark'] = "Purchase Bales Payout, ".$item->lable;
                      }

                    }

                }
             }


             //bill 2 bill dynamic field
             if($bales_row['bill2bill_dynamic_data']!='')
           {
                $bill2bill_dynamic_data=json_decode($bales_row['bill2bill_dynamic_data']);
                if(count($bill2bill_dynamic_data)>0)
                {
                    foreach ($bill2bill_dynamic_data as $key => $item) 
                    {

                      if($item->b2b_date>=$start_date && $item->b2b_date<=$end_date)
                      {
                           $i+=1;
                           $mainArr[$i]['date'] = $item->b2b_date;
                           $mainArr[$i]['invoice_no'] = $bales_row['invoice_no'];
                           $mainArr[$i]['firm'] = getFirmDetails($bales_row['firm_id'])[0];
                           $mainArr[$i]['party_name'] = getExternalPartyDetails($bales_row['party'])[0];
                           $mainArr[$i]['party_address'] = getExternalPartyDetails($bales_row['party'])[1];
                           $mainArr[$i]['amount'] = $item->b2b_amount;
                           $mainArr[$i]['remark'] = "Purchase Bales Payout, ".$item->b2b_label;
                      }

                    }

                }
             }

             $i++;
           
           }
         }


         //get record from debit report if bales payout is not created
        if(isset($_POST['tbl_pur_debit_report']))
        {
          $sqlDebitReport="select * from debit_report where ad_hoc_date>='".$start_date."' AND ad_hoc_date<='".$end_date."' ORDER by ad_hoc_date DESC";
           $resultDebitReport = mysqli_query($conn, $sqlDebitReport);

           while($rowDebitReport = mysqli_fetch_assoc($resultDebitReport))
           {
              //check if bales payour created if not then add that record in array

              $sqlCheck="select * from pur_pay where debit_report_id='".$rowDebitReport['id']."'";
              $resultCheck = mysqli_query($conn, $sqlCheck);

              if(mysqli_num_rows($resultCheck)==0)
              {

                $mainArr[$i]['date'] = $rowDebitReport['ad_hoc_date'];
                $mainArr[$i]['invoice_no'] = $rowDebitReport['invoice_no'];
                $mainArr[$i]['firm'] = getFirmDetails($rowDebitReport['firm'])[0];
                $mainArr[$i]['party_name'] = getExternalPartyDetails($rowDebitReport['party'])[0];
                $mainArr[$i]['party_address'] = getExternalPartyDetails($rowDebitReport['party'])[1];
                $mainArr[$i]['amount'] = $rowDebitReport['ad_hoc'];
                $mainArr[$i]['remark'] = 'Purchase Debit Report';
                $i++;

              }

           }
         }









         // transport_payout
      if(isset($_POST['tbl_transport_payout']))
      {
        $transSQL="select * from transport_payout where pay_date>='".$start_date."' AND pay_date<='".$end_date."' ORDER by pay_date DESC";
         $transResult = mysqli_query($conn, $transSQL);

         while($trans_row = mysqli_fetch_assoc($transResult))
         {

           $mainArr[$i]['date'] = $trans_row['pay_date'];
           $mainArr[$i]['invoice_no'] = $trans_row['trans_lr_no'];
            $mainArr[$i]['firm'] = getFirmDetails($trans_row['firm_id'])[0];
           $mainArr[$i]['party_name'] = getTransportDetails($trans_row['trans_id'])[0];
           $mainArr[$i]['party_address'] = getTransportDetails($trans_row['trans_id'])[1];
            $mainArr[$i]['amount'] = $trans_row['total_amount'];
            $mainArr[$i]['remark'] = 'Transport Payout';
           $i++;
         
         }
       }


       if(isset($_POST['tbl_other_payout']))
        {
         // other_payout
        $otherSQL="select * from other_payout where date>='".$start_date."' AND date<='".$end_date."' ORDER by date DESC";
         $otherResult = mysqli_query($conn, $otherSQL);

         while($other_row = mysqli_fetch_assoc($otherResult))
         {

           $mainArr[$i]['date'] = $other_row['date'];
           $mainArr[$i]['invoice_no'] = $other_row['invoice_no'];
           $mainArr[$i]['firm'] = getFirmDetails($other_row['firm_id'])[0];

           if($other_row['pay_to']=='b')
           {
              $mainArr[$i]['party_name'] = getBrokerDetails($other_row['broker_id'])[0].' (Broker)';
              $mainArr[$i]['party_address'] = getBrokerDetails($other_row['broker_id'])[1];
           }
           else if($other_row['pay_to']=='e')
           {
              $mainArr[$i]['party_name'] = getExternalPartyDetails($other_row['ext_party_id'])[0];
              $mainArr[$i]['party_address'] = getExternalPartyDetails($other_row['ext_party_id'])[1];
           }
           else
           {
              $mainArr[$i]['party_name'] = '';
              $mainArr[$i]['party_address'] = '';
           }

           


            $mainArr[$i]['amount'] = $other_row['amount'];
            $mainArr[$i]['remark'] = $other_row['remark'];
           $i++;
         
         }
       }


           // rd payout
        if(isset($_POST['tbl_rd_payment']))
        {

        $RdSQL="select * from rd_kapas_payment";
         $rdResult = mysqli_query($conn, $RdSQL);

         while($rd_row = mysqli_fetch_assoc($rdResult))
         {

               if($rd_row['dynamic_field']!='')
               {
                  $dynamic_field=json_decode($rd_row['dynamic_field']);

                  if(count($dynamic_field)>0)
                  {
                      foreach ($dynamic_field as $key => $item) 
                      {
                        if($item->date>=$start_date && $item->date<=$end_date)
                        {
                             $i+=1;
                             $mainArr[$i]['date'] = $item->date;
                             $mainArr[$i]['invoice_no'] = $rd_row['invoice_no'];
                             $mainArr[$i]['firm'] = getFirmDetails($rd_row['firm_id'])[0];
                             $mainArr[$i]['party_name'] = getExternalPartyDetails($rd_row['party'])[0];
                             $mainArr[$i]['party_address'] = getExternalPartyDetails($rd_row['party'])[1];
                             $mainArr[$i]['amount'] = $item->amt;
                             $mainArr[$i]['remark'] = "RD Kapas Purchase Payment, ".$item->lable;
                        }

                      }

                  }
               }


                 //bill 2 bill dynamic field
          if($rd_row['bill2bill_dynamic_data']!='')
           {
                $bill2bill_dynamic_data=json_decode($rd_row['bill2bill_dynamic_data']);
                if(count($bill2bill_dynamic_data)>0)
                {
                    foreach ($bill2bill_dynamic_data as $key => $item) 
                    {

                      if($item->b2b_date>=$start_date && $item->b2b_date<=$end_date)
                      {
                           $i+=1;
                           $mainArr[$i]['date'] = $item->b2b_date;
                           $mainArr[$i]['invoice_no'] = $rd_row['invoice_no'];
                           $mainArr[$i]['firm'] = getFirmDetails($rd_row['firm_id'])[0];
                           $mainArr[$i]['party_name'] = getExternalPartyDetails($rd_row['party'])[0];
                           $mainArr[$i]['party_address'] = getExternalPartyDetails($rd_row['party'])[1];
                           $mainArr[$i]['amount'] = $item->b2b_amount;
                           $mainArr[$i]['remark'] = "RD Kapas Purchase Payment, ".$item->b2b_label;
                      }

                    }

                }
             }



               $i++;
         
         }
       }

       



          // urd_kapas_purchase_payment
        if(isset($_POST['tbl_urd_payment']))
        {
          $urdSQL="select * from urd_purchase_payment where date>='".$start_date."' AND date<='".$end_date."' ORDER by date DESC";
           $urdResult = mysqli_query($conn, $urdSQL);

           while($urd_row = mysqli_fetch_assoc($urdResult))
           {

             $mainArr[$i]['date'] = $urd_row['date'];
             $mainArr[$i]['invoice_no'] = '';
             $mainArr[$i]['firm'] = getFirmDetails($urd_row['firm'])[0];
             $mainArr[$i]['party_name'] = getFarmerName($urd_row['farmer'])[0];
             $mainArr[$i]['party_address'] = getFarmerName($urd_row['farmer'])[1];
             $mainArr[$i]['amount'] = $urd_row['amount'];
             $mainArr[$i]['remark'] = 'Man Weight : '.$urd_row['weight'].', Man Rate : '.$urd_row['rate'];
             $i++;
           
           }
        }
      
      }



      //sort by date desc
      array_sort_by_column($mainArr,'date');



      //convert to dd/mm/yyyy
      $mainArr=convertDateArr($mainArr);


     

   

    $_SESSION['daily_payment_export_data']=$mainArr;


  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Daily Payment Report</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">



    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

     <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>

      <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
         rel = "stylesheet">

      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

       <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

       
       <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 








     <script> 

    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 

       $(".datepicker").datepicker({

        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
      });

       $(".datepicker").keydown(false);

       $(".mul-select").select2({
        placeholder: "Select Option",
      
         theme: "classic",
         width: 'element',
        

      });

      

    });
    </script> 



  </head>
  <body>



    
    <div class="wrapper">
      <div id="sidebarnav"></div>

        <!-- Page Content  -->
        <div id="content">
          <div id="topnav"></div>

      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Daily Payment Report</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>
               <ul class="navbar-nav">

             

               <!--  <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li> -->

              </ul>
          </div>
        </div>
      </nav>

      <div class="container-fluid">
        <div class="row justify-content-center">

                <div class="card">
                  <div class="card-header">Filter</div>
                      <div class="card-body">
                        <form class="" action="" method="post" enctype="multipart/form-data">
                    <div class="row justify-content-center">

                             <div class="form-group col-md-6">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off" required="">
                            </div>

                            <div class="form-group col-md-6">
                              <label for="end_date">End Date :</label>
                                <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off" required="">
                            </div>

                           

                      </div>

                          <div class="row">

                            <div class="col-md-12">
                                 <h6>Table :</h6>
                            </div>

                          </div>

                          
                          
                         
                         <div class="row ml-2">
                          
                             <div class=" form-check col-md-3">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_bales_payout" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_bales_payout'])){echo 'checked';}}else{echo 'checked';}?>>Purchase Bales Payout
                                </label>
                              </div>
                              <div class="form-check  col-md-3">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_pur_debit_report" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_pur_debit_report'])){echo 'checked';}}else{echo 'checked';}?>>Purchase Debit Report
                                </label>
                              </div>
                                <div class="form-check  col-md-3">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_transport_payout" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_transport_payout'])){echo 'checked';}}else{echo 'checked';}?>>Transport Payout
                                </label>
                              </div>
                               <div class="form-check  col-md-3">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_other_payout" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_other_payout'])){echo 'checked';}}else{echo 'checked';}?>>Other Payout
                                </label>
                              </div>
                               <div class="form-check  col-md-3">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_rd_payment" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_rd_payment'])){echo 'checked';}}else{echo 'checked';}?>>RD Kapas Purchase Payment
                                </label>
                              </div>
                              <div class="form-check  col-md-4">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" name="tbl_urd_payment" value="1" <?php if(isset($_POST['submit'])){if(isset($_POST['tbl_urd_payment'])){echo 'checked';}}else{echo 'checked';}?>>URD Kapas Purchase & Payment
                                </label>
                              </div>
                           
                         </div>
                         <br>

                           
                        <div class="row">

                           <div class="form-group col-md-1">
                            <button type="submit" name="submit" class="btn btn-primary waves">Filter</button>
                          </div>
                          <div class="form-group col-md-1">
                            <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear Filter</button>
                          </div>
                           
                         </div>
                           


                        </form>
                        
                     </div>
                </div>
          
                <div class="card mt-3">
                   
                      <div class="card-body expoert-register">

                        <?php
                          if(isset($_POST['submit']))
                          {
                            ?>
                            <div class="export-cta">
                             
                                
                              <a href="export.php" name="export" class="btn btn-info">Export To Excel</a>
                              
                         
                            </div>
                            <br>
                            <?php
                          }
                        ?>
                      <table id="example" class="registertable table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Ref/Inv/Deliver No</th>
                                <th>Firm</th>
                                <th>External Party / Broker</th>
                                <th>External Pary / Broker Address</th>
                                <th>Amount</th>
                                <th>Remark</th>
                                </tr>
                        </thead>
                        <tfoot>
                          
                            
                          
                          <tr>
                              <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Ref/Inv/Deliver No</th>
                                <th>Firm</th>
                                <th>External Party Name</th>
                                <th>External Pary Address</th>
                                <th>Amount</th>
                                <th>Remark</th>
                                </tr>
                               
                               
                          </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {

                            if (count($mainArr)>0) {

                            
                            
                            $i=0;
                            foreach ($mainArr as $key => $value) {
                               
                              
                            ?>
                          

                          <tr>
                            <td><?php echo $i = $i+1 ?></td>
                            
                            <td><?php echo $value['date']; ?></td>
                            <td><?php echo $value['invoice_no']; ?></td>

                            <td><?php echo $value['firm']; ?></td>

                            <td><?php echo $value['party_name']; ?></td>

                              <td><?php echo $value['party_address']; ?></td>

                              <td><?php echo $value['amount']; ?></td>

                              <td><?php echo $value['remark']; ?></td>


                            






                          
                          
                            
                           

                          
                           
                          </tr>

                          <?php }
                           }

                        }


                          ?>

                          
                          
                          
                        </tbody>
                      </table>
                    </div>
                </div>
            

        </div>
      </div>

</div>
</div>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
    <script type="text/javascript">

       

    $(document).ready(function() {

      
       




      } );
    </script>
  </body>
</html>
