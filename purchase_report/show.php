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
  $dir = "/file_storage/";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pur_report where id=".$id;
    $result = mysqli_query($conn, $sql);



    if (mysqli_num_rows($result) > 0) 
      {
      $row = mysqli_fetch_assoc($result);

      }
    else 
      {
      $errorMsg = 'Could not Find Any Record';
      }
    //   echo "<pre>";
    //   print_r($row);
    //   echo "</pre>";
    // exit();
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Purchase Report </title>
 
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
     <script> 
    $(function(){
     $("#sidebarnav").load("../nav.html"); 
      $("#topnav").load("../nav2.html"); 
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Purchase Report</span></a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav mr-auto"></ul>

                <?php
                  $page=1;
                  if(isset($_GET['page']))
                  {
                    $page=$_GET['page'];
                  }
                  ?>

              <ul class="navbar-nav ml-auto"><a class="btn btn-outline-danger" href="index.php?page=<?php echo $page ?>"><i class="fa fa-sign-out-alt"></i><span>Back</span></a>
              </ul>


          </div>
        </div>
      </nav>

      <!-- last change on table START-->
       <div class="last-updates">
                  <div class="firm-selectio">
             <div class="firm-selection-pre">
                <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["pur_firm"]; ?></span>
            </div>
            <div class="year-selection-pre">
            <span class="pre-year-text">Financial Year :</span> 
            <span class="pre-year">
              <?php 

              $finYearArr=explode('/',$_SESSION["pur_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 

              ?>
            </span>
            </div>
          </div>
          <div class="last-edits-fl">
        <?php
          $sqlLastChange="select username,updated_at from pur_report where id='".$row['id']."'";

           $resultlLastChange = mysqli_query($conn, $sqlLastChange);

           if(mysqli_num_rows($resultlLastChange)>0)
           {
              $lastChangeRow=mysqli_fetch_assoc($resultlLastChange);

              //.get username from user master
              $user_name='';
              $sqlgetUser="select * from users where id='".$lastChangeRow['username']."'";
              $sqlResultGetUser = mysqli_query($conn, $sqlgetUser);
              if(mysqli_num_rows($sqlResultGetUser)>0)
              {
                $getUserRow=mysqli_fetch_assoc($sqlResultGetUser);
                $user_name=$getUserRow['name'];
              }

              echo "
                
                      <span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>
                 
              ";
           }
          ?>

        </div>
          </div>

          <!-- last change on table END-->    



      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="card">
            <div class="card-header">
              Party Details
            </div>
            <div class="card-body">
                <div class="row">
                  <table class="table table-bordered">

                        <div class="form-group">
                            <tr>
                                <th>External Party</th>
                                <td><?php 

                            $party = "select * from external_party where id='".$row['party']."'";
                              $partyresult = mysqli_query($conn, $party);

                              $partyrow = mysqli_fetch_assoc($partyresult);

                              $ex_party='';
                              if(isset($partyrow))
                              {
                                $ex_party=$partyrow['partyname'];
                              }
                              echo $ex_party;


                                 ?></td>
                            </tr>
                            <tr>
                                <th>Confirmation No</th>
                                <td><?php echo $row['conf_no'] ?></td>
                            </tr>
                            <tr>
                              <?php
                              $report_date='';
                              if($row['report_date']!='' && $row['report_date']!='0000-00-00')
                              {
                              $report_date=date("d/m/Y", strtotime($row['report_date']));
                              }
                              ?>

                                <th>Report Date</th>
                                <td><?php echo  $report_date; ?></td>
                            </tr>

                            <tr>
                                <th>Available Bales</th>
                                <td><?php echo $row['avl_bales'] ?></td>
                            </tr>

                            <tr>
                                <th>Candy Rate</th>
                                <td><?php echo $row['cndy_rate'] ?></td>
                            </tr>

                            <tr>
                                <th>Lot No</th>
                                <td><?php echo $row['lot_no'] ?></td>
                            </tr>

                            <tr>
                                <th >PR No Start</th>
                                <td><?php echo $row['pr_no_start'] ?> </td>
                                
                            </tr>

                            <tr>
                                <th >PR No End</th>
                                <td><?php echo $row['pr_no_end'] ?> </td>
                                
                            </tr>


                            <?php

                             $trans_name='';
                                  if($row['trans_id']!='')
                                  {
                                        
                                    $sql_trans="select trans_name from transport where id='".$row['trans_id']."'";
                                    $result_trans = mysqli_query($conn, $sql_trans);
                                    $row_trans=mysqli_fetch_assoc($result_trans);
                                    $trans_name=$row_trans['trans_name'];

                                  }

                              if($row['trans_pay_type']=='to_be_pay')
                              {

                                 


                                  $trans_lr_date='';
                                  if($row['trans_lr_date']!='' && $row['trans_lr_date']!='0000-00-00')
                                    {
                                      $trans_lr_date = str_replace('-', '/', $row['trans_lr_date']);
                                      $trans_lr_date = date('d/m/Y', strtotime($trans_lr_date));
                                    }



                              ?>
                               <tr>
                                  <th >Transport Name</th>
                                  <td><?php echo $trans_name ?> </td>
                               </tr>

                                <tr>
                                  <th >Transport Vehicle No.</th>
                                  <td><?php echo $row['trans_veh_no'] ?> </td>
                               </tr>

                                <tr>
                                  <th >Transport LR Date</th>
                                  <td><?php echo $trans_lr_date ?> </td>
                               </tr>

                                <tr>
                                  <th >Transport LR No.</th>
                                  <td><?php echo $row['trans_lr_no'] ?> </td>
                               </tr>

                                <tr>
                                  <th >Transport Amount</th>
                                  <td><?php echo $row['trans_amount'] ?> </td>
                               </tr>


                              <?php
                                }
                                else
                                {
                                ?>

                                 <tr>
                                  <th >Transport Name</th>
                                  <td><?php echo $trans_name ?> </td>
                               </tr>

                                <tr>
                                  <th >Transport Vehicle No.</th>
                                  <td><?php echo $row['trans_veh_no'] ?> </td>
                               </tr>

                                <?php
                                }
                              ?>




                            <tr>
                                <th >Weight</th>
                                <td><?php echo $row['weight'] ?> </td>
                                
                            </tr>

                            <tr>
                                <th>Broker</th>
                                <td><?php 

                            $broker = "select * from broker where id='".$row['broker']."'";
                              $broker_result = mysqli_query($conn, $broker);

                              $broker_row = mysqli_fetch_assoc($broker_result);

                              $ex_broker_='';
                              if(isset($broker_row))
                              {
                                $ex_broker_=$broker_row['name'];
                              }
                              echo $ex_broker_;



                                 ?></td>
                            </tr>

                            <tr>
                                <th>Invoice No.</th>
                                <td><?php echo $row['invoice_no'] ?></td>
                            </tr>

                            <tr>
                                <th>No Of Bales</th>
                                <td><?php echo $row['bales'] ?></td>
                            </tr>

                            <tr>
                                <th>Gross Amount</th>
                                <td><?php echo $row['grs_amt'] ?></td>
                            </tr>
                            <tr>
                                <th>Tax(%)</th>
                                <td><?php echo $row['txn'] ?></td>
                            </tr>
                            <tr>
                                <th>Tax Amount</th>
                                <td><?php echo $row['txn_amount'] ?></td>
                            </tr>
                            <tr>
                                <th>TCS (%)</th>
                                <td><?php echo $row['tcs'] ?></td>
                            </tr>
                            <tr>
                                <th>TCS Amount</th>
                                <td><?php echo $row['tcs_amount'] ?></td>
                            </tr>
                            <tr>
                                <th>Other Amount</th>
                                <td><?php echo $row['other_amt'] ?></td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td class="bold"><?php echo $row['netpayableamt'] ?></td>
                            </tr>

                          



                        </div>
                </table>
                </div> 
      <div class="row">

<?php
  if($row['doc_file'] != ''){
   $prev = explode(',',$row['doc_file']);
  $prev_img_title = explode(',',$row['img_title']);
  foreach ($prev as $key => $imging) {
    $attend =  $dir.$imging;

    if($attend)
        {
        $attendExt = strtolower(pathinfo($attend, PATHINFO_EXTENSION));

        $attend_allowExt  = array('jpeg', 'jpg', 'png', 'gif');


        if(in_array($attendExt, $attend_allowExt)) 
        {

          ?>
                    <div class="form-group col-md-4 field-show-image">
                      <div class="image-upload">  
                        <div class="label">
                        <h6 class="title">Document File <?= $key+1 ?></h6>
                      
                      </div>
                      <div class="filed-form-control">  
                      <img src="<?php echo $dir.$imging ?>"  data-toggle="modal" data-target="#myModal" id="1" onerror="this.onerror=null; this.src='../../image/no-image.jpg'" height="300" width="300">
                        </div>
                      <div class="text-center mt-3"> (Click Image to Open)</div>

           
                      </div>
                      <br>
<input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                    </div>

          <?php
         
        }
        else
        {
          ?>
           <div class="form-group col-md-4 field-show-image pl-0">
                      <div class="image-upload">  
                        <div class="label">
                        <h6 class="title">Document File <?= $key+1 ?></h6>
                      
                      </div>
                      <div class="filed-form-control">  
             <img src="<?php echo $dir.$imging ?>"   id="1" onerror="this.onerror=null; this.src='../../image/no-prev.jpg'" height="300" width="300">
          <a href="<?php echo $dir.$imging ?>" class="btn btn-success btn-lg" target="_blank">Download File</a>

           
                      </div>
                      <br>
                      <input type="text" class="form-control" placeholder="Enter Image Title" name="img_title[]" value="<?php echo $prev_img_title[$key]; ?>" readonly>
                    </div>
                  </div>
          <?php
        }

      }
    }
    }

?>                        
</div>

<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <img class="img-responsive" src="" width="600" height="600" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>




            </div>
            </div>
          </div>
        </div>

      </div>
</div>
</div>

<script type="text/javascript">
  $(document).ready(function () {
        $('#myModal').on('show.bs.modal', function (e) {
            var image = $(e.relatedTarget).attr('src');
            $(".img-responsive").attr("src", image);
        });
        });
</script>
   
  

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>

    </body>
  </html>
