<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: ../login.php");
  exit;
}

function getBrokerName($id)
{
    $arr=array();
    include('../db.php');
    $party = "select * from broker where id='".$id."'";
    $partyresult = mysqli_query($conn, $party);

    $partyrow = mysqli_fetch_array($partyresult);

   $name=$partyrow['name'];
    return $name;
}


if(isset($_POST['clearFilter']))
{
  header("location:index.php");
}

  
if(isset($_POST['submit'])){

      $row_arr  = array();
     
      $main_query="select * from rd_kapas_report where";


      $start_date='';
      $end_date='';
      $where_cond = array();
      if($_POST['start_date']!='' && $_POST['end_date']=='')
      {
        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $where_cond[] = " report_date>='".$start_date."'";
      }

      if($_POST['start_date']=='' && $_POST['end_date']!='')
      {
        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

        $where_cond[] = " report_date<='".$end_date."'";
      }

     
      if($_POST['start_date']!='' && $_POST['end_date']!='')
      {

        $start_date = str_replace('/', '-', $_POST['start_date']);
        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = str_replace('/', '-', $_POST['end_date']);
        $end_date = date('Y-m-d', strtotime($end_date));

      
        $where_cond[] = " report_date>='".$start_date."' AND report_date<='".$end_date."'";

      }

      if(isset($_POST['firm']))
      {
        $firm=implode(",",$_POST['firm']);

        $where_cond[] = " firm in (".$firm.")";
        
      }

      if(isset($_POST['ext_party']))
      {
        $ext_party="'".implode("','",$_POST['ext_party'])."'";
        $where_cond[] = " external_party in (".$ext_party.")";
      }

      if(isset($_POST['broker']))
      {
        $broker="'".implode("','",$_POST['broker'])."'";
        $where_cond[] = " broker in (".$broker.")";
      }

      
      if(!empty($where_cond)){
        $where = implode('AND',$where_cond);
        $main_query = $main_query.$where.' order by report_date DESC';
      }else{

        $main_query="select * from rd_kapas_report order by report_date DESC";
      }


       
      $row_arr  = array();
       $result2 = mysqli_query($conn, $main_query);


       while($value = mysqli_fetch_assoc($result2)){

         $row_arr[] = $value;
       
       }
  


       $dataArr=array();
       $i=0;
       foreach ($row_arr as $key => $value) 
       {

        $dataArr[$i]['sr_no']=$i+1;

        //firm
        $firm_name='';
        $sql_firm = "select * from party where id='".$value['firm']."'";
        $result_firm = mysqli_query($conn, $sql_firm);
        $row_firm=mysqli_fetch_array($result_firm);

        if(mysqli_num_rows($result_firm)>0)
        {
           $firm_name=$row_firm['party_name'];
        }


        $dataArr[$i]['firm']=$firm_name;


        //external party
          $ex_party='';

          $exSQL = "select * from external_party where id='".$value['external_party']."'";
          $exResult = mysqli_query($conn, $exSQL);
          $exRow = mysqli_fetch_assoc($exResult);
          
          if(mysqli_num_rows($exResult)>0)
          {
            $ex_party=$exRow['partyname'];
          }

          $dataArr[$i]['ex_party']=$ex_party;

          $dataArr[$i]['broker']=getBrokerName($value['broker']);


          //bill date
          $bill_date='';
          if($value['report_date']!='' && $value['report_date']!='0000-00-00')
          {
            $bill_date = str_replace('-', '/', $value['report_date']);
            $bill_date = date('d/m/Y', strtotime($bill_date));
          }
          $dataArr[$i]['bill_date']=$bill_date;


          $dataArr[$i]['invoice_no']=$value['invoice_no'];

          $dataArr[$i]['basic_amt']=$value['basic_amt'];

          $dataArr[$i]['tax_amt']=$value['tax_amt'];

          $dataArr[$i]['tcs_amt']=$value['tcs_amt'];

          $dataArr[$i]['total_amt']=$value['net_amt'];


          $debit_amt='';
          $tds_amt='';
          $pay_date='';
          $pay_amt='';
          $outstanding_amt=$value['net_amt'];


          //get data from RD purchase Payment
          $SQL2="select * from rd_kapas_payment where rd_kapas_report_id='".$value['id']."'";
          $result2 = mysqli_query($conn, $SQL2);
          if(mysqli_num_rows($result2)>0)
          {
            $row2=mysqli_fetch_assoc($result2);

            $debit_amt=$row2['debit_amt'];
            $tds_amt=$row2['party_tds_amt'];
            $outstanding_amt=$row2['pay_amt'];

             $dataArr[$i]['debit_amt']=$debit_amt;
             $dataArr[$i]['tds_amt']=$tds_amt;
             $dataArr[$i]['outstanding_amt']=$outstanding_amt;

            $payArr=json_decode($row2['dynamic_field'],true);
            $bill2billArr=json_decode($row2['bill2bill_dynamic_data'],true);


            //merge bill 2 bill  & dynamic amount in one array
            $dynamicArr=array();
            $d=0;
            if(count($payArr)>0)
            {
              foreach ($payArr as $key => $item) 
              {
                if($item['amt']!='')
                {
                  $dynamicArr[$d]['label']=$item['lable'];
                  $dynamicArr[$d]['amt']=$item['amt'];
                  $dynamicArr[$d]['date']=$item['date'];
                  $d++;
                }
                
              }
            }



            if(count($bill2billArr)>0)
            {
              foreach ($bill2billArr as $key => $item) 
              {
                if($item['b2b_amount']!='')
                {
                  $dynamicArr[$d]['label']=$item['b2b_label'];
                  $dynamicArr[$d]['amt']=$item['b2b_amount'];
                  $dynamicArr[$d]['date']=$item['b2b_date'];
                  $d++;
                }
                
              }
            }



            if(count($dynamicArr)>0)
            {
              foreach ($dynamicArr as $key2 => $item) 
              {
                if($key2!=0)
                {
                    $dataArr[$i]['sr_no']='';
                    $dataArr[$i]['firm']='';
                    $dataArr[$i]['ex_party']='';
                    $dataArr[$i]['broker']='';
                    $dataArr[$i]['bill_date']='';
                    $dataArr[$i]['invoice_no']='';
                    $dataArr[$i]['basic_amt']='';
                    $dataArr[$i]['tax_amt']='';
                    $dataArr[$i]['tcs_amt']='';
                    $dataArr[$i]['total_amt']='';
                    $dataArr[$i]['debit_amt']='';
                    $dataArr[$i]['tds_amt']='';
                    $dataArr[$i]['outstanding_amt']='';
                }

               

                //pay_date
                $pay_date='';
                if($item['date']!='' && $item['date']!='0000-00-00')
                {
                  $pay_date = str_replace('-', '/', $item['date']);
                  $pay_date = date('d/m/Y', strtotime($pay_date));
                }

                $dataArr[$i]['pay_date']=$pay_date;
                $dataArr[$i]['pay_amt']=$item['amt'];



                $i++;
              }
            }
            
          }
          else
            {
                $dataArr[$i]['pay_date']=$pay_date;
                $dataArr[$i]['pay_amt']=$pay_amt;
                $dataArr[$i]['debit_amt']=$debit_amt;
                $dataArr[$i]['tds_amt']=$tds_amt;
                $dataArr[$i]['outstanding_amt']=$outstanding_amt;

            }

          $i++;
 
         
       }

       $_SESSION['rd_kapas_export_data']=$dataArr;


}
     

      
      
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>RD Kapas Report</title>
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

       $(".datepicker2").datepicker({
        dateFormat:'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
      }).datepicker('setDate', 'today');

       $(".datepicker").keydown(false);
       $(".datepicker2").keydown(false);

       $('.searchDropdown').selectpicker();

      

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
          <a class="navbar-brand" href="index.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> RD Kapas Report</span></a>
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
                    <div class="row">


                         
                          <div class="form-group col-md-4">
                            <label for="firm">Select Firm</label>
                                <?php
                                    $sql = "select * from party";
                                    $result = mysqli_query($conn, $sql);
                                ?>                      
                        <select name="firm[]" class="form-control searchDropdown" multiple title="Select Firm"> 
                              
                            
                            <?php                   
                                foreach ($conn->query($sql) as $result) 
                                {
                                  
                                    if(isset($_POST['firm']) && in_array($result['id'], $_POST['firm']))
                                   {
                                        echo "<option  value='".$result['id']."' selected>".$result['party_name']. "</option>";
                                   }
                                   else
                                   {
                                        echo "<option  value='".$result['id']."'>".$result['party_name']. "</option>";
                                   }
                                }
                            ?>                              
                            </select>
                        </div>

                          <div class="form-group col-md-4">
                              <label for="party">Select External Party</label>
                              <?php
                                $sql = "select * from external_party";
                                $result = mysqli_query($conn, $sql);
                              ?>                      
                              <select name="ext_party[]" class="form-control searchDropdown" data-live-search="true" title="Select Option" multiple>
                                
                                <?php                   
                                  foreach ($conn->query($sql) as $result) 
                                  {
                                    if(isset($_POST['ext_party']) && in_array($result['id'], $_POST['ext_party']))
                                     {
                                          echo "<option  value='".$result['id']."' selected>" .$result['partyname']. "</option>";
                                     }
                                     else
                                     {
                                          echo "<option  value='".$result['id']."'>" .$result['partyname']. "</option>";
                                     }
                                  }
                                ?>                              
                              </select>
                            </div>

                      <div class="form-group col-md-4">
                          <label for="broker">Select Broker</label>
                          <?php
                            $Broker_sql = "select * from broker";
                            $Broker_result = mysqli_query($conn, $Broker_sql);                            
                          ?>                      
                          <select name="broker[]" class="form-control searchDropdown" multiple data-live-search="true" title="Select Option">
                            <?php                   
                              foreach ($conn->query($Broker_sql) as $Broker_result) 
                              {
                                 if(isset($_POST['broker']) && in_array($Broker_result['id'], $_POST['broker']))
                                 {
                                     echo "<option  value='".$Broker_result['id']."' selected>" .$Broker_result['name']. "</option>";
                                 }
                                 else
                                 {
                                     echo "<option  value='".$Broker_result['id']."'>" .$Broker_result['name']. "</option>";
                                 }
                              }
                            ?>                              
                          </select>
                        </div>





                             <div class="form-group col-md-4">
                              <label for="start_date">Start Date :</label>
                                <input type="text" class="form-control datepicker" name="start_date"  placeholder="Select Start Date" value="<?php if(isset($_POST['start_date'])){echo $_POST['start_date'];} ?>" autocomplete="off">
                              </div>

                            <div class="form-group col-md-4">
                                <label for="end_date">End Date :</label>
                                  <input type="text" class="form-control datepicker" name="end_date"  placeholder="Select End Date" value="<?php if(isset($_POST['end_date'])){echo $_POST['end_date'];} ?>" autocomplete="off">
                              </div>
                            </div>

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
            

         
                <div class="card mt-4">
                   
                      <div class="card-body export-data">

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
                      <table id="example" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Firm Name</th>
                                <th>Party Name</th>
                                <th>Broker</th>
                                <th>Bill Date</th>
                                <th>Invoice No</th>
                                <th>Basic Amount</th>
                                <th>Tax Amount</th>
                                <th>TCS Amount</th>
                                <th>Total Amount</th>
                                <th>Debit Note Amount</th>
                                <th>TDS Amount</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Outstanding</th>

                                </tr>
                        </thead>
                        <tfoot>
                          
                          <tr>
                              <th>ID</th>
                                <th>Firm Name</th>
                                <th>Party Name</th>
                                <th>Broker</th>
                                <th>Bill Date</th>
                                <th>Invoice No</th>
                                <th>Basic Amount</th>
                                <th>Tax Amount</th>
                                <th>TCS Amount</th>
                                <th>Total Amount</th>
                                <th>Debit Note Amount</th>
                                <th>TDS Amount</th>
                                <th>Payment Date</th>
                                <th>Payment Amount</th>
                                <th>Outstanding</th>
                          </tr>


                        </tfoot>
                        <tbody>
                          <?php 

                          if (isset($_POST['submit'])) {


                            if (count($dataArr)>0) {
                              $i=1;
                       
                            foreach ($dataArr as $key => $value) {
                               
                              
                            ?>
                          

                          <tr>
                            <td>
                              <?php
                              if($value['firm']!='')
                              {
                                 echo $i++;
                              }
                            ?>
                              
                            </td>

                            
                            <td><?php echo $value['firm']; ?></td>

                            <td><?php echo $value['ex_party']; ?></td>

                            <td><?php echo $value['broker']; ?></td>

                            <td><?php echo $value['bill_date']; ?></td>

                            <td><?php echo $value['invoice_no']; ?></td>

                            <td><?php echo $value['basic_amt']; ?></td>

                            <td><?php echo $value['tax_amt']; ?></td>

                            <td><?php echo $value['tcs_amt']; ?></td>

                            <td><?php echo $value['total_amt']; ?></td>

                            <td><?php echo $value['debit_amt']; ?></td>

                            <td><?php echo $value['tds_amt']; ?></td>

                            <td><?php echo $value['pay_date']; ?></td>

                            <td><?php echo $value['pay_amt']; ?></td>

                            <td><?php echo $value['outstanding_amt']; ?></td>

                          
                           
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


          <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>


    <script type="text/javascript">

    $(document).ready(function() {

      
       




      } );
    </script>
  </body>
</html>