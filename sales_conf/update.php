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
  if (isset($_POST['Submit'])) {
     
      $id=$_POST['id'];
      $sales_conf = $_POST['sales_conf']; 
      $conf_type = $_POST['conf_type'];

      //date convert dd/mm/yyyy to yyyy-mm-dd
      if($_POST['sales_date']!='')
      {
        $sales_date = DateTime::createFromFormat('d/m/Y', $_POST['sales_date']);
        $sales_date=$sales_date->format('Y-m-d');
      }
      

      $external_party = $_POST['external_party'];
      $shipping= $_POST['shipping_ext_party_id'];

      $firm = $_POST['firm'];

      $product = $_POST['product'];
      $prod_quality = $_POST['prod_quality'];
      $prod_variety = $_POST['variety'];
      $sub_variety = $_POST['sub_variety'];

      date_default_timezone_set('Asia/Kolkata');
      $timestamp=date("Y-m-d H:i:s");
      $username= $_SESSION["username"];

      $broker = $_POST['broker'];
      $trans_ins = $_POST['trans_ins'];

      $length = $_POST['length'];
      $strength = $_POST['strength'];
      $mic = $_POST['mic'];
      $rd = $_POST['rd'];
      $trash = $_POST['trash'];
      $moi = $_POST['moi'];


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

      $cont_quantity = $_POST['cont_quantity'];
      $no_lot = $_POST['no_lot'];
      $lot_no = json_encode($_POST['lot_no']);
      $lot_bales = json_encode($_POST['lot_bales']);
      $press_no = $_POST['press_no'];
      $candy_rate = $_POST['candy_rate'];
     
      $bill_inst = $_POST['bill_inst'];
      $spl_rmrk = $_POST['spl_rmrk'];

      if($_POST['dispatch_date']!='')
      {
        $dispatch_date = DateTime::createFromFormat('d/m/Y', $_POST['dispatch_date']);
        $dispatch_date=$dispatch_date->format('Y-m-d');
      }
      $station = $_POST['station'];
      $credit_days = $_POST['credit_days'];


    
      //......................Edit sale Split Start..........................

      $sql_split_edit = "select * from sales_conf_split where conf_no = '".$sales_conf."'";

      $result_split_edit = mysqli_query($conn, $sql_split_edit);      
      if(mysqli_num_rows($result_split_edit)){
        while($row_split_edit = mysqli_fetch_assoc($result_split_edit))
        {
          $editsplt_ids = $row_split_edit['id'];
          $editspltConfNo = $row_split_edit['conf_split_no'];
          

           $SplitSQLEdit="update sales_conf_split set
                product='".$product."',
                external_party='".$external_party."',
                broker='".$broker."',
                trans_ins='".$trans_ins."',
                length='".$length."',
                strength='".$strength."',
                mic='".$mic."',
                rd='".$rd."',
                trash='".$trash."',
                moi='".$moi."',
                sgst='".$sgst."',
                cgst='".$cgst."',
                igst='".$igst."',
                price='".$candy_rate."',
                shipping_ext_party_id='".$shipping."',
                credit_days='".$credit_days."',
                station='".$station."',
                variety='".$prod_variety."',
                sub_variety='".$sub_variety."',
                prod_quality='".$prod_quality."',
                dispatch_date='".$dispatch_date."'

                where 
                id='".$editsplt_ids."'
              ";

            $resultSQLEdit = mysqli_query($conn, $SplitSQLEdit);
            if($resultSQLEdit){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }



            $ReportSQLEdit="update sales_report set
                
                shipping_ext_party_id='".$shipping."',
                length='".$length."',
                strength='".$strength."',
                mic='".$mic."',
                rd='".$rd."',
                trash='".$trash."',
                moi='".$moi."',
                variety='".$prod_variety."',
                sub_variety='".$sub_variety."',
                credit_days='".$credit_days."',
                candy_rate='".$candy_rate."'
                where 
                conf_no='".$editspltConfNo."' AND sales_ids='".$editsplt_ids."'
              ";

            $reportSQL = mysqli_query($conn, $ReportSQLEdit);
            if($reportSQL){
              $successMsg = 'New record Updated successfully';
              
            }else{
              $errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
            }
            

            //sales Receivable update
            $sql_sreport="select * from sales_report where conf_no='".$editspltConfNo."' AND sales_ids='".$editsplt_ids."'
              ";

            $result_sreport = mysqli_query($conn, $sql_sreport);      
            if(mysqli_num_rows($result_sreport)>0)
            {
              while($row_sreport = mysqli_fetch_assoc($result_sreport))
              {
                 $sreport_id=$row_sreport['id'];

                   $sql_sr = "select * from sales_rcvble where sale_report_id = '".$sreport_id."'";

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
           

          
        }



      }
      //......................Edit sale Split END..........................




      //......................Edit sale report Start..........................

      $sql_report_edit = "select * from sales_report where conf_no = '".$sales_conf."' AND sales_ids= '".$id."'";

      $result_report_edit = mysqli_query($conn, $sql_report_edit);      
      if(mysqli_num_rows($result_report_edit)){
        while($row_report_edit = mysqli_fetch_assoc($result_report_edit))
        {
          $editreport_ids = $row_report_edit['id'];
     
              $ReportSQLEdit="update sales_report set
                party_name='".$external_party."',
                shipping_ext_party_id='".$shipping."',
                length='".$length."',
                strength='".$strength."',
                mic='".$mic."',
                rd='".$rd."',
                trash='".$trash."',
                moi='".$moi."',
                variety='".$prod_variety."',
                sub_variety='".$sub_variety."',
                credit_days='".$credit_days."',
                candy_rate='".$candy_rate."'
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


            //sales Receivable update
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
                                pur_party='".$external_party."',
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

        $sql="update seller_conf set
          product='".$product."',
          sales_conf='".$sales_conf."',
          conf_type='".$conf_type."',
          sales_date='".$sales_date."',
          external_party='".$external_party."',
          firm='".$firm."',
          broker='".$broker."',
          trans_ins='".$trans_ins."',
          length='".$length."',
          strength='".$strength."',
          mic='".$mic."',
          rd='".$rd."',
          trash='".$trash."',
          moi='".$moi."',
          sgst='".$sgst."',
          cgst='".$cgst."',
          igst='".$igst."',
          cont_quantity='".$cont_quantity."',
          no_lot='".$no_lot."',
          lot_no='".$lot_no."',
          lot_bales='".$lot_bales."',
          press_no='".$press_no."',
          candy_rate='".$candy_rate."',
         
          bill_inst='".$bill_inst."',
          spl_rmrk='".$spl_rmrk."',
          tax_type='".$tax_type."',
          dispatch_date='".$dispatch_date."',
          station='".$station."',
          credit_days='".$credit_days."',
          shipping_ext_party_id='".$shipping."',
          prod_quality='".$prod_quality."',
          variety='".$prod_variety."',
          sub_variety='".$sub_variety."',
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
