<?php

session_start();

require_once('../db.php');

require_once '../PHPLibraries/PHPWord/autoload.php';

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
		if (isset($_GET['id'])) 
		{
			$module = "";
			if (isset($_GET["module"])) {
				if ($_GET["module"] == "sales") {
					$module = "sales";
				} elseif ($_GET["module"] == "purchase_cotton") {
					$module = "purchase_cotton";
				}
			}

			$sql = "SELECT * FROM `tds_tcs_declaration` where id='" . $_GET['id'] . "'";
			$result = mysqli_query($conn, $sql);
			if (mysqli_num_rows($result) > 0) {
				$row = array();
				foreach ($conn->query($sql) as $key => $result) {
					$row = $result;
				}
				$good_exceeding = "";
				if(isset($row['good_exceeding'])){
					$good_exceeding = $row['good_exceeding'];
				}
				//date
				if ($row['date'] != '' && $row['date'] != '0000-00-00') {
					$date = str_replace('-', '/', $row['date']);
					$date = date('d/m/Y', strtotime($date));
				}
				//external party name & address
				$ext_sql = "select * from external_party where id='" . $row['ext_party_id'] . "'";
				$ext_result = mysqli_query($conn, $ext_sql);
				if (mysqli_num_rows($ext_result) > 0) {
					$ext_row = mysqli_fetch_assoc($ext_result);
					$ext_party = $ext_row['partyname']; 
				}
				//firm
				$firm_name = '';
				$firm_add1 = '';
				$firm_add2 = '';
				$email1 = '';
				$contact_per = "";
				$contact1 = '';

				$sqlFirm = "select * from party where id='" . $row['firm_id'] . "'";
				$resultFirm = mysqli_query($conn, $sqlFirm);
				$rowFirm = mysqli_fetch_assoc($resultFirm);
				if (mysqli_num_rows($resultFirm) > 0) {
					$firm_name = strtoupper($rowFirm['party_name']);
					
					//contact person
					
					if(isset($rowFirm['contact_per'])){
						$contact_per = $rowFirm['contact_per'];
					}

					//contact no
					$contactArr = json_decode($rowFirm['contact_number'], true);
					if (isset($contactArr[0])) {
						$contact1 = 'Mobile No. : ' . $contactArr[0];
					}
					
				}

				$year = '';
				$sql2 = "select * from financial_year where id='" . $row['financiyal_year_id'] . "'";
				$result2 = mysqli_query($conn, $sql2);
				if (mysqli_num_rows($result2) > 0) {
					$rowYear = mysqli_fetch_assoc($result2);
					//get Start Year And End Year
					$syear = date("Y", strtotime($rowYear['startdate']));
					$eyear = date("y", strtotime($rowYear['enddate']));
					$year = $syear . '-' . $eyear;
				}
			}

			$pw = new \PhpOffice\PhpWord\PhpWord();

			$section = $pw->addSection();

			$pw->setDefaultParagraphStyle(

			array(

			//'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,

			'align'=>'both',

			'spacing' => 200,

			'lineHeight' => 1,

			)

			);
		


		//date 

		$pw->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>10));

		$pw->addParagraphStyle('p2Style', array('align'=>'right', 'spaceAfter'=>100));

		// $section->addText('Date :  ', 'r2Style', 'p2Style');





		$html="

		<html>

		<head>

		</head>
		<body style='font-size: 15px;font-family: sans-serif;'> ";

		/*header section end */

		$html.="

			<p style='text-align:center;'><b>Annexure-1</b></p>
			<br/>
			<p style='text-align:right;'>Date : " .$date ."</p>
			<br/>
			<p><b>Sub : Applicability of TDS  under section 194Q of the Income Tax Act, 1961 on Purchase of Goods w.e.f. 1st July 2021</b></p>
			<br/>
			<p>Dear Sir/Madam, </p>
			<br/>
			<p style='text-align:left;'>We hereby declare that. : </p>
			<br/>

			<ol type='1'>
				<li style='text-align:justify;'>Our Sales turnover during financial year ".$year." exceeded ".$row['turnover'].". In view of this fact. We are liable to deduct TDS as per provisions of section 194Q of the Income Tax Act. Please refer to point no.4 for the rates.</li>
				<li style='text-align:justify;'>We shall deduct TDS u/s. 194Q on receipt of purchase invoices or on basis of Advance payment made to you, whichever is earlier on or after 1st July 2021.</li>
				<li style='text-align:justify;'>You are requested not to charge TCS u/s. 206c (1H) of the Income Tax Act,1961 on your invoices issued in our favor from 1st “July,2021 on wards.</li>
				<li style='text-align:left;'>Rate of TDS :
				<br/><p style='text-align:left !important;'>The Rate of TDS is 0.1% on purchase of goods by us in excess of Rs.".$good_exceeding." in the current financial year in normal circumstances.</p>
					<ol type='i'>
						<li style='text-align:justify;'>However, New Section 206AB(TDS) and section 206CCA(TCS) to be applicable from 1st July 2021 which have specified higher rate of TDS/TCS on payment of specified persons as mentioned in (iii)</li>
						<li style='text-align:justify;'>The Rate of  TDS shall be at the double of the normal rate or 5%, whichever is higher for all payment to be made to the vendors who has not field income tax return for immediately two previous year and total TDS deducted for each year was Rs. 50,000/- or more.</li>
						<li style='text-align:justify;'>Therefore, in order to decide the rate of TDS, we need to get details as per annexure A.</li>
					</ol>
				</li>
				<li style='text-align:justify;'>For any query you may please contact Mr. ".$contact_per." ".$contact1."</li>
			</ol>
			<br/>
			<div>
				<p>Your faithfully </p>
			</div>
			<br/>

			<div>For, " .$ext_party ."</div>
			<br/>
			<br/>
			<br/>
			<br/>
			<div>Authorized Signature</div>
			<div>Name of company</div>
			<div>Seal of company</div>
		";

		$html.='</body></html>';

		\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

		// 1. Basic table

		$tableStyle = array(
			'borderColor' => 'black',
			'borderSize'  => 6,
			'cellMargin'  => 50,
			'width'=>100,
		);

		$firstRowStyle = array('bgColor' => 'white');
		// $pw->addTableStyle('tbl1', $tableStyle, $firstRowStyle);
		//concultion

		$timestamp1=date("dmY_His");
		$filename="tds_tcs_declaration_".$module.$timestamp1.".docx";


		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment;filename=".$filename);

		/*$path="word_files/";

		$check=file_put_contents($pw->save($path.$filename, "Word2007"));

		*/

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($pw, 'Word2007');
		$objWriter->save('php://output');

	}
}



