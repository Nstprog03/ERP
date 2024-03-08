<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

if(!isset($_SESSION["bank_transaction_firm_id"]) && !isset($_SESSION["bank_transaction_financial_year_id"])){
    header("location:../bank_transation.php");
    exit;
}

if(!isset($_SESSION['bank'])){
  header("location:index.php");
  exit;
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $sql = "select * from bank_balance where id = ".$id;
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0){
        $row2 = mysqli_fetch_assoc($result);
        if(isset($row2)){
            $sql2  = "SELECT * FROM `bank_balance` WHERE date >= '" . $row2['date'] . "' AND `bank` = '" . $row2['bank'] . "' AND `firm` = '" . $row2['firm'] . "' AND `financial_year` = '" . $row2['financial_year'] . "' ORDER BY  `date` ASC,`id` ASC";
            $result2 = mysqli_query($conn,$sql2);
            if(mysqli_num_rows($result2) > 0){
                while($row3 = mysqli_fetch_assoc($result2)){
                    if($row2['date'] == $row3['date'] && $row2['id'] >= $row3['id']){
                    }else{
                        if($row2['bank_peyout'] == 0){
                            $pre_bal = $row3['previous_balance'] - $row2['balance'];
                            $total_bal = $row3['total_balance'] - $row2['balance'];
                        }else{
                            $total_bal = $row3['total_balance'] + $row2['payment'];
                            $pre_bal = $row3['previous_balance'] + $row2['payment'];
                        }
                        $sql3        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance` = '".$total_bal."' WHERE id = '" . $row3['id'] . "'";
                        $result3 = mysqli_query($conn, $sql3);
                    }
                }
            }

            $sql = "delete from bank_balance where id=".$id;
            if(mysqli_query($conn, $sql)){
                $page=1;
                if(isset($_GET['page']))
                {
                $page=$_GET['page'];
                }
                header("Location: home.php?page=$page");
            }
        }
    }
}

    if(isset($_GET['deletes'])){
		$id = $_GET['delete'];
		$sql = "select * from bank_balance where id = ".$id;
		$result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0){
          $row2 = mysqli_fetch_assoc($result);
          if(isset($row2)){
    
            $sql2  = "SELECT * FROM `bank_balance` WHERE date >= '" . $row2['date'] . "' AND `bank` = '" . $row2['bank'] . "' AND `firm` = '" . $row2['firm'] . "' AND `financial_year` = '" . $row2['financial_year'] . "' ORDER BY  `date` ASC,`id` ASC";
            $result2 = mysqli_query($conn,$sql2);
            if(mysqli_num_rows($result2) > 0){
              while($row3 = mysqli_fetch_assoc($result2)){
                if($row2['date'] == $row3['date'] && $row2['id'] >= $row3['id']){
                }else{
                  if($row2['bank_peyout'] == 0){
                    $pre_bal = $row3['previous_balance'] - $row2['balance'];
                    $total_bal = $row3['total_balance'] - $row2['balance'];
                  }else{
                    $total_bal = $row3['total_balance'] - $row2['payment'];
                    $pre_bal = $row3['previous_balance'] - $row2['payment'];
                  }
                  $sql3        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance` = '".$total_bal."' WHERE id = '" . $row3['id'] . "'";
                  $result3 = mysqli_query($conn, $sql3);
                }
              }
            }
    
            $sql = "delete from bank_balance where id=".$id;
            if(mysqli_query($conn, $sql)){
              $page=1;
              if(isset($_GET['page']))
              {
                $page=$_GET['page'];
              }
              header("Location: home.php?page=$page");
            }
          }
        }
	}

  if(isset($_POST['clearFilter']))
{
  unset ($_SESSION["balance_filter_data"]);
  unset ($_SESSION["balance_filter_selected"]);
  header("location:home.php");
}
$isFilter    = false;
$selectedArr = array();
if (isset($_POST['filter']) || isset($_SESSION['balance_filter_data'])) {
    
    $isFilter = true;
    
    $main_query = "select bb.* from bank_balance bb ";
    $where_cond = array();
    
    if (isset($_POST['filter'])) {
      $join = "";
      if(isset($_POST['table']))
      {
          $table = implode("','",$_POST['table']);
          $join = " LEFT JOIN bank_transaction bt ON bt.id = bb.bank_peyout LEFT JOIN bank_receipt br ON br.id = bb.bank_receipt ";
          $where_cond[] = " (br.table_indicator IN ('".$table."') OR bt.table_indicator IN ('".$table."')) ";
      }

      if(isset($_POST['party']))
      {
          $party = implode(",",$_POST['party']);
          $join = " LEFT JOIN bank_transaction bt ON bt.id = bb.bank_peyout LEFT JOIN bank_receipt br ON br.id = bb.bank_receipt ";
          $where_cond[] = " (br.party IN (".$party.") OR bt.ext_party IN (".$party.")) ";
      }
        
        $start_date = '';
        $end_date   = '';
        if ($_POST['start_date'] != '' && $_POST['end_date'] == '') {
            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));
            $where_cond[] = " bb.date>='" . $start_date . "'";
        }
        
        if ($_POST['start_date'] == '' && $_POST['end_date'] != '') {
            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));
            
            $where_cond[] = " bb.date<='" . $end_date . "'";
        }
        
        
        if ($_POST['start_date'] != '' && $_POST['end_date'] != '') {
            
            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));
            
            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));
            
            $where_cond[] = " bb.date>='" . $start_date . "' AND bb.date<='" . $end_date . "'";
        }
        
        $selectedArr                        = $_POST;
        $_SESSION['balance_filter_selected'] = $selectedArr;
        $_SESSION['balance_filter_data']     = $where_cond;
    } else {
        $where_cond  = $_SESSION['balance_filter_data'];
        $selectedArr = $_SESSION['balance_filter_selected'];
    }
  }
    
    $i = 0;
    
    if (!empty($where_cond)) {
        
        $where      = implode(' AND ', $where_cond);
        $main_query = $main_query .$join." WHERE ". $where . " AND  bb.bank = '".$_SESSION['bank']."' AND bb.firm = '".$_SESSION["bank_transaction_firm_id"]."' AND bb.financial_year = '".$_SESSION["bank_transaction_financial_year_id"]."' ORDER BY bb.date DESC,bb.id DESC";
        $result = mysqli_query($conn, $main_query);
        
    } else {
      //pagination  ------------------
      $per_page_record = 10;         
      if (isset($_GET["page"])) 
      {    
          $page  = $_GET["page"];    
      }    
      else 
      {    
        $page=1;    
      }  

      //id auto increment
      $i=0;
      $i=($page*10)-10;   

      $start_from = ($page-1) * $per_page_record;  
      $sql = "SELECT * FROM bank_balance WHERE bank = '".$_SESSION['bank']."' AND firm = '".$_SESSION["bank_transaction_firm_id"]."' AND financial_year = '".$_SESSION["bank_transaction_financial_year_id"]."' ORDER BY date DESC , id DESC LIMIT $start_from, $per_page_record";     
      $result = mysqli_query($conn, $sql);
	
  }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bank Balance List</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
        integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js"
        integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous">
    </script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js"
        integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous">
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>

    <link href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
    <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script>
    $(function() {
        $("#sidebarnav").load("../nav.html");
        $("#topnav").load("../nav2.html");

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true
        });
        $(".datepicker").keydown(false);

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
                    <a class="navbar-brand" href="home.php"><span class="page-name-top"><span
                                class="icon-report_dashboard"></span> Bank Balance Database</span></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto"></ul>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item mr-2">
                                <button type="button" name="viewfilter" id="viewfilter"
                                    class="btn btn-outline-primary"><i class="fa fa-filter"></i><span class="pl-1">View
                                        Filter</span></button>

                                <script type="text/javascript">
                                $(document).ready(function() {
                                    $(".viewfilter").hide();
                                    var check = false;
                                    var checkPageLoad = "<?php echo $isFilter ; ?>";
                                    if (checkPageLoad == true) {
                                        $(".viewfilter").show();
                                        check = true;
                                    }
                                    $("#viewfilter").click(function() {});
                                    $("#viewfilter").click(function() {
                                        if (check == false) {
                                            $(".viewfilter").show();
                                            check = true;
                                        } else {
                                            $(".viewfilter").hide();
                                            check = false;
                                        }
                                    });
                                });
                                </script>

                            </li>
                            <li class="nav-item mr-2">
                                <form action="../bank_transation.php" method="post">
                                    <input type="hidden" name="financial_year"
                                        value="<?php echo $_SESSION['bank_transaction_financial_year']; ?>">
                                    <input type="hidden" name="firm"
                                        value="<?php echo $_SESSION['bank_transaction_firm'].'/'.$_SESSION['bank_transaction_firm_id']; ?>">

                                    <button type="submit" name="submit" class="btn btn-outline-danger"><i
                                            class="fa fa-sign-out-alt"></i><span class="pl-1">Back</span></button>
                                </form>
                            </li>
                            <li class="nav-item mr-2"><a class="btn btn-outline-secondary"
                                    href="../bank_transation.php"><i class="fa fa-undo-alt"></i> Pre-Selection</a></li>
                            <li class="nav-item mr-2"><a title="Excel Sheet" class="btn btn-success" id="viewExcel"><i
                                        class="fa fa-file-excel"></i></a></li>
                            <script type="text/javascript">
                            $(document).ready(function() {
                                $(".viewExcel").hide();
                                var check = false;

                                $("#viewExcel").click(function() {
                                    if (check == false) {
                                        $("#bank").prop('selectedIndex', 0);
                                        $("select[name=party]").prop('selectedIndex', 0);
                                        $(".viewExcel").show();
                                        check = true;
                                    } else {
                                        $(".viewExcel").hide();
                                        check = false;
                                    }
                                });
                            });
                            </script>
                            <li class="nav-item"><a class="btn btn-primary" href="create.php"><i
                                        class="fa fa-user-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- last change on table START-->
            <div class="last-updates">
                <div class="firm-selectio">
                    <div class="firm-selection-pre">
                        <span class="pre-firm">Firm : </span><span
                            class="pre-firm-name"><?php echo $_SESSION["bank_transaction_firm"]; ?></span>
                    </div>
                    <div class="year-selection-pre">
                        <span class="pre-year-text">Financial Year :</span>
                        <span class="pre-year">
                            <?php 

              $finYearArr=explode('/',$_SESSION["bank_transaction_financial_year"]);

              $start_date=date('Y', strtotime($finYearArr[0]));
               $end_date=date('Y', strtotime($finYearArr[1]));

              echo $start_date.' - '.$end_date; 
              ?>
                        </span>
                    </div>
                </div>
                <div class="last-edits-fl">
                    <?php
           $sqlLastChange="select username,updated_at from bank_balance where
          financial_year='".$_SESSION['bank_transaction_financial_year_id']."' 
          AND firm='".$_SESSION['bank_transaction_firm_id']."' order by updated_at DESC LIMIT 1";

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
                    <!-- Excel Section Start -->
                    <div class="card viewExcel">
                        <div class="card-header">Generate Excel</div>
                        <div class="card-body">
                            <form action="generate_excel.php" method="post" enctype="multipart/form-data">
                                <div class="row">

                                    <div class="form-group col-md-3">
                                        <label for="start_date">Select Start Date :</label>
                                        <input class="form-control datepicker" type="text" id="start_date"
                                            name="start_date" autocomplete="off" placeholder="Start Date"
                                            value="<?php if(isset($_SESSION['purpay_filter_selected']['start_date'])){ echo $_SESSION['purpay_filter_selected']['start_date']; } ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="end_date">Select End Date :</label>
                                        <input id="end_date" class="form-control datepicker" type="text" name="end_date"
                                            autocomplete="off" placeholder="End Date"
                                            value="<?php if(isset($_SESSION['purpay_filter_selected']['end_date'])){ echo $_SESSION['purpay_filter_selected']['end_date']; } ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="table">Select Table</label>
                                        <select name="table[]" class="form-control searchDropdown" title="Select Option"
                                            multiple="">
                                            <option value="Sales Recievable">Sales Recievable</option>
                                            <option value="Kapasiya Sales">Kapasiya Sales</option>
                                            <option vlaue="Debit Note Ad-Hoc">Debit Note Ad-Hoc</option>
                                            <option vlaue="Bales Payout">Bales Payout</option>
                                            <option vlaue="Transport Payout">Transport Payout</option>
                                            <option vlaue="RD Kapas purchase Payment">RD Kapas purchase Payment</option>
                                            <option vlaue="URD Kapas purchase Payment">URD Kapas purchase Payment
                                            </option>
                                            <option vlaue="Other Payout">Other Payout</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="party[]">Select External Party</label>
                                        <?php
                                          $ext_Party = "select * from external_party";
                                          $ext_Partyresult = mysqli_query($conn, $ext_Party);
                                        ?>
                                        <select name="party[]" data-live-search="true"
                                            class="form-control searchDropdown" title="Select" multiple="">

                                            <?php                   
                                              foreach ($conn->query($ext_Party) as $ext_Partyresult) 
                                              {
                                                echo "<option  value='".$ext_Partyresult['id']."'>" .$ext_Partyresult['partyname']. "</option>"; 
                                              }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-1">
                                        <button type="submit" name="submit"
                                            class="btn btn-primary waves">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Excel Section End -->

                    <!-- Filter Section Start -->
                    <div class="card viewfilter m-3">
                        <div class="card-header">Filter</div>
                        <div class="card-body">
                          <!-- <?php 
                          // echo "<pre>";
                          // print_r($_SESSION['balance_filter_selected']);
                          // echo "</pre>";
                          ?> -->

                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="start_date">Select Start Date :</label>
                                        <input class="form-control datepicker" type="text" autocomplete="off"
                                            name="start_date" placeholder="Start Date"
                                            value="<?php if(isset($_SESSION['balance_filter_selected']['start_date'])){ echo $_SESSION['balance_filter_selected']['start_date'];} ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="end_date">Select End Date :</label>
                                        <input  class="form-control datepicker" type="text" name="end_date"
                                            autocomplete="off" placeholder="End Date"
                                            value="<?php if(isset($_SESSION['balance_filter_selected']['end_date'])){ echo $_SESSION['balance_filter_selected']['end_date'];} ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="table">Select Table</label>
                                        <select name="table[]" class="form-control searchDropdown" title="Select Option"
                                            multiple="">
                                            <option <?php if(isset($selectedArr['table']) && in_array("Sales Recievable", $selectedArr['table'])){ echo "selected";} ?> value="Sales Recievable">Sales Recievable</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("Kapasiya Sales", $selectedArr['table'])){ echo "selected";} ?> value="Kapasiya Sales">Kapasiya Sales</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("Debit Note Ad-Hoc", $selectedArr['table'])){ echo "selected";} ?> vlaue="Debit Note Ad-Hoc">Debit Note Ad-Hoc</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("Bales Payout", $selectedArr['table'])){ echo "selected";} ?> vlaue="Bales Payout">Bales Payout</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("Transport Payout", $selectedArr['table'])){ echo "selected";} ?> vlaue="Transport Payout">Transport Payout</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("RD Kapas purchase Payment", $selectedArr['table'])){ echo "selected";} ?> vlaue="RD Kapas purchase Payment">RD Kapas purchase Payment</option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("URD Kapas purchase Payment", $selectedArr['table'])){ echo "selected";} ?> vlaue="URD Kapas purchase Payment">URD Kapas purchase Payment
                                            </option>
                                            <option <?php if(isset($selectedArr['table']) && in_array("Other Payout", $selectedArr['table'])){ echo "selected";} ?> vlaue="Other Payout">Other Payout</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="party[]">Select External Party</label>
                                        <?php
                        $ext_Party = "select * from external_party";
                        $ext_Partyresult = mysqli_query($conn, $ext_Party);
                      ?>
                                        <select name="party[]" data-live-search="true"
                                            class="form-control searchDropdown" title="Select" multiple="">

                                            <?php                   
                          foreach ($conn->query($ext_Party) as $ext_Partyresult) 
                          {
                            if(isset($selectedArr['party']) && in_array($ext_Partyresult['id'], $selectedArr['party'])){
                              echo "<option selected  value='".$ext_Partyresult['id']."'>" .$ext_Partyresult['partyname']. "</option>"; 
                            }else{
                              echo "<option  value='".$ext_Partyresult['id']."'>" .$ext_Partyresult['partyname']. "</option>"; 
                            }
                            
                          }
                        ?>
                                        </select>
                                    </div>
                                </div>

                        </div>

                        <div class="row mt-3">

                            <div class="form-group col-md-1 ml-4">
                                <button type="submit" name="filter" class="btn btn-primary waves">Filter</button>
                            </div>
                            <div class="form-group col-md-1">
                                <button type="submit" name="clearFilter" class="btn btn-danger waves">Clear
                                    Filter</button>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>
                <!-- Filter Section End -->


                <div class="card mt-3">
                    <div class="card-header">Bank Balance List</div>
                    <div class="card-body">
                        <form action="#" method="POST">

                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Table</th>
                                        <th>Party Name</th>
                                        <th>Opening Balance</th>
                                        <th>Receivable</th>
                                        <th>Payment</th>
                                        <th>Closing Balance</th>
                                        <th class="text-center" style="width: 170px;">Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Table</th>
                                        <th>Party Name</th>
                                        <th>Opening Balance</th>
                                        <th>Receivable</th>
                                        <th>Payment</th>
                                        <th>Closing Balance</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                           
                            
                    				if(mysqli_num_rows($result)){
                    					while($row = mysqli_fetch_assoc($result)){
                                $ext_Party = "";
                                $table = "";

                                if(isset($row['bank_receipt']) && $row['bank_receipt'] != "0"){
                                  $receipt_sql = "SELECT ep.partyname,br.table_indicator FROM bank_receipt br LEFT JOIN external_party ep ON br.party = ep.id WHERE br.id =".$row['bank_receipt'] ;
                                  $receipt_result = mysqli_query($conn,$receipt_sql);
                                  if(mysqli_num_rows($receipt_result) > 0){
                                    $prt_data = mysqli_fetch_assoc($receipt_result);
                                    $ext_Party = $prt_data['partyname'];
                                    $table = $prt_data['table_indicator'];
                                  }
                                  
                                }

                                if(isset($row['bank_peyout']) && $row['bank_peyout'] != "0" && $row['bank_peyout'] != null ){
                                  $payout_sql = "SELECT * FROM bank_transaction WHERE id =".$row['bank_peyout'];
                                  $payout_result = mysqli_query($conn,$payout_sql);
                                  if(mysqli_num_rows($payout_result) > 0){
                                    $payout_data = mysqli_fetch_assoc($payout_result);
                                    if($payout_data['table_indicator'] != "Transport Payout"){
                                      if($payout_data['pay_to'] == '0'){
                                        $prt_sql = "SELECT * FROM external_party WHERE id = '".$payout_data['ext_party']."' ";
                                        $prt_result = mysqli_query($conn,$prt_sql);
                                        if(mysqli_num_rows($prt_result) > 0){
                                          $prt_data = mysqli_fetch_assoc($prt_result);
                    
                                          $ext_Party = $prt_data['partyname'];
                                        }
                                      }else{
                                        $brok_sql = "SELECT * FROM broker WHERE id = '".$payout_data['ext_party']."' ";
                                        $brok_result = mysqli_query($conn,$brok_sql);
                                        if(mysqli_num_rows($brok_result) > 0){
                                          $brok_data = mysqli_fetch_assoc($brok_result);
                                          $ext_Party = $brok_data['name'];
                                        }
                                      }
                                    }else{
                                      $sql3 = "select * from transport where id = ".$payout_data['ext_party'];
                                      $result3 = mysqli_query($conn,$sql3);
                                      if(mysqli_num_rows($result3) > 0){
                                        $row3 = mysqli_fetch_assoc($result3);
                                        $ext_Party =  $row3['trans_name'];
                                      }
                                    }
                                    $table = $payout_data['table_indicator'];
                                  }
                                  
                                }
                          ?>
                                    <tr>

                                        <td><?php echo $i+1 ?></td>
                                        <td><?php echo date("d/m/Y",strtotime($row['date']));?></td>
                                        <td><?php if(isset($table)){ echo $table; } ?></td>
                                        <td><?php if(isset($ext_Party)){ echo $ext_Party; } ?></td>
                                        <td><?php echo $row['previous_balance'] ?></td>
                                        <td><?php echo $row['balance'] ?></td>
                                        <td><?php echo $row['payment'] ?></td>
                                        <td><?php echo $row['total_balance'] ?></td>
                                        <?php
                            if(!isset($page))
                            {
                            $page=1;
                            }
                            ?>


                                        <td class="text-center">
                                            <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>"
                                                class="btn btn-success"><i class="fa fa-eye"></i></a>
                                            <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>"
                                                class="btn btn-info"><i class="fa fa-user-edit"></i></a>

                                           <!-- <a href="home.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>"
                                                class="btn btn-danger"
                                                onclick="return confirm('Are you sure to delete this record?')"><i
                                                    class="fa fa-trash-alt"></i></a> -->

                                        </td>
                                    </tr>
                                    <?php
                              $i++;}
                            }
                          ?>
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <?php
                    if(!isset($_SESSION['balance_filter_data'])) {
                      $query = "SELECT COUNT(*) FROM bank_balance WHERE bank = '".$_SESSION['bank']."' AND firm = '".$_SESSION["bank_transaction_firm_id"]."' AND financial_year = '".$_SESSION["bank_transaction_financial_year_id"]."'";     
                      $rs_result = mysqli_query($conn, $query);     
                      $row = mysqli_fetch_row($rs_result);     
                      $total_records = $row[0];
                    ?>


                    <div class="pagination-parent">
                        <div class="total-pg">
                            <?php 
                         echo "Total Records : ".$total_records;
                         ?>
                        </div>

                        <ul class="pagination">

                            <?php                           
                           // Number of pages required.   
                          $total_pages = ceil($total_records / $per_page_record);     
                          $pagLink = ""; 


                          $totalPages  = $total_pages;
                          $currentPage = $page;

                          if ($totalPages <= 10) {
                              $start = 1;
                              $end   = $totalPages;
                          } else {
                              $start = max(1, ($currentPage - 4));
                              $end   = min($totalPages, ($currentPage + 5));

                              if ($start === 1) {
                                  $end = 10;
                              } elseif ($end === $totalPages) {
                                  $start = ($totalPages - 9);
                              }
                          }

                          if($page>=2)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link"
                                    href="home.php?page=<?php echo $page-1 ?>">Previous</a></li>
                            <?php 
                          }

                          for ($i = $start; $i <= $end; $i++) 
                          { 
                                if ($i == $page) 
                                { 
                                ?>
                            <li class="page-item active"><a class="page-link"
                                    href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                            <?php   
                                }               
                                else  
                                {  
                                ?>
                            <li class="page-item"><a class="page-link"
                                    href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                            <?php    
                                 
                                }  
                          } 

                          if($page<$total_pages)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link"
                                    href="home.php?page=<?php echo $page+1 ?>">Next</a></li>
                            <?php 
                          }

                      ?>
                        </ul>
                        <div class="total-pages">Total Pages : <?php echo $total_pages; ?></div>
                    </div>
                    <?php 
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
    </div>


    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
        integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous">
    </script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous">
    </script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>