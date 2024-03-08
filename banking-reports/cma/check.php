<?php
if(isset($_POST['submit'])){

	//print_r($_FILES['img']['name']);

	$var1=['1','2','3'];
	echo '<pre>';
	print_r($var1);
	echo '</pre>';

	$names=$_FILES['img']['name'];

	echo '<pre>';
	print_r($names);
	echo '</pre>';



	foreach ($names as $key => $value) {

		if($value[0]!='')
		{
		  $var1[$key-1]=$value[0];
		}
		
	}

	echo '<pre> <br><b>After</b><br>';
	print_r($var1);
	echo '</pre>';

}

?>
<form method="post" enctype="multipart/form-data">
<input type="file" name="img[1][]">
<input type="file" name="img[2][]">
<input type="file" name="img[3][]">
<input type="file" name="img[4][]">
<input type="submit" value="submit" name="submit">
</form>