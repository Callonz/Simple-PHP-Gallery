<?php
if(isset($_POST['delete'])){
	del_file($_POST['delete']);
}
if(isset($_POST['amount'])){
  $sort = $_POST['sortno'];
}else{
  $sort=25;
}
if ($handle = opendir('.')) {
	$arr_img = array();
  while (false !== ($entry = readdir($handle))) {
  if ($entry != "." && $entry != ".." && $entry != basename(__FILE__) && !is_dir($entry)) {
      $arrayname = array($entry => filemtime($entry));
      $arr_img += $arrayname;
      }
  }
	closedir($handle);
}
uasort($arr_img, 'cmp'); //Sorting the Array by Date
 ?>
<html>
<head>
<title>Simple PHP Gallery</title>
<style>
td, th {
    border: 1px solid #ddd;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2;}

tr:hover {background-color: #ddd;}

th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #797a79;
    color: white;
}
img{
    max-width:200;
    max-height:200px;
}
</style>
</head>
<body>
Total number of items:<?php echo sizeof($arr_img);?>
<form method="post">
Number of items to display:
<select name = "sortno">
 <option <?php if ($sort == 25){echo "selected='selected'";}?> value="25">25</option>
 <option <?php if ($sort == 50){echo "selected='selected'";}?> value="50">50</option>
 <option <?php if ($sort == 100){echo "selected='selected'";}?> value="100">100</option>
 <option <?php if ($sort == 150){echo "selected='selected'";}?> value="150">150</option>
 <option <?php if ($sort == 250){echo "selected='selected'";}?> value="250">250</option>
 <option <?php if ($sort == "All" || $sort==0){echo "selected='selected'";}?> value="All">All</option>
</select>
<input type='submit' name='amount' value='Change'/>
</form>
<table>
  <tr><th>No.</th><th>Preview</th><th>Name</th><th>Date</th><th>Size</th><th>Delete</th></tr>
<?php

$sort_no=0;
foreach ($arr_img as $key => $value) {
	$filetype = explode(".", $key);
	echo "<tr><td>".($sort_no+1)."<td><a target='_blank' href='./".$key."'>";
	$preview_extensions = array('png', 'jpg', 'jpeg', 'gif');
	$arr_size = sizeof($filetype) -1;
	$file_ext =strtolower($filetype[$arr_size]);
	if(in_array($file_ext, $preview_extensions)) {
		echo "<img src=".$key."></img>";
	}else{
		echo "<p>No preview available.</p>";
	}
	echo "</a></td><td><a target='_blank' href='./".$key."'>".$key."</a></td>
		<td>".date("F d Y H:i:s",$value)."</td><td>".human_filesize(filesize($key))."</td><td><form method='POST'><button type='submit' name='delete' value='".$key."'/>Delete</button>
		</form></td></tr>";
    if($sort<>0 && $sort<>"All" && $sort_no>=($sort-1)){
       break;
    }
    $sort_no++;
}
echo "</table>";
function del_file($file){
  if(file_exists($file)){
	   unlink($file);
  }
}
function cmp($a, $b) {
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}
function makeprivate($file){
	//TODO
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}
?>
</body>
</html>
