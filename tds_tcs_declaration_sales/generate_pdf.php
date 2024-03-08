<?php
   session_start();
   
   require_once "../db.php";
   
   require_once "../PHPLibraries/dompdf/autoload.php";
   
   use Dompdf\Dompdf;
   
   //dd/mm/yyy
   
   function convertDate($date)
   {
       $final_date = "";
   
       if ($date != "" && $date != "0000-00-00") {
           $final_date = str_replace("-", "/", $date);
   
           $final_date = date("d/m/Y", strtotime($final_date));
       }
   
       return $final_date;
   }
   
if(isset($_GET["module"]) && $_GET["module"] == "sales"){

   if (isset($_GET["id"])) {
       $module = "";
       if (isset($_GET["module"])) {
           if ($_GET["module"] == "sales") {
               $module = "sales";
           } elseif ($_GET["module"] == "purchase_cotton") {
               $module = "purchase_cotton";
           }
       }
   
       $dompdf = new Dompdf();
   
       $dompdf->setPaper("A4", "portrait");
   
       $id = $_GET["id"];
   
       $sql = "SELECT * FROM `tds_tcs_declaration` where id='" . $id . "'";
   
       $result = mysqli_query($conn, $sql);
   
       if (mysqli_num_rows($result) > 0) {
           $row = [];
   
           foreach ($conn->query($sql) as $key => $result) {
               $row = $result;
           }
   
           //date
   
           if ($row["date"] != "" && $row["date"] != "0000-00-00") {
               $date = str_replace("-", "/", $row["date"]);
   
               $date = date("d/m/Y", strtotime($date));
           }
   
           //external party name & address
   
           $ext_sql =
               "select * from external_party where id='" .
               $row["ext_party_id"] .
               "'";
   
           $ext_result = mysqli_query($conn, $ext_sql);
   
           if (mysqli_num_rows($ext_result) > 0) {
               $ext_row = mysqli_fetch_assoc($ext_result);
   
               $ext_party = $ext_row["partyname"];
   
               $ext_gstin = $ext_row["gstin"];
   
               $ext_party_add1 =
                   $ext_row["address"] . ", " . $ext_row["city"] . ",";
   
               $ext_party_add2 =
                   "Dist. " .
                   $ext_row["district"] .
                   ", " .
                   $ext_row["state"] .
                   " - " .
                   $ext_row["pincode"];
           }
   
           //firm
   
           $firm_name = "";
   
           $firm_add1 = "";
   
           $firm_add2 = "";
   
           $email1 = "";
   
           $firm_gst = "";

		   $firm_tan = "";
   
           $contact1 = "";
   
           $sqlFirm = "select * from party where id='" . $row["firm_id"] . "'";
   
           $resultFirm = mysqli_query($conn, $sqlFirm);
   
           $rowFirm = mysqli_fetch_assoc($resultFirm);
   
           if (mysqli_num_rows($resultFirm) > 0) {
               $firm_name = strtoupper($rowFirm["party_name"]);
   
               $firm_add1 =
                   $rowFirm["party_address"] . ", " . $rowFirm["city"] . ",";
   
               $firm_add2 =
                   "Dist. " .
                   $rowFirm["district"] .
                   ", " .
                   $rowFirm["state"] .
                   " - " .
                   $rowFirm["pincode"];
   
               $firm_gst = "<b>GST IN : </b>" . $rowFirm["gst_in"];

			   $firm_tan = $rowFirm['tan_no'];
   
               $path = "../firm/files/logo/" . $rowFirm["logo_img"];
   
               $type = pathinfo($path, PATHINFO_EXTENSION);
   
               $data = file_get_contents($path);
   
               $company_logo =
                   "data:image/" . $type . ";base64," . base64_encode($data);
   
               //stamp
   
               $path2 = "../firm/files/stamp/" . $rowFirm["stamp_img"];
   
               $type = pathinfo($path2, PATHINFO_EXTENSION);
   
               $data2 = file_get_contents($path2);
   
               $stamp_image =
                   "data:image/" . $type . ";base64," . base64_encode($data2);
   
               //email
   
               $emailArr = json_decode($rowFirm["party_email"], true);
   
               if (isset($emailArr[0])) {
                   $email1 = "<b>Email : </b>" . $emailArr[0];
               }
   
               //contact no
   
               $contactArr = json_decode($rowFirm["contact_number"], true);
   
               if (isset($contactArr[0])) {
                   $contact1 = "
<b>Contact No. : </b>" . $contactArr[0];
               }
           }
   
           $year = "";
   
           $sql2 =
               "select * from financial_year where id='" .
               $row["financiyal_year_id"] .
               "'";
   
           $result2 = mysqli_query($conn, $sql2);
   
           if (mysqli_num_rows($result2) > 0) {
               $rowYear = mysqli_fetch_assoc($result2);
   
               //get Start Year And End Year
   
               $syear = date("Y", strtotime($rowYear["startdate"]));
   
               $eyear = date("y", strtotime($rowYear["enddate"]));
   
               $year = $syear . "-" . $eyear;
           }
   
           //get audit & ITR Data
   
           $auditDataArr = [];
   
           $idArr = explode(",", $row["audit_report_id"]);
   
           foreach ($idArr as $key => $id) {
               $sql2 = "select * from party_audit_report where id='" . $id . "'";
   
               $result2 = mysqli_query($conn, $sql2);
   
               if (mysqli_num_rows($result2) > 0) {
                   $rowAudit = mysqli_fetch_assoc($result2);
   
                   $auditDataArr[$key] = $rowAudit;
   
                   //get assessment year (finacial_year)
   
                   $fyear = "";
   
                   $sql2 =
                       "select * from financial_year where id='" .
                       $rowAudit["assessment_year_id"] .
                       "'";
   
                   $result2 = mysqli_query($conn, $sql2);
   
                   if (mysqli_num_rows($result2) > 0) {
                       $rowYear = mysqli_fetch_assoc($result2);
   
                       //get Start Year And End Year
   
                       $syear = date("Y", strtotime($rowYear["startdate"]));
   
                       $eyear = date("y", strtotime($rowYear["enddate"]));
   
                       $fyear = "AY. ".$syear . "-" . $eyear;
                   }
   
                   $auditDataArr[$key]["assessment_year"] = $fyear;
   
                   $auditDataArr[$key]["due_date"] = convertDate(
                       $rowAudit["due_date"]
                   );
               }
           }
   
           $assement_year_arr = [];
   
           $assement_year_txt = "";
   
           if (count($auditDataArr) > 0) {
               foreach ($auditDataArr as $key => $item) {
                   $assement_year_arr[] = $item["assessment_year"];
               }
   
               $assement_year_txt = implode(" and ", $assement_year_arr);
           }

		   
       } else {
           $errorMsg = "Could not Find Any Record";
       }
   
$html = <<<HTML
<html>
<head>
	<style>
	* {
		font-size: 16px;
		font-family: 'Poppins', sans-serif;
	}

	.page {
		height: 100%;
		width: 100%;
		padding: 3%;
	}

	.date {
		text-align: left;
	}

	.paragraph1 {
		/* margin-top: 10px; */
		text-align: justify;
	}

	.paragraph2 {
		margin-top: 30px;
		text-align: justify;
		margin-left: 20px;
	}

	li {
		margin-top: 20px;
		text-align: justify;
	}

	.table_section {
		margin-top: 20px;
	}

	.table2 td,
	th {
		border: 1px solid black;
		text-align: left;
		font-size: 16px;
		padding:5px;
	}

	.table2 {
		width: 100%;
		border-collapse: collapse;
	}

	.concusion_title {
		margin-top: 20px;
	}

	.footer_firm {
		margin-top: 40px;
	}

	.sign_title {
		margin-top: 3%;
	}

	.table1 {
		width: 100%;
	}

	.logo {
		width: 195px;
		height: 90px;
	}

	.head_firm_name {
		font-size: 16px;
		font-weight: bold;
	}

	.table1 {
		width: 100%;
		border: 1px solid black;
	}

	.stamp_img {
		margin-top: 10px;
		width: 150px;
		height: 100px;
	}
	</style>
</head>
<body class="page">   
HTML;
   
       /*header section end */
   
       $html .="
			<div class='main'>
				<table class='table1'>
					<tr>
						<td width='35%'>
							<img class='logo' src=" .$company_logo ." />
						</td>
						<td>
							<div class='head_firm_name'>" .$firm_name ."</div>
							<div class='head_firm_add'>" .$firm_add1 ."</div>
							<div class='head_firm_add'>" .$firm_add2 ."</div>
							<div class='head_firm_contact'>" .$email1 ."</div>
							<div class='head_firm_contact'>" .$contact1 ."</div>
							<div class='head_firm_gst'>" .$firm_gst ."</div>
						</td>
					</tr>
				</table>
				<p class='date'>Date : " .$date ."</p>
				<p style='font-size: 18px !important;'><b>Subject. – Declaration of our status with reference to Section 194Q/206AB</b></p>
				<p>Dear Sir/Madam, </p>
				<div class='paragraph1'>
					<p>This Communication is with reference to new amendments made by finance act, 2021 in TDS provision u/s 194Q and TDS at higher rate u/s 206AB.</p>
					<p>Accordingly, we hereby provide the required details.</p>
      			</div>
				
				<div class='table_section'>
					<table class='table2'>
						<tr>
							<td  style='width:50%;'>We have PAN*</td>
							<td colspan='2'>".$rowFirm['pan_no']."</td>
						</tr>
						<tr>
							<td  style='width:50%;'>TAN No.</td>
							<td colspan='2'>".$firm_tan."</td>
						</tr>
						<tr>
							<td  style='width:50%;'>GST No</td>
							<td colspan='2'>".$rowFirm['gst_in']."</td>
						</tr>
						<tr>
							<td  style='width:50%;'>We have Field ITRs of ".$assement_year_txt."</td>
							<td colspan='1'>Date </td>
							<td colspan='1'>Acknowledgement No.</td>
						</tr>";

						foreach ($idArr as $key => $id) {
							$sql2 = "select * from party_audit_report where id='" . $id . "'";
				
							$result2 = mysqli_query($conn, $sql2);
				
							if (mysqli_num_rows($result2) > 0) {
								$rowAudit = mysqli_fetch_assoc($result2);

								if($rowAudit['date_of_filling'] != "" && $rowAudit['date_of_filling'] != "0000-00-00"){
									$date = date("d-m-Y", strtotime($rowAudit['date_of_filling']));
								}else{
									$date = "";
								}

									$html.="<tr>
												<td  style='width:50%;'>";
												if(isset($assement_year_arr[$key])){
													$html.=$assement_year_arr[$key];
												}
												$html.="</td>
												<td colspan='1'>".$date."</td>
												<td colspan='1'>".$rowAudit['acknow_no']."</td>
											</tr>";
							}
						}
   
       
   
       		$html .="</table>
					<p>*in case of individual, PAN should be linked with Aadhar.</p>
					<p>Please feel free to contact us if any further clarification is required </p>
					<p>Regards,</p>
					<div class='footer_firm'>For, " .$firm_name ."</div>
					<div>
						<img class='stamp_img' src=" .$stamp_image ." />
					</div>
					<div class='sign_title'>Authorized Signature</div>
				</div>
			</div>
      
      ";
   
       $html .= "
		</body>
	</html>";

	
    
	
       $dompdf->loadHtml($html);
   
       $dompdf->set_option("isHtml5ParserEnabled", true);
   
       // Render the HTML as PDF
   
       $dompdf->render();
   
       // Output the generated PDF to Browser
   
       //$dompdf->stream();
	   
   
       date_default_timezone_set("Asia/Kolkata"); //India time (GMT+5:30)
   
       $timestamp = date("dmY_His");
   
       $filename = "tds_tcs_declaration_" . $timestamp . ".pdf";
   
       $dompdf->stream($filename, ["Attachment" => false]);

   
       include "../global_function.php";
       $data = getFileStoragePath("tds_tcs_declaration_".$module, $financiyal_year_id); //function from global_function file
       $root_path = $data[0]; // file move path
       $store_path = $data[1]; // db store path
   
	   if($module == "sales"){
			$table_indicator = 10;
	   }else{
		$table_indicator = 11;
	   }
       $check = file_put_contents($root_path . $filename, $dompdf->output());
   
       $filename = $store_path . $filename;
   
       if ($check) {
           $timestamp = date("Y-m-d H:i:s");
   
           $sql_pdf =
               "insert into pdf (table_indicator,record_id,file_name,username,created_at) 
			   value('".$table_indicator."','" .$_GET["id"] ."','" .$filename ."','" .$_SESSION["username"] ."','" .$timestamp ."')";
           mysqli_query($conn, $sql_pdf);
       }
   }
}


