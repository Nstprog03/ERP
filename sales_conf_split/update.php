<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}
  if (isset($_POST['submit'])) {

      $id =$_POST['id'];
      
      $data = $_POST['conf_no'];
      $conf_no =explode('/',$data)[1];
      $sale_conf_id = explode('/',$data)[0];

      $conf_split_no = $_POST['conf_split_no'];
      $conf_type = $_POST['conf_type'];
      $split_party_name = $_POST['split_party_name'];
      $conf_split_date = DateTime::createFromFormat('d/m/Y', $_POST['conf_split_date']);
      $conf_split_date=$conf_split_date->format('Y-m-d');
      $firm = $_POST['firm'];
      $length = $_POST['length'];
      $strength = $_POST['strength'];    
      $mic = $_POST['mic'];
      $rd = $_POST['rd'];
      $trash = $_POST['trash'];
      $tax_type=$_POST['taxtype'];

      if($tax_type=='sgst')
      {
        $sgst = $_POST['sgst'];
        $cgst = $_POST['cgst'];
        $igst='';
      }
      else if($tax_type=='igst')
      {
        $igst = $_POST['igst'];
        $cgst = '';
        $sgst = '';
      }

      $no_of_bales = $_POST['no_of_bales'];
      $avl_bales = $_POST['avl_bales'];
     
      $lot_no = json_encode($_POST['lot_no']);
      $lot_bales = json_encode($_POST['lot_bales']);
      $press_no = $_POST['press_no'];

      $price = $_POST['price'];
      
      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];
      $external_party = $_POST['external_party'];
      $product = $_POST['product'];
      $broker = $_POST['broker'];
      $trans_ins = $_POST['trans_ins'];
      

      $press_no = $_POST['press_no'];
      // $variety = $_POST['variety'];
      // $sub_variety = $_POST['sub_variety'];
      $price = $_POST['price'];
     
      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];

      $shipping_ext_party_id = $_POST['shipping_ext_party_id'];
      $moi = $_POST['moi'];
      $credit_days = $_POST['credit_days'];

      $station = $_POST['station'];
      $dispatch_date = $_POST['dispatch_date'];
      $prod_quality = $_POST['prod_quality'];

      $prod_variety = $_POST['variety'];
      $sub_variety = $_POST['sub_variety'];

       date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
      $username=$_SESSION['username'];

     

      //......................Edit sale report Start..........................

      $old_conf_split_no=$_POST['old_conf_split_no'];

      $sql_report_edit = "select * from sales_report where conf_no = '".$old_conf_split_no."' AND sales_ids= '".$id."'";

      $result_report_edit = mysqli_query($conn, $sql_report_edit);      
      if(mysqli_num_rows($result_report_edit)){
        while($row_report_edit = mysqli_fetch_assoc($result_report_edit)){
          $editreport_ids = $row_report_edit['id'];
            
              $ReportSQLEdit="update sales_report set
                party_name='".$split_party_name."',
                conf_no='".$conf_split_no."',

                shipping_ext_party_id='".$shipping_ext_party_id."',
                length='".$length."',
                strength='".$strength."',
                mic='".$mic."',
                rd='".$rd."',
                trash='".$trash."',
                moi='".$moi."',
                variety='".$prod_variety."',
                sub_variety='".$sub_variety."',
                credit_days='".$credit_days."',
                candy_rate='".$price."'
                where 
                id='".$editreport_ids."'
              ";

            $reportSQL = mysqli_query($conn, $ReportSQLEdit);
            if($reportSQL){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }



            //sales Receivable Update
             $sql_sr = "select * from sales_rcvble where sale_report_id = '".$editreport_ids."'";

                    $result_sr = mysqli_query($conn, $sql_sr);      
                    if(mysqli_num_rows($result_sr)>0)
                    {
                      while($row_sr = mysqli_fetch_assoc($result_sr))
                      {
                          $rcv_id=$row_sr['id'];
                          if ($credit_days != '' ) 
                          {
                             $Final_credit_day=$credit_days-1;

                            $due_date1 = date('Y-m-d', strtotime($row_sr['parakh_date']. " + $Final_credit_day day"));

                            $due_date=date("Y-m-d", strtotime($due_date1));

                           $sql_sr_update="update sales_rcvble set
                                    pur_party='".$split_party_name."',
                                    credit_days='".$credit_days."',
                                    due_date='".$due_date1."'
                                    where id='".$rcv_id."'";
                             
                            if(mysqli_query($conn, $sql_sr_update))
                            {
                              $successMsg = 'New record updated successfully';
                            }else{
                              $errorMsg = 'Error '.mysqli_error($conn);
                            }

                          }

                      }
                    }






        }
      }
      
      //......................Edit sale report END..........................
     
   
  
  if(!isset($errorMsg)){

      // sale report
    // print_r($conf_no);exit();
       /* $sales_report = "select * from sales_report where conf_no = '".$conf_no."' AND firm = '".$_SESSION['sales_conf_firm_id']."'";

        $result_sales_report = mysqli_query($conn, $sales_report);      
        if(mysqli_num_rows($result_sales_report)){
          while($row_sales_report = mysqli_fetch_assoc($result_sales_report)){
              
              $sale_report_id = $row_sales_report['id'];
              

              $ReportSQLEdit="update sales_report set
                candy_rate='".$price."'
                where 
                id='".$sale_report_id."'
              ";

              $reportSQL = mysqli_query($conn, $ReportSQLEdit);
              if($reportSQL){
                $successMsg = 'New record Updated successfully';
                
              }else{
                $errorMsg = 'Error '.mysqli_error($conn);
                  echo $errorMsg;
              }

              
            }
      }*/

      // sale report



        $sql="update sales_conf_split 

        set
          product='".$product."',
          conf_no='".$conf_no."',
          conf_split_no='".$conf_split_no."',
          conf_type='".$conf_type."',
          conf_split_date='".$conf_split_date."',
          split_party_name='".$split_party_name."',
          external_party='".$external_party."',
          firm='".$firm."',
          broker='".$broker."',
          trans_ins='".$trans_ins."',
          length='".$length."',
          strength='".$strength."',
          mic='".$mic."',
          rd='".$rd."',
          trash='".$trash."',
          sgst='".$sgst."',
          cgst='".$cgst."',
          igst='".$igst."',
          no_of_bales='".$no_of_bales."',
         
          lot_no='".$lot_no."',
          lot_bales='".$lot_bales."',
          press_no='".$press_no."',
          price='".$price."',
          bill_inst='".$bill_inst."',
          spl_rmrk='".$spl_rmrk."',
          tax_type='".$tax_type."',
          avl_bales='".$avl_bales."',
          shipping_ext_party_id='".$shipping_ext_party_id."',
          moi='".$moi."',
          credit_days='".$credit_days."',
          station='".$station."',
          prod_quality='".$prod_quality."',
          variety='".$prod_variety."',
          sub_variety='".$sub_variety."',
          dispatch_date='".$dispatch_date."',
          sale_conf_id='".$sale_conf_id."',
          username='".$username."',
          updated_at='".$timestamp."'

          where id='".$id."'
        ";      
      $result = mysqli_query($conn, $sql);
      if($result){
        $successMsg = 'New record Updated successfully';
        $page=1;
        if(isset($_POST['page_no']))
        {
          $page=$_POST['page_no'];
        }
        header("Location: index.php?page=$page");

      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
      }
    }
  }
?>
