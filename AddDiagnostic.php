<?php
require "conn.php";
set_include_path('.;C:\wamp\bin\php\php5.5.12\pear');
include('Crypt/AES.php');
include('Crypt/RSA.php');
include('Crypt/Random.php');
include('Math/BigInteger.php');
$client_response = $_POST["client_response"];

//Decrypt Data		
//---------------------------------------------------------------
	$cipher = new Crypt_AES(CRYPT_AES_MODE_ECB);
	$symmetricKey = file_get_contents('C:\wamp\www\symmetric.txt');
	$cipher->setKey($symmetricKey);
	$decryptedData = $cipher->decrypt(base64_decode($client_response));
//---------------------------------------------------------------	

$obj = json_decode($decryptedData);

$tag_id = $obj->{"tag_id"};
$doctor_tag = $obj->{"doctor_tag"};
$description = $obj->{"description"};

$patient_id = "";
$doctor_id = "";

$mysql_qry1 = "SELECT `patient_id` FROM `patient` WHERE tag_id='$tag_id'";

if ($result1=mysqli_query($conn,$mysql_qry1))
{
  while($row1=mysqli_fetch_row($result1))
    {
		$patient_id = $row1[0];
	}
}	

$mysql_qry2 = "SELECT `doctor_id` FROM `doctor` WHERE tag_id='$doctor_tag'";

if ($result2=mysqli_query($conn,$mysql_qry2))
{
  while($row2=mysqli_fetch_row($result2))
    {
		$doctor_id = $row2[0];
	}
}

$curr_date = date("Y-m-d");

$mysql_qry = "INSERT INTO `hospital`.`diagnostics` (`diagnostic_id`, `date`, `doctor_id`, `patient_id`, `description`) VALUES (NULL, '$curr_date', '$doctor_id', '$patient_id', '$description')";

if ($result=mysqli_query($conn,$mysql_qry))
{
	echo "success";
}
mysqli_close($conn);
?>