//  Purchase PDF Start


if(isset($_GET["module"]) && $_GET["module"] == "purchase_cotton"){

	if (isset($_GET["id"])) {
		$module = "";
		if (isset($_GET["module"])) {
			if ($_GET["module"] == "sales") {
				$module = "sales";
			} elseif ($_GET["module"] == "purchase_cotton") {
				$module = "purchase_cotton";
			}
		}
	
		$dompdf = new Dompdf();
	
		$dompdf->setPaper("A4", "portrait");
	
		$id = $_GET["id"];
	
		$sql = "SELECT * FROM `tds_tcs_declaration` where id='" . $id . "'";
	
		$result = mysqli_query($conn, $sql);
	
		if (mysqli_num_rows($result) > 0) {
			$row = [];
	
			foreach ($conn->query($sql) as $key => $result) {
				$row = $result;
			}

			$good_exceeding = "";
			if(isset($row['good_exceeding'])){
				$good_exceeding = $row['good_exceeding'];
			}

	
			//date
	
			if ($row["date"] != "" && $row["date"] != "0000-00-00") {
				$date = str_replace("-", "/", $row["date"]);
	
				$date = date("d/m/Y", strtotime($date));
			}
	
			//external party name & address
	
			$ext_sql =
				"select * from external_party where id='" .
				$row["ext_party_id"] .
				"'";
	
			$ext_result = mysqli_query($conn, $ext_sql);
	
			if (mysqli_num_rows($ext_result) > 0) {
				$ext_row = mysqli_fetch_assoc($ext_result);
	
				$ext_party = $ext_row["partyname"];
	
				$ext_gstin = $ext_row["gstin"];
	
				$ext_party_add1 =
					$ext_row["address"] . ", " . $ext_row["city"] . ",";
	
				$ext_party_add2 =
					"Dist. " .
					$ext_row["district"] .
					", " .
					$ext_row["state"] .
					" - " .
					$ext_row["pincode"];
			}
	
			//firm
	
			$firm_name = "";
	
			$firm_add1 = "";
	
			$firm_add2 = "";
	
			$email1 = "";
	
			$firm_gst = "";

			$firm_tan = "";
	
			$contact1 = "";
	
			$sqlFirm = "select * from party where id='" . $row["firm_id"] . "'";
	
			$resultFirm = mysqli_query($conn, $sqlFirm);
	
			$rowFirm = mysqli_fetch_assoc($resultFirm);
	
			if (mysqli_num_rows($resultFirm) > 0) {
				$firm_name = strtoupper($rowFirm["party_name"]);
	
				$firm_add1 =
					$rowFirm["party_address"] . ", " . $rowFirm["city"] . ",";
	
				$firm_add2 =
					"Dist. " .
					$rowFirm["district"] .
					", " .
					$rowFirm["state"] .
					" - " .
					$rowFirm["pincode"];
	
				$firm_gst = "<b>GST IN : </b>" . $rowFirm["gst_in"];

				$firm_tan = $rowFirm['tan_no'];
	
				$path = "../firm/files/logo/" . $rowFirm["logo_img"];
	
				$type = pathinfo($path, PATHINFO_EXTENSION);
	
				$data = file_get_contents($path);
	
				$company_logo =
					"data:image/" . $type . ";base64," . base64_encode($data);
	
				//stamp
	
				$path2 = "../firm/files/stamp/" . $rowFirm["stamp_img"];
	
				$type = pathinfo($path2, PATHINFO_EXTENSION);
	
				$data2 = file_get_contents($path2);
	
				$stamp_image =
					"data:image/" . $type . ";base64," . base64_encode($data2);
	
				//email
	
				$emailArr = json_decode($rowFirm["party_email"], true);
	
				if (isset($emailArr[0])) {
					$email1 = "<b>Email : </b>" . $emailArr[0];
				}

				// comtact person 
				$contact_per = $rowFirm['contact_per'];
	
				//contact no
	
				$contactArr = json_decode($rowFirm["contact_number"], true);
	
				if (isset($contactArr[0])) {
					$contact1 = "<b>Contact No. : </b>" . $contactArr[0];
					$contact2 = "Contact No. : " . $contactArr[0];
				}
			}
	
			$year = "";
	
			$sql2 =
				"select * from financial_year where id='" .
				$row["financiyal_year_id"] .
				"'";
	
			$result2 = mysqli_query($conn, $sql2);
	
			if (mysqli_num_rows($result2) > 0) {
				$rowYear = mysqli_fetch_assoc($result2);
	
				//get Start Year And End Year
	
				$syear = date("Y", strtotime($rowYear["startdate"]));
	
				$eyear = date("y", strtotime($rowYear["enddate"]));
	
				$year = $syear . "-" . $eyear;
			}
	
			//get audit & ITR Data
	
			$auditDataArr = [];
	
			$idArr = explode(",", $row["audit_report_id"]);
	
			foreach ($idArr as $key => $id) {
				$sql2 = "select * from party_audit_report where id='" . $id . "'";
	
				$result2 = mysqli_query($conn, $sql2);
	
				if (mysqli_num_rows($result2) > 0) {
					$rowAudit = mysqli_fetch_assoc($result2);
	
					$auditDataArr[$key] = $rowAudit;
	
					//get assessment year (finacial_year)
	
					$fyear = "";
	
					$sql2 =
						"select * from financial_year where id='" .
						$rowAudit["assessment_year_id"] .
						"'";
	
					$result2 = mysqli_query($conn, $sql2);
	
					if (mysqli_num_rows($result2) > 0) {
						$rowYear = mysqli_fetch_assoc($result2);
	
						//get Start Year And End Year
	
						$syear = date("Y", strtotime($rowYear["startdate"]));
	
						$eyear = date("y", strtotime($rowYear["enddate"]));
	
						$fyear = $syear . "-" . $eyear;
					}
	
					$auditDataArr[$key]["assessment_year"] = $fyear;
	
					$auditDataArr[$key]["due_date"] = convertDate(
						$rowAudit["due_date"]
					);
				}
			}
	
			$assement_year_arr = [];
	
			$assement_year_txt = "";
	
			if (count($auditDataArr) > 0) {
				foreach ($auditDataArr as $key => $item) {
					$assement_year_arr[] = $item["assessment_year"];
				}
	
				$assement_year_txt = implode(" and ", $assement_year_arr);
			}
		} else {
			$errorMsg = "Could not Find Any Record";
		}
$html = <<<HTML
<html>
<head>
	<style>
	* {
		font-size: 14px;
		font-family: 'Poppins', sans-serif;
	}

	.page {
		height: 100%;
		width: 100%;
		padding: 3%;
	}

	.date {
		text-align: right;
	}

	.paragraph1 {
		/* margin-top: 10px; */
		text-align: justify;
	}

	.paragraph2 {
		margin-top: 30px;
		text-align: justify;
		margin-left: 20px;
	}

	li {
		margin-top: 10px;
		text-align: justify;
	}

	.table_section {
		margin-top: 20px;
	}

	.table2 td,
	th {
		border: 1px solid black;
		text-align: left;
		font-size: 16px;
		padding:5px;
	}

	.table2 {
		width: 100%;
		border-collapse: collapse;
	}

	.concusion_title {
		margin-top: 20px;
	}

	.footer_firm {
		margin-top: 10px;
	}

	.sign_title {
		margin-top: 1%;
	}

	.table1 {
		width: 100%;
	}

	.logo {
		width: 195px;
		height: 90px;
	}

	.head_firm_name {
		font-size: 16px;
		font-weight: bold;
	}

	.table1 {
		width: 100%;
		border: 1px solid black;
	}

	.stamp_img {
		margin-top: 10px;
		width: 150px;
		height: 100px;
	}
	</style>
</head>
<body class="page">   
HTML;
	
		/*header section end */
	
		$html .="
			 <div class='main'>
				 <table class='table1'>
					 <tr>
						 <td width='35%'>
							 <img class='logo' src=" .$company_logo ." />
						 </td>
						 <td>
							 <div class='head_firm_name'>" .$firm_name ."</div>
							 <div class='head_firm_add'>" .$firm_add1 ."</div>
							 <div class='head_firm_add'>" .$firm_add2 ."</div>
							 <div class='head_firm_contact'>" .$email1 ."</div>
							 <div class='head_firm_contact'>" .$contact1 ."</div>
							 <div class='head_firm_gst'>" .$firm_gst ."</div>
						 </td>
					 </tr>
				 </table>
				<p style='font-size: 18px !important;text-align:center;'><b>Annexure-1</b></p>

				<p class='date'>Date : " .$date ."</p>
				<p style='font-size: 16px !important;'><b>Sub : Applicability of TDS  under section 194Q of the Income Tax Act, 1961 on Purchase of Goods w.e.f. 1st July 2021</b></p>
				<p>Dear Sir/Madam, </p>
				<p>We hereby declare that. : </p>

				<ol type='1'>

				<li style='text-align:justify;'>Our Sales turnover during financial year ".$year." exceeded ".$row['turnover'].". In view of this fact. We are liable to deduct TDS as per provisions of section 194Q of the Income Tax Act. Please refer to point no.4 for the rates.</li>

				<li style='text-align:justify;'>We shall deduct TDS u/s. 194Q on receipt of purchase invoices or on basis of Advance payment made to you, whichever is earlier on or after 1st July 2021.</li>

				<li style='text-align:justify;'>You are requested not to charge TCS u/s. 206c (1H) of the Income Tax Act,1961 on your invoices issued in our favor from 1st “July,2021 on wards.</li>

				<li style='text-align:justify;'>Rate of TDS :
					<ol type='i'>
						<li style='text-align:justify;'>The Rate of TDS is 0.1% on purchase of goods by us in excess of Rs.".$good_exceeding." in the current financial year in normal circumstances.</li>
						<li style='text-align:justify;'>However, New Section 206AB(TDS) and section 206CCA(TCS) to be applicable from 1st July 2021 which have specified higher rate of TDS/TCS on payment of specified persons as mentioned in (iii)</li>
						<li style='text-align:justify;'>The Rate of  TDS shall be at the double of the normal rate or 5%, whichever is higher for all payment to be made to the vendors who has not field income tax return for immediately two previous year and total TDS deducted for each year was Rs. 50,000/- or more.</li>
						<li style='text-align:justify;'>Therefore, in order to decide the rate of TDS, we need to get details as per annexure A.</li>
					</ol>
				</li>

				<li style='text-align:justify;'>For any query you may please contact Mr.".$contact_per." ".$contact2."</li>
			</ol>

			<br/>
			<p>
				Your faithfully 
			</p>	
			<div class='footer_firm'>For, " .$firm_name ."</div>
			<div>
				<img class='stamp_img' src=" .$stamp_image ." />
			</div>
			<div class='sign_title'>Authorized Signature</div>
		</div>
	</div>
	   ";
	
		$html .= "
		 </body>
	 </html>";


	 
	 
		$dompdf->loadHtml($html);
	
		$dompdf->set_option("isHtml5ParserEnabled", true);
	
		// Render the HTML as PDF
	
		$dompdf->render();
	
		// Output the generated PDF to Browser
	
		//$dompdf->stream();
		
	
		date_default_timezone_set("Asia/Kolkata"); //India time (GMT+5:30)
	
		$timestamp = date("dmY_His");
	
		$filename = "tds_tcs_declaration_" . $timestamp . ".pdf";
	
		$dompdf->stream($filename, ["Attachment" => false]);
	
		include "../global_function.php";
		$data = getFileStoragePath("tds_tcs_declaration_purchase", $financiyal_year_id); //function from global_function file
		$root_path = $data[0]; // file move path
		$store_path = $data[1]; // db store path
	
		if($module == "sales"){
			 $table_indicator = 10;
		}else{
		 $table_indicator = 11;
		}
		$check = file_put_contents($root_path . $filename, $dompdf->output());
	
		$filename = $store_path . $filename;
	
		if ($check) {
			$timestamp = date("Y-m-d H:i:s");
	
			$sql_pdf =
				"insert into pdf (table_indicator,record_id,file_name,username,created_at) 
				value('".$table_indicator."','" .$_GET["id"] ."','" .$filename ."','" .$_SESSION["username"] ."','" .$timestamp ."')";
			mysqli_query($conn, $sql_pdf);
		}
	}
 }
   
   ?>