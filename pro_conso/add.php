<?php
  require_once('../db.php');

  if (isset($_POST['Submit'])) {
    $product = $_POST['product'];
    $pur_conf = $_POST['pur_conf'];
    $conf_type = $_POST['conf_type'];
    $report_date = $_POST['report_date'];
    $party = $_POST['party'];
    $firm = $_POST['firm'];
    $seller_ref = $_POST['seller_ref'];
    $bargain_date = $_POST['bargain_date'];
    $broker = $_POST['broker'];
    $transit_ins = $_POST['transit_ins'];
    $pro_length = $_POST['pro_length'];
    $pro_meanlen = $_POST['pro_meanlen'];
    $pro_ui = $_POST['pro_ui'];
    $pro_str = $_POST['pro_str'];
    $pro_sfi = $_POST['pro_sfi'];
    $pro_mic = $_POST['pro_mic'];
    $pro_rd = $_POST['pro_rd'];
    $pro_b = $_POST['pro_b'];
    $pro_cg = $_POST['pro_cg'];
    $pro_trash = $_POST['pro_trash'];
    $pro_mois = $_POST['pro_mois'];
    $bales = $_POST['bales'];
    $pro_variety = $_POST['pro_variety'];
    $price = $_POST['price'];
    $del_addr = $_POST['del_addr'];
    $bill_inst = $_POST['bill_inst'];
    $spl_rmrk = $_POST['spl_rmrk'];
    $lab_name = $_POST['lab_name'];  
    
    

  if(!isset($errorMsg)){
			$sql = "insert into pro_conso(product, pur_conf, conf_type, report_date, party, firm, seller_ref, bargain_date, broker, transit_ins, pro_length, pro_meanlen, pro_ui, pro_str, pro_sfi, pro_mic, pro_rd, pro_b, pro_cg, pro_trash, pro_mois, bales, pro_variety, price, del_addr, bill_inst, spl_rmrk, lab_name)
					values('".$product."', '".$pur_conf."', '".$conf_type."', '".$report_date."', '".$party."', '".$firm."', '".$seller_ref."', '".$bargain_date."', '".$broker."', '".$transit_ins."', '".$pro_length."', '".$pro_meanlen."', '".$pro_ui."', '".$pro_str."', '".$pro_sfi."', '".$pro_mic."', '".$pro_rd."', '".$pro_b."', '".$pro_cg."', '".$pro_trash."', '".$pro_mois."', '".$bales."', '".$pro_variety."', '".$price."', '".$del_addr."', '".$bill_inst."', '".$spl_rmrk."', '".$lab_name."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
                echo $errorMsg;
			}
		}
  }
?>
