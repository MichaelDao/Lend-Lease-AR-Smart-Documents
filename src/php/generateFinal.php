<?php

require "sql_header.php";


$docuCreate = "INSERT INTO Document (name, DocumentType) VALUES ";
$docuDataInsert = "INSERT INTO DocumentData (documentID, dataType, dataLocation) VALUES ";


$documentName = $_POST['title'];
$documentType = "1";



//echo "document title: ".$documentName;


if ($conn->query($docuCreate."( '".$documentName."', '".$documentType."' )") == TRUE)
{    
	$last_id = $conn->insert_id;
//	echo "New record created successfully. Last inserted ID is: " . $last_id;
} 
else
    echo "Error: " . $conn->error;

$documentID = $last_id;


$text_array = $_POST['text_field'];

$text_array[] = "<h4>Incident Circumstance: ".$_POST['incidentCircumstance']."</h4>";
$text_array[] = "<h4>Incident No: ".$_POST['incidentNo']."</h4>";
$text_array[] = "<h4>Issued by: ".$_POST['docAuthor']."</h4>";
$text_array[] = "<h4>Incident Date: ".$_POST['incidentDate']."</h4>";
$text_array[] = "<h4>Description of incident: ".$_POST['descriptionDoc']."</h4>";
$text_array[] = "<h4>Key Learning: ".$_POST['keyLearning']."</h4>";
$text_array[] = "<h4>Conclusion: ".$_POST['conclusionDoc']."</h4>";

$dataLocation = "/srv/files/";
foreach ($text_array as $text)
{
	//echo "<br> TEXT: $text<br>";
	$hash = hash("md5", $text);
	$thisDataLocation = $dataLocation.$hash;
	file_put_contents($thisDataLocation, $text);
	if ($conn->query($docuDataInsert."( '".$documentID."', 0, '".$thisDataLocation."' )") == TRUE)
	{    
		$last_id = $conn->insert_id;
		//echo "New record created successfully. Hash: ".$hash."Last inserted ID is: " . $last_id;
	} 
	else
		;
	//	echo "Error: " . $conn->error;
}



//echo "FILES: <br/>";
//var_dump($_FILES);
//echo "POST: <br/>";
//var_dump($_POST);

$fileDataLocation = "/srv/hack/srv/files/";
foreach ($_FILES['file_upload']['tmp_name'] as $file) // TODO: MAKE THIS
{
	echo "<br> file: $file<br>";
	$hash = hash("md5", $file);
	$thisDataLocation = $fileDataLocation.$hash;
	
	// MOVE FILE TO LOCATION
//	echo "File uploaded to: ".$file."<br/>";
	move_uploaded_file($file, $thisDataLocation);
//	echo "Moved file to $thisDataLocation<br/>";

	if ($conn->query($docuDataInsert."( '".$documentID."', 1, '".$dataLocation.$hash."' )") == TRUE)
	{    
		$last_id = $conn->insert_id;
//		echo "New record created and file uploaded successfully. Hash: ".$hash."Last inserted ID is: " . $last_id;
	} 
	else
		;
//		echo "Error: " . $conn->error;
}



echo "<h1>Document Generated</h1>";

?>
