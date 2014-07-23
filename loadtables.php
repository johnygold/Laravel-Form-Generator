<?php

$database = $_POST['database'];

$dbh = new PDO("mysql:host=localhost;user=root;dbname=$database");
$statement = $dbh->query('SHOW TABLES');
$tableList = $statement->fetchAll();


//Maak nieuwe array uit de geneste data
foreach($tableList as $key => $value)
{
	foreach($value as $key2 => $value2)
	{
		$tableListNew[] = $value2;
	}
}

$tableList = array_unique($tableListNew);
echo json_encode($tableList);




?>