<?php
namespace Dompdf;
require_once 'dompdf/dompdf/autoload.inc.php';
ob_start();
?>

<html>
<style>
body {
  padding: 4px;
  text-align: center;
  background-color:#09A8F7;
  font-size:22px;
}

p{
color:#fff;

}
</style>
<h2>PHPGurukul | Programming Blog</h2>
<p>Welcome to PHGurukul</p>

<p>This is Anuj Kumar. I’m a professional web developer with 3+ year experience.I write blogs in my free time. I love to learn new technologies and share with others.</p>
<p>
I founded PHPGurukul in September 2015. The main aim of this website to is provide php , jquery , mysql , phpoops and other web development tutorials.</p>

<p>
At this website I also provide PHP’s projects for beginners and professionals. There are more than 10+ projects.</p>

<p>
I am trying best effort to make PHPGurukul useful for every single moment spend on this website. If you think this website is useful to visit please share with your friend and buddies.</p>


<?php
$html = ob_get_clean();
$dompdf = new DOMPDF();
$dompdf->setPaper('A4', 'landscape');
$dompdf->load_html($html);
$dompdf->render();
//For view
$dompdf->stream("",array("Attachment" => false));
// for download
//$dompdf->stream("sample.pdf");
?>