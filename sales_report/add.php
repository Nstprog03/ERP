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


        include('../global_function.php'); 
        $data=getFileStoragePath("sales_report",$_SESSION['sales_financial_year_id']);  //function from global_function file
        $root_path=$data[0]; // file move path
        $store_path=$data[1]; // db store path

        


        
        $party_data= explode('/', $_POST['party_data']);
        $party_name = $party_data[0];
        $conf_no = $party_data[1];
        $firm= $_POST['firm'];
        $sales_id = $_POST['sales_id'];
        $financial_year_id = $_POST['financial_year_id'];
        $delivery_city= $_POST['delivery_city'];
        $truck= $_POST['truck'];
        $invoice_date = DateTime::createFromFormat('d/m/Y', $_POST['invoice_date']);
        $invoice_date=$invoice_date->format('Y-m-d');


        $parakh_date = DateTime::createFromFormat('d/m/Y', $_POST['parakh_date']);
        $parakh_date=$parakh_date->format('Y-m-d');

        $invice_no= $_POST['invice_no'];
        $avl_bales= $_POST['avl_bales'];
        $noOFBales= $_POST['noOFBales'];
        $net_weight= $_POST['net_weight'];
        $candy_rate= $_POST['candy_rate'];
        $grs_amt= $_POST['grs_amt'];
        $txn= $_POST['txn'];
        $txn_amt= $_POST['txn_amt'];
        $Other= $_POST['Other'];
        $other_amt_tcs= $_POST['other_amt_tcs'];
        $total_value= $_POST['total_value'];

        $start_pr= $_POST['start_pr'];
        $end_pr= $_POST['end_pr'];

        $lot_no = json_encode($_POST['lot_no']);
        $lot_bales = json_encode($_POST['lot_bales']);


        $shipping_ext_party_id=$_POST['shipping_ext_party_id'];
        $variety=$_POST['variety'];
        $sub_variety=$_POST['sub_variety'];
        $length=$_POST['length'];
        $strength=$_POST['strength'];
        $mic=$_POST['mic'];
        $rd=$_POST['rd'];
        $trash=$_POST['trash'];
        $moi=$_POST['moi'];
        $credit_days=$_POST['credit_days'];


        $imgArr=array();
    $img_titleArr = array();
    foreach ($_FILES['doc_file']['tmp_name'] as $key =>  $imges) {
        
        $img_title = $_POST['img_title'][$key];
        $img = $_FILES['doc_file']['name'][$key];
            $imgTmp = $_FILES['doc_file']['tmp_name'][$key];
            $imgSize = $_FILES['doc_file']['size'][$key];

    
        if(!empty($img)){
                array_push($img_titleArr,$img_title);
                $imgExt = strtolower(pathinfo($img, PATHINFO_EXTENSION));

                $allowExt  = array('jpeg', 'jpg', 'png', 'gif', 'doc', 'docx', 'csv', 'pdf', 'xls', 'xlsx', 'txt');

                $img = time().'_'.rand(1000,9999).'.'.$imgExt;
                array_push($imgArr,$store_path.$img);

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


            }
    }

    $imgTitle = implode(',', $img_titleArr);
    $imgStore = implode(',', $imgArr);




        $user= $_POST['username'];
        date_default_timezone_set('Asia/Kolkata');
        $timestamp=date("Y-m-d H:i:s");



  

        $sql = "insert into sales_report (conf_no,party_name,firm, delivery_city, truck, invoice_date,invice_no, avl_bales, noOFBales, net_weight, candy_rate, grs_amt, txn,txn_amt, Other,other_amt_tcs, total_value,start_pr,end_pr,lot_no,lot_bales,shipping_ext_party_id,variety,sub_variety,length,strength,mic,rd,trash,moi,credit_days,username,created_at,updated_at,parakh_date,sales_ids,financial_year_id,doc_file,img_title) values ('".$conf_no."','".$party_name."', '".$firm."', '".$delivery_city."', '".$truck."', '".$invoice_date."', '".$invice_no."', '".$avl_bales."', '".$noOFBales."', '".$net_weight."', '".$candy_rate."', '".$grs_amt."', '".$txn."', '".$txn_amt."', '".$Other."', '".$other_amt_tcs."', '".$total_value."', '".$start_pr."', '".$end_pr."', '".$lot_no."', '".$lot_bales."','".$shipping_ext_party_id."', '".$variety."', '".$sub_variety."', '".$length."', '".$strength."', '".$mic."', '".$rd."', '".$trash."', '".$moi."', '".$credit_days."','".$user."','".$timestamp."','".$timestamp."','".$parakh_date."','".$sales_id."','".$financial_year_id."','".$imgStore."','".$imgTitle."')";
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            //echo "New record created successfully. Last inserted ID is: " . $last_id;
            header('Location: index.php');

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    }    
                


            
    
        

?>