//  Purchase Docs start


if(isset($_GET["module"]) && $_GET["module"] == "purchase_cotton"){
	if (isset($_GET['id'])) 
	{
		$module = "";
		if (isset($_GET["module"])) {
			if ($_GET["module"] == "sales") {
				$module = "sales";
			} elseif ($_GET["module"] == "purchase_cotton") {
				$module = "purchase_cotton";
			}
		}

		$sql = "SELECT * FROM `tds_tcs_declaration` where id='" . $_GET['id'] . "'";
		$result = mysqli_query($conn, $sql);
		if (mysqli_num_rows($result) > 0) {
			$row = array();
			foreach ($conn->query($sql) as $key => $result) {
				$row = $result;
			}
			//date
			if ($row['date'] != '' && $row['date'] != '0000-00-00') {
				$date = str_replace('-', '/', $row['date']);
				$date = date('d/m/Y', strtotime($date));
			}
			//external party name & address
			$ext_sql = "select * from external_party where id='" . $row['ext_party_id'] . "'";
			$ext_result = mysqli_query($conn, $ext_sql);
			if (mysqli_num_rows($ext_result) > 0) {
				$ext_row = mysqli_fetch_assoc($ext_result);
				$ext_party = $ext_row['partyname']; 
			}
			//firm
			$firm_name = '';
			$firm_add1 = '';
			$firm_add2 = '';
			$email1 = '';
			$contact_per = "";
			$contact1 = '';

			$sqlFirm = "select * from party where id='" . $row['firm_id'] . "'";
			$resultFirm = mysqli_query($conn, $sqlFirm);
			$rowFirm = mysqli_fetch_assoc($resultFirm);
			if (mysqli_num_rows($resultFirm) > 0) {
				$firm_name = strtoupper($rowFirm['party_name']);
				
				//contact person
				
				if(isset($rowFirm['contact_per'])){
					$contact_per = $rowFirm['contact_per'];
				}

				//contact no
				$contactArr = json_decode($rowFirm['contact_number'], true);
				if (isset($contactArr[0])) {
					$contact1 = 'Mobile No. : ' . $contactArr[0];
				}
				
			}

			$year = '';
			$sql2 = "select * from financial_year where id='" . $row['financiyal_year_id'] . "'";
			$result2 = mysqli_query($conn, $sql2);
			if (mysqli_num_rows($result2) > 0) {
				$rowYear = mysqli_fetch_assoc($result2);
				//get Start Year And End Year
				$syear = date("Y", strtotime($rowYear['startdate']));
				$eyear = date("y", strtotime($rowYear['enddate']));
				$year = $syear . '-' . $eyear;
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

		}

		$pw = new \PhpOffice\PhpWord\PhpWord();

		$section = $pw->addSection();

		$pw->setDefaultParagraphStyle(

		array(

		//'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT,

		'align'=>'both',

		'spacing' => 200,

		'lineHeight' => 1,

		)

		);
	


		//date 

		$pw->addFontStyle('r2Style', array('bold'=>false, 'italic'=>false, 'size'=>10));

		$pw->addParagraphStyle('p2Style', array('align'=>'right', 'spaceAfter'=>100));

		// $section->addText('Date :  ', 'r2Style', 'p2Style');

		$html="

		<html>
		<head>
		</head>
		<body style='font-size: 15px;font-family: sans-serif;'> ";

		/*header section end */

		$html.="
		<br/>
			<p>Date : " .$date ."</p>
			<br/>
			<p><b>Subject. – Declaration of our status with reference to Section 194Q/206AB</b></p>
			<br/>
			<p>Dear Sir/Madam, </p>
			<br/>
			<p style='text-align:left;'>This Communication is with reference to new amendments made by finance act, 2021 in TDS provision u/s 194Q and TDS at higher rate u/s 206AB.</p>
			<br/>
			<p style='text-align:left;'>Accordingly, we hereby provide the required details.</p>
			<br/>
			

			<table class='table2' style='width: 100%;border-collapse: collapse;'>
				<tr>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=50% !important;'>We have PAN*</td>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px;' colspan='2'></td>
				</tr>
				<tr>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px;  width=50% !important;'>TAN No.</td>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px;' colspan='2'></td>
				</tr>
				<tr>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px;  width=50% !important;' >GST No</td>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px;' colspan='2'></td>
				</tr>
				<tr>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=50% !important;'>We have Field ITRs of ".$assement_year_txt."</td>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=25%' >Date </td>
					<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=25%' >Acknowledgement No.</td>
				</tr>";

				foreach($assement_year_arr as $year){
					$html.="<tr>
						<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=50% !important;' >".$year."</td>
						<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=25%' ></td>
						<td style='border: 1px solid black;text-align: left;font-size: 14px;padding:5px; width=25%' ></td>
					</tr>";
				}

				$html.="</table>

			<br/>
				<p>*in case of individual, PAN should be linked with Aadhar.</p>
				<br/>
				<p>Please feel free to contact us if any further clarification is required Regards,</p>
				<br/>

			<div>For, " .$ext_party ."</div>
			<br/>
			<br/>
			<br/>
			<br/>
			<br/>
			<p>Authorized Signature</p>
			<p>Name of company</p>
			<p>Seal of company</p>
		";

		$html.='</body></html>';


		\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

		// 1. Basic table

		$tableStyle = array(
			'borderColor' => 'black',
			'borderSize'  => 6,
			'cellMargin'  => 50,
			'width'=>100,
		);

		$firstRowStyle = array('bgColor' => 'white');
		// $pw->addTableStyle('tbl1', $tableStyle, $firstRowStyle);
		//concultion

		$timestamp1=date("dmY_His");
		$filename="tds_tcs_declaration_".$module.$timestamp1.".docx";


		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment;filename=".$filename);

		$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($pw, 'Word2007');
		$objWriter->save('php://output');

	}
}

?>