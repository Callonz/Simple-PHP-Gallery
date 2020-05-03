<?php
##################################################
# Simple PHP Gallery
# Made by Callonz
# Version 1.2
# https://github.com/Callonz/Simple-PHP-Gallery/
##################################################
include 'config.php';
if(isset($_POST['delete']) && $ALLOWDELETION){
	del_file($_POST['delete']);
}
if(isset($_POST['amount'])){ //Checking if user has sorted, if not, uses default value from config
  $sort = $_POST['sortno'];
}else{
  $sort=$DEFAULTNUMBEROFFILES;
}
$arr_img = array();
$totalsize = 0;
$folders = $FILEPATH;
if(isset($_POST["checkedfilePaths"])){
	$newFilepath = [];
	foreach($folders as $path){
		unset($folders);
		if (in_array($path,$_POST["checkedfilePaths"])){
			array_push($newFilepath,$path);
		}
	}
	$folders = $newFilepath;
}
foreach($folders as $dir){
	if ($handle = opendir($dir)) {
		array_push($DISALLOW,basename(__FILE__)); //adding self to list of disallowed items
		while (false !== ($entry = readdir($handle))) {
			if (!in_array($entry, $DISALLOW) && !is_dir($entry)) {			
				$arrayname = array($dir.$entry => filemtime($dir.$entry));
				$arr_img += $arrayname;
				$totalsize += filesize($dir.$entry);
			}
		}
		closedir($handle);
	}
}
uasort($arr_img, 'cmp'); //Sorting the Array by Date
 ?>
<html>
<head>
<title><?php echo $TITLE ?></title>
<style>
p,button {
    font-family: "Lucida Sans Unicode", Lucida Grande, sans-serif;
    font-size: 15;
}
td, th {
    font-family: "Lucida Sans Unicode", Lucida Grande, sans-serif;
    font-size: 15;
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
<p>There are <?php echo sizeof($arr_img);?> items in this gallery, taking up <?php echo human_filesize($totalsize);?>.</p>
<form method="post">
<?php
if (sizeof($FILEPATH)>1){
	echo '<p>Only show filed from these directories: ';
	if(isset($_POST["checkedfilePaths"])){
		foreach($FILEPATH as $path){
			if(in_array($path, $_POST["checkedfilePaths"])){
				echo '<input type="checkbox" name="checkedfilePaths[]" value="'.$path.'" checked>'.$path;
			}else{
				echo '<input type="checkbox" name="checkedfilePaths[]" value="'.$path.'">'.$path;
			}
		}
		
	}else{
		foreach($FILEPATH as $path){
			echo '<input type="checkbox" name="checkedfilePaths[]" value="'.$path.'" checked>'.$path;
		}
	}
	echo '<input type="checkbox" name="checkedfilePaths[]" value="" hidden checked></p>';
}
?>
<p>Number of items to display:


<select name = "sortno">
<?php 

foreach($NUMBEROFFILES as $limit){
	echo '<option ';
	if ($sort == $limit){echo "selected='selected'";} 
	echo ' value="'.$limit.'">'.$limit.'</option>';
} ?>
<option <?php if ($sort == "All" || $sort==0){echo "selected='selected'";}?> value="All">All</option>
</select>
<input type='submit' name='amount' value='Change'/>
</p>
</form>
<table>
  <tr><th>No.</th><th>Preview</th><th>Name</th><th>Date</th><th>Size</th><?php if($ALLOWDELETION){echo'<th>Delete</th>';}?></tr>
<?php

$sort_no=0;
foreach ($arr_img as $key => $value) {
	$filetype = explode(".", $key);
	echo "<tr><td>".($sort_no+1)."<td><a target='_blank' href='./".$key."'>";
	
	$arr_size = sizeof($filetype) -1;
	$file_ext =strtolower($filetype[$arr_size]);
	if(in_array($file_ext, $PREVIEWEXTENSIONS)) {
		echo "<img src='".$key."'></img>";
	}else{
		echo "<p>No preview available.</p>";
	}
	echo "</a></td><td><a target='_blank' href='./".$key."'>".$key."</a></td>
		<td>".date("F d Y H:i:s",$value)."</td><td>".human_filesize(filesize($key))."</td>";
	if($ALLOWDELETION){echo "<td><form method='POST'><button type='submit' name='delete' value='".$key."'/>Delete</button></form></td>";}
	echo "</tr>";
    if($sort<>0 && $sort<>"All" && $sort_no>=($sort-1)){ //dirty method of stopping the loop at the wanted maximum 
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
