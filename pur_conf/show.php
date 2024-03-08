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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "select * from pur_conf p, party f where p.firm=f.id AND p.id='".$id."'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) 
      {
      $row = mysqli_fetch_assoc($result);
      }
    else 
      {
      $errorMsg = 'Could not Find Any Record';
      }

      
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Product Confirmation Database Details </title>
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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Product Confirmation Database Details</span></a>
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
         $sqlLastChange="select username,updated_at from pur_conf where id='".$id."'";

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
               
                <div class="col-md">
                  <h5 class="form-control">
                      <span class="title">Confirmation No. : </span>
                      <span><?php echo $row['pur_conf'] ?></span>
                    </h5>

                       <h5 class="form-control">
                      <span class="title">Confirmation Type. : </span>
                      <?php $conf_type=["Original","Revised","Cancel"]; ?>
                      <span><?php echo $conf_type[$row['conf_type']]; ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">External Party Name : </span>
                      <span>
                        <?php 

                          $party = "select * from external_party where id='".$row['party']."'";
                          $partyresult = mysqli_query($conn, $party);

                          $partyrow = mysqli_fetch_assoc($partyresult);

                          $ex_party='';
                          if(isset($partyrow))
                          {
                            $ex_party=$partyrow['partyname'];
                          }
                          echo $ex_party;

                        ?></span>
                    </h5>
                    <h5 class="form-control">
                      <span class="title">Firm Name : </span>
                      <span><?php 

                        $sql4 = "select * from party where id='".$row['firm']."'";
                            $result4 = mysqli_query($conn, $sql4);

                            $row10 = mysqli_fetch_assoc($result4);
                            // print_r($row10);
                            $pname='';
                            if(isset($row10))
                            {
                              $pname=$row10['party_name'];
                            }
                      echo $pname; ?></span>
                    </h5>

                    <?php
                    $ReportDate='';
                    if($row['pur_report_date']!='' && $row['pur_report_date']!='0000-00-00')
                    {
                    $ReportDate=date("d/m/Y", strtotime($row['pur_report_date']));
                    }
                    ?>

                    <h5 class="form-control">
                      <span class="title">Report Date : </span>
                      <span><?php echo $ReportDate; ?></span>
                    </h5>

                    <?php 

                      $financial_year = "select * from financial_year where id='".$row['financial_year']."'";
                          $financial_year_result = mysqli_query($conn, $financial_year);

                          $financial_year_row = mysqli_fetch_assoc($financial_year_result);

                          $financial_year_ids = '';
                          if(isset($financial_year_row))
                          {
                            $syear=date("Y", strtotime($financial_year_row['startdate']));
                            $eyear=date("Y", strtotime($financial_year_row['enddate']));
                          }
                          $financial_year_ids = $syear.'-'.$eyear;
                          ?>

                    <h5 class="form-control">
                      <span class="title">Financial Year : </span>
                      <span><?php echo $financial_year_ids; ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Bales : </span>
                      <span><?php echo $row['bales'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Broker : </span>
                      <span><?php 

                        $broker = "select * from broker where id='".$row['broker']."'";
                          $broker_result = mysqli_query($conn, $broker);

                          $broker_row = mysqli_fetch_assoc($broker_result);

                          $broker_ids='';
                          if(isset($broker_row))
                          {
                            $broker_ids=$broker_row['name'];
                          }
                          echo $broker_ids;?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Transnport Name : </span>
                      <span><?php 
                      $transport = "select * from transport where id='".$row['trans_name']."'";
                          $transport_result = mysqli_query($conn, $transport);

                          $transport_row = mysqli_fetch_assoc($transport_result);

                          $transport_ids='';
                          if(isset($transport_row))
                          {
                            $transport_ids=$transport_row['trans_name'];
                          }
                          echo $transport_ids; ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Payment Type : </span>
                      <span><?php 
                      
                        if($row['trans_pay_type']=='to_be_pay')
                        {
                          echo "To Be Pay";
                        }
                        else if($row['trans_pay_type']=='to_be_build')
                        {
                          echo "To Be Build";
                        }


                       ?></span>
                    </h5>




                    <h5 class="form-control">
                      <span class="title">Product : </span>
                      <span><?php 

                          $products = "select * from products where id='".$row['product_name']."'";
                          $products_result = mysqli_query($conn, $products);

                          $products_row = mysqli_fetch_assoc($products_result);

                          $products_ids='';
                          if(isset($products_row))
                          {
                            $products_ids=$products_row['prod_name'];
                          }
                          echo $products_ids; ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Station : </span>
                      <span><?php echo $row['station'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Delivery Date : </span>
                      <span><?php echo $row['delivery_date'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Product Length : </span>
                      <span><?php echo $row['pro_length'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Product MIC : </span>
                      <span><?php echo $row['pro_mic'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Product RD : </span>
                      <span><?php echo $row['pro_rd'] ?></span>
                    </h5>

                     <h5 class="form-control">
                      <span class="title">Product Trash : </span>
                      <span><?php echo $row['pro_trash'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Product Moisture : </span>
                      <span><?php echo $row['pro_mois'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Candy Rate : </span>
                      <span><?php echo $row['candy_rate'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Dispatch : </span>
                      <span><?php echo $row['dispatch'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">No. Of Vehicle : </span>
                      <span><?php echo $row['no_of_vehicle'] ?></span>
                    </h5>

                  

                    <h5 class="form-control">
                      <span class="title">Vehicle No. : </span>
                      <span>
                        <?php 

                        if($row['vehicle_no']!='' || $row['vehicle_no']!=null)
                        {
                           $veh_no=json_decode($row['vehicle_no']);
                            if(count($veh_no)>0)
                            {
                              echo implode(", ",$veh_no);
                            }
                        }
                       
                       ?> 
                       </span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Insaurance Company : </span>
                      <span><?php echo $row['ins_cmpny'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Insaurance Policy No : </span>
                      <span><?php echo $row['ins_policy_no'] ?></span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Payment Term : </span>
                      <span><?php echo $row['pay_term'] ?></span>
                    </h5>

                                                        
                    <h5 class="form-control">
                      <span class="title">Laboratory Master: </span>
                      <span>
                      <?php 
                        $lsql = "select * from laboratory_master where id='".$row['laboratory_master']."'";
                        $lresult = mysqli_query($conn, $lsql);
                        if (mysqli_num_rows($result) > 0) 
                          {                            
                            $lrow = mysqli_fetch_assoc($lresult);                            
                            echo $lrow['lab_name'];
                          }
                      ?>
                    </span>
                    </h5>

                    <h5 class="form-control">
                      <span class="title">Special Remark : </span>
                      <span><?php echo $row['spl_rmrk'] ?></span>
                    </h5>


                    <h5 class="form-control">
                      <span class="title">Terms & Condition : </span>
                      <span><?php echo $row['term_condtion'] ?></span>
                    </h5>
                </div>

              </div>
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
    </body>
  </html>
