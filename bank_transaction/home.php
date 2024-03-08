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

$firm = $_SESSION["bank_transaction_firm_id"];
$financial_year = $_SESSION["bank_transaction_financial_year_id"];

$user_id = $_SESSION['username'];
date_default_timezone_set('Asia/Kolkata');
$timestamp = date("Y-m-d H:i:s");

  if (isset($_GET['delete'])) {
    $id     = $_GET['delete'];
    $sql    = "select * from bank_transaction where id = " . $id;
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $rowData = mysqli_fetch_assoc($result);
        $sql2  = "SELECT * FROM `bank_transaction` WHERE date >= '" . $rowData['date'] . "' AND `bank` = '" . $rowData['bank'] . "' AND `firm` = '" . $rowData['firm'] . "' AND `financial_year` = '" . $rowData['financial_year'] . "' ORDER BY  `date` ASC,`id` ASC";
        $result2 = mysqli_query($conn,$sql2);
        if(mysqli_num_rows($result2) > 0){
          while($row2 = mysqli_fetch_assoc($result2)){
            if($rowData['date'] == $row2['date'] && $rowData['id'] > $row2['id']){
            }else{
              $bal = $row2['bankbalance'] + $rowData['total_payment'];
              $sql3        = "UPDATE `bank_transaction` SET `bankbalance`='" . $bal . "' WHERE id = '" . $row2['id'] . "'";
              $result3 = mysqli_query($conn, $sql3);
            }
          }
        }
        $sql     = "delete from bank_transaction where id=" . $rowData['id'];
        if (mysqli_query($conn, $sql)) {
            $sql_sub_tran    = "SELECT * FROM bank_transaction_history WHERE bank_transaction_id = " . $rowData['id'];
            $sub_tran_result = mysqli_query($conn, $sql_sub_tran);
            if (mysqli_num_rows($sub_tran_result) > 0) {
                while ($sub_tran_row = mysqli_fetch_assoc($sub_tran_result)) {
                    if (isset($sub_tran_row)) {
                        $delete = "delete from bank_transaction_history where id=" . $sub_tran_row['id'];
                        $result = mysqli_query($conn, $delete);
                    }
                }
            }
            
            $sqlbank     = "SELECT * FROM `bank_balance` WHERE `bank_peyout` = '" . $rowData['id'] . "' AND `bank` = '" . $rowData['bank'] . "' AND `firm` = '" . $rowData['firm'] . "' AND `financial_year` = '" . $rowData['financial_year'] . "' ORDER BY `bank_balance`.`id` DESC";
            $result_bank = mysqli_query($conn, $sqlbank);
            if (mysqli_num_rows($result_bank) > 0) {
                $bank_row = mysqli_fetch_assoc($result_bank);
                $date     = $bank_row['date'];
                if (isset($bank_row)) {

                  $bank_payment = $bank_row['payment'];
                  
                  $sql_bal    = "SELECT * FROM `bank_balance` WHERE date >= '" . $date . "' AND `bank` = '" . $bank_row['bank'] . "' AND `firm` = '" . $bank_row['firm'] . "' AND `financial_year` = '" . $bank_row['financial_year'] . "' ORDER BY  `bank_balance`.`date` ASC,`bank_balance`.`id` ASC";
                
                  $result_bal = mysqli_query($conn, $sql_bal);
                  if (mysqli_num_rows($result_bal) > 0) {
                      while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                          $pre_bal       = $bal_row['previous_balance'] + $bank_payment;
                          $clo_bal       = $bal_row['total_balance'] + $bank_payment;
                          if($date == $bal_row['date'] && $bank_row['id'] > $bal_row['id']){
                          }else{
                            $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                            $update_result = mysqli_query($conn, $update);
                          }
                      }
                  }
                  
                  $b_bal_delete = "delete from bank_balance where id=" . $bank_row['id'];
                  if ($result = mysqli_query($conn, $b_bal_delete)) {
                      $successMsg = 'New record added successfully';
                      header('Location: home.php');
                  } else {
                      $errorMsg = 'Error ' . mysqli_error($conn);
                  }
                }
            }
            if(!isset($page)){
                $page = 1;
            }
            header("Location: home.php?page=$page");
          } else {
              $errorMsg = 'Error ' . mysqli_error($conn);
          }
    }
  }

  if(isset($_POST['clearFilter']))
  {
    unset ($_SESSION["payout_filter_data"]);
    unset ($_SESSION["payout_filter_selected"]);
    header("location:home.php");
  }
  
  
    $isFilter=false;
    $selectedArr=array();
    if (isset($_POST['filter']) || isset($_SESSION['payout_filter_data'])) 
    {
  
      $isFilter=true;
  
      $main_query="select * from bank_transaction where ";
      $where_cond = array();
  
      if(isset($_POST['filter']))
      {
          // ext_party
          if(isset($_POST['ext_party_id']))
          {
            $ext_party=implode(",",$_POST['ext_party_id']);
            $where_cond[] = " ext_party in (".$ext_party.")";

          }

          //table Year
          if(isset($_POST['table']))
          {
            $table=$_POST['table'];
            $where_cond[] = "table_indicator = '".$table."'";
          }

          $start_date='';
          $end_date='';
          if($_POST['start_date']!='' && $_POST['end_date']=='')
          {
            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));

            $where_cond[] = " date>='".$start_date."'";
          }

          if($_POST['start_date']=='' && $_POST['end_date']!='')
          {
            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));

            $where_cond[] = " date<='".$end_date."'";
          }

           
          if($_POST['start_date']!='' && $_POST['end_date']!='')
          {

            $start_date = str_replace('/', '-', $_POST['start_date']);
            $start_date = date('Y-m-d', strtotime($start_date));

            $end_date = str_replace('/', '-', $_POST['end_date']);
            $end_date = date('Y-m-d', strtotime($end_date));

            $where_cond[] = " date>='".$start_date."' AND date<='".$end_date."'";
          }
  
            
            $selectedArr=$_POST;
            $_SESSION['payout_filter_selected']=$selectedArr;
            $_SESSION['payout_filter_data']=$where_cond;
      }
      else
      {
        $where_cond=$_SESSION['payout_filter_data'];
        $selectedArr=$_SESSION['payout_filter_selected'];
      }
  
      $i=0;
  
      if(!empty($where_cond)){
       
        $where = implode(' AND ',$where_cond);
        $main_query = $main_query.$where." AND firm = '".$firm."' AND financial_year = '".$financial_year."' ORDER BY date DESC,id DESC";
  
        $result = mysqli_query($conn, $main_query);
  
      }else{
          $_POST = array();

          $start_from = ($page-1) * $per_page_record;  
          $sql = "select * from bank_transaction WHERE firm = '".$firm."' AND financial_year = '".$financial_year."' ORDER BY date DESC,id DESC ";     
          $result = mysqli_query($conn, $sql);
          }
      
    }else{
  
      
      //pagination  ------------------
      $per_page_record = 10;         
      if (isset($_GET["page"]) && $_GET["page"] != "") 
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
      $sql = "SELECT * FROM bank_transaction where firm = '".$firm."' AND financial_year = '".$financial_year."' ORDER BY date DESC,id DESC LIMIT $start_from, $per_page_record";     
      $result = mysqli_query($conn, $sql);
    }

   
	
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bank Payout List</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/css/bootstrap.css">

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="../style4.css">
    <link rel="stylesheet" href="../css/custom.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" charset="utf-8"></script>
    <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
    <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script>
        $(function() {
            $("#sidebarnav").load("../nav.html");
            $("#topnav").load("../nav2.html");

            $(".datepicker").datepicker({
                 dateFormat:'dd/mm/yy',
                 changeMonth: true,
                 changeYear: true
             });
             $(".datepicker").keydown(false);
             $('.selectpicker').selectpicker();
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
                    <a class="navbar-brand" href="home.php"><span class="page-name-top"><span class="icon-report_dashboard"></span> Bank Payout Database</span></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto"></ul>
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item mr-2">
                                <form action="../bank_transation.php" method="post">
                                    <input type="hidden" name="financial_year" value="<?php echo $_SESSION['bank_transaction_financial_year']; ?>">
                                    <input type="hidden" name="firm" value="<?php echo $_SESSION['bank_transaction_firm'].'/'.$_SESSION['bank_transaction_firm_id']; ?>">

                                    <button type="submit" name="submit" class="btn btn-outline-danger"><i class="fa fa-sign-out-alt"></i><span class="pl-1">Back</span></button>
                                </form>
                            </li>
                            <li class="nav-item mr-2">
                                <button type="button" name="viewfilter" id="viewfilter" class="btn btn-outline-primary"><i class="fa fa-filter"></i><span class="pl-1">View Filter</span></button>

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
                            <li class="nav-item mr-2"><a class="btn btn-outline-secondary" href="../bank_transation.php"><i class="fa fa-undo-alt"></i> Pre-Selection</a></li>
                            <li class="nav-item mr-2"><a title="Excel Sheet" class="btn btn-success" id="viewExcel"><i class="fa fa-file-excel"></i></a></li>
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
                            <li class="nav-item"><a class="btn btn-primary" href="create.php"><i class="fa fa-user-plus"></i></a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- last change on table START-->
            <div class="last-updates">
                <div class="firm-selectio">
                    <div class="firm-selection-pre">
                        <span class="pre-firm">Firm : </span><span class="pre-firm-name"><?php echo $_SESSION["bank_transaction_firm"]; ?></span>
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
           $sqlLastChange="select username,updated_at from bank_transaction where
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

              echo "<span class='fullch'><span class='chtext'><span class='icon-edit'></span>Last Updated By :</span> <span class='userch'>".$user_name."</span> - <span class='datech'>".date('d/m/Y h:i:s A', strtotime($lastChangeRow['updated_at']))."</span> </span>";
           }
          ?>

                </div>
            </div>

            <!-- last change on table END-->

            <div class="container-fluid">
                <div class="row justify-content-center">
                  <!-- Genarate Excel Section Start -->
                    <div class="card viewExcel m-3">
                        <div class="card-header">Generate Excel</div>
                        <div class="card-body">
                            <form action="generate_excel.php" method="post" enctype="multipart/form-data">
                                <div class="row">

                                    <div class="form-group col-md-3">
                                        <label for="start_date">Select Start Date :</label>
                                        <input class="form-control datepicker" type="text" id="start_date" name="start_date" autocomplete="off" placeholder="Start Date" value="<?php if(isset($_SESSION['purpay_filter_selected']['start_date'])){ echo $_SESSION['purpay_filter_selected']['start_date']; } ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="end_date">Select End Date :</label>
                                        <input id="end_date" class="form-control datepicker" type="text" name="end_date" autocomplete="off" placeholder="End Date" value="<?php if(isset($_SESSION['purpay_filter_selected']['end_date'])){ echo $_SESSION['purpay_filter_selected']['end_date']; } ?>">
                                    </div>

                                    <div class="form-group col-md-3">
                                      <label for="bank_ac_number">Select Bank</label>
                                      <?php
                                          $sql_prt = "select * from party where id = ". $_SESSION["bank_transaction_firm_id"];
                                          $result_prt = mysqli_query($conn, $sql_prt);
                                          if(mysqli_num_rows($result_prt) > 0){
                                            $firm_row = mysqli_fetch_assoc($result_prt);
                                            $bank = json_decode($firm_row['bankDetails'],true);
                                          }
                                          ?>                      
                                      <select name="bank[]"  class="form-control selectpicker" title="Select" multiple="">
                                          <option  disabled="">Select Option</option>
                                          <?php   
                                            if(isset($bank)){                
                                                foreach ($bank as $result_prt) 
                                                {
                                                  echo "<option  value='".$result_prt['bank_ac_number']."'>".$result_prt['bank_name']. "</option>";
                                                }
                                            }
                                          ?>                              
                                      </select>
                                    </div>

                                    <div class="form-group col-md-3">
                                        <label for="table">Select Table</label>
                                        <select name="table" class="form-control" title="Select Option">
                                          <option vlaue="Debit Note Ad-Hoc">Debit Note Ad-Hoc</option>
                                          <option vlaue="Bales Payout">Bales Payout</option>
                                          <option vlaue="Transport Payout">Transport Payout</option>
                                          <option vlaue="RD Kapas purchase Payment">RD Kapas purchase Payment</option>
                                          <option vlaue="URD Kapas purchase Payment">URD Kapas purchase Payment</option>
                                          <option vlaue="Other Payout">Other Payout</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="form-group col-md-1">
                                        <button type="submit" name="submit" class="btn btn-primary waves">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Genarate Excel Section End -->
                    <!-- Filter Section Start -->
                    <div class="card viewfilter m-3">
                        <div class="card-header">Filter</div>
                        <div class="card-body">

                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group col-md-3 extParty">
                                        <label for="ext_party_id">Select External Party</label>
                                        <?php
                                            $sqlFirm = "select * from external_party";
                                            $resultParty = mysqli_query($conn, $sqlFirm);
                                        ?>
                                        <select name="ext_party_id[]" class="form-control selectpicker" data-live-search="true" multiple="">
                                            <?php                   
                                          foreach ($conn->query($sqlFirm) as $resultParty) 
                                          {
                                            if(isset($selectedArr['ext_party_id']) && in_array($resultParty['id'], $selectedArr['ext_party_id']))
                                            {
                                                echo "<option  value='".$resultParty['id']."' selected>" .$resultParty['partyname']. "</option>";
                                            }
                                            else
                                            {
                                                echo "<option  value='" .$resultParty['id']. "'>" .$resultParty['partyname']. "</option>";
                                            }
                                          }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-3">
                                      <label for="table">Select Table</label>
                                      <select name="table" id="table" class="form-control">
                                          <option selected="" disabled="">Select Option</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "Debit Note Ad-Hoc"){ echo "selected";} ?> vlaue="Debit Note Ad-Hoc">Debit Note Ad-Hoc</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "Bales Payout"){ echo "selected";} ?> vlaue="Bales Payout">Bales Payout</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "Transport Payout"){ echo "selected";} ?> vlaue="Transport Payout">Transport Payout</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "RD Kapas purchase Payment"){ echo "selected";} ?> vlaue="RD Kapas purchase Payment">RD Kapas purchase Payment</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "URD Kapas purchase Payment"){ echo "selected";} ?> vlaue="URD Kapas purchase Payment">URD Kapas purchase Payment</option>
                                          <option <?php if(isset($_SESSION['payout_filter_selected']['table']) && $_SESSION['payout_filter_selected']['table'] == "Other Payout"){ echo "selected";} ?> vlaue="Other Payout">Other Payout</option>
                                      </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                      <label for="date">Start Date</label>
                                      <input type="text" class="form-control datepicker" placeholder="Start Date" name="start_date" autocomplete="off" value="<?php if(isset($_SESSION['payout_filter_selected']['start_date'])){ echo $_SESSION['payout_filter_selected']['start_date'];} ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                      <label for="date">End Date</label>
                                      <input type="text" class="form-control datepicker" placeholder="End Date" name="end_date" autocomplete="off" value="<?php if(isset($_SESSION['payout_filter_selected']['end_date'])){ echo $_SESSION['payout_filter_selected']['end_date'];} ?>">
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
                <div class="card">
                    <div class="card-header">Bank Payout List</div>
                    <div class="card-body">
                        <form action="#" method="POST">

                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <!--<th>Balance</th>-->
                                        <th>Table</th>
                                        <th>External Party</th>
                                        <th>Payment</th>
                                        <th class="text-center" style="width: 170px;">Action</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Bank</th>
                                        <!--<th>Balance</th>-->
                                        <th>Table</th>
                                        <th>External Party</th>
                                        <th>Payment</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php
                           
                            
                    				if(mysqli_num_rows($result)){
                    					while($row = mysqli_fetch_assoc($result)){
                          ?>
                                    <tr>

                                        <td><?php echo $i+1 ?></td>
                                        <td><?php echo date("d/m/Y",strtotime($row['date']));?></td>
                                        <td><?php
                                        $bank_name = "";
                                        $sql_firm = "SELECT * FROM party WHERE id = ".$firm;
                                        $firm_result = mysqli_query($conn,$sql_firm);
                                        if(mysqli_num_rows($firm_result) > 0){
                                          $firm_row = mysqli_fetch_assoc($firm_result);
                                          $bank_details = json_decode($firm_row['bankDetails'],true);
                                          foreach($bank_details as $bData){
                                            if($bData['bank_ac_number'] == $row['bank']){
                                              $bank_name = $bData['bank_name'];
                                            }
                                          }
                                        }
                                        
                                    echo $bank_name; 
                                  ?>
                                        </td>
                                        <!--<td><?php echo $row['bankbalance'] ?></td>-->
                                        <td><?php echo $row['table_indicator'] ?></td>
                                        <td>
                                            <?php 
                                                if($row['table_indicator'] != "Transport Payout"){
                                                  if($row['ext_party'] != null){
                                                    if($row['pay_to'] == '0'){
                                                      $sql3 = "select * from external_party where id = ".$row['ext_party'];
                                                      $result3 = mysqli_query($conn,$sql3);
                                                      if(mysqli_num_rows($result3) > 0){
                                                        $row3 = mysqli_fetch_assoc($result3);
                                                        echo $row3['partyname'];
                                                      }
                                                    }else{
                                                      $sql3 = "select * from broker where id = ".$row['ext_party'];
                                                      $result3 = mysqli_query($conn,$sql3);
                                                      if(mysqli_num_rows($result3) > 0){
                                                        $row3 = mysqli_fetch_assoc($result3);
                                                        echo $row3['name'];
                                                      }
                                                    }
                                                  }
                                                }else{
                                                  $sql3 = "select * from transport where id = ".$row['ext_party'];
                                                  $result3 = mysqli_query($conn,$sql3);
                                                  if(mysqli_num_rows($result3) > 0){
                                                    $row3 = mysqli_fetch_assoc($result3);
                                                    echo $row3['trans_name'];
                                                  }
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo $row['total_payment'] ?></td>
                                        <?php
                            if(!isset($page))
                            {
                            $page=1;
                            }
                            ?>
                                        <td class="text-center">
                                            <a href="show.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-success"><i class="fa fa-eye"></i></a>
                                            <a href="edit.php?id=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-info"><i class="fa fa-user-edit"></i></a>
                                            <a href="home.php?delete=<?php echo $row['id'] ?>&page=<?php echo $page ?>" class="btn btn-danger" onclick="return confirm('Are you sure to delete this record?')"><i class="fa fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                              $i++;
                              }
                            }
                          ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <?php 
                    if(!isset($_SESSION['payout_filter_data'])) {
                        $query = "SELECT COUNT(*) FROM bank_transaction WHERE firm = '".$firm."' AND financial_year = '".$financial_year."'";     
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
                            <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $page-1 ?>">Previous</a></li>
                            <?php 
                          }

                          for ($i = $start; $i <= $end; $i++) 
                          { 
                                if ($i == $page) 
                                { 
                                ?>
                            <li class="page-item active"><a class="page-link" href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                            <?php   
                                }               
                                else  
                                {  
                                ?>
                            <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $i ?>"><?php echo $i ?></a></li>
                            <?php    
                                 
                                }  
                          } 

                          if($page<$total_pages)
                          {   
                          ?>
                            <li class="page-item"><a class="page-link" href="home.php?page=<?php echo $page+1 ?>">Next</a></li>
                            <?php 
                          }
                          ?>
                          </ul>
                        <div class="total-pages">Total Pages : <?php echo $total_pages; ?></div>
                          <?php 
                        }
                      ?>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>

    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>