<?php
header("Content-type: text/html;charset=utf-8");

print_r(PDO::getAvailableDrivers());

try {  
  # MS SQL Server � Sybase ����� PDO_DBLIB  
  $host = "obl-devel";
  $dbname = "Halls";
  $user = "sa";
  $pass = "Gjdybq<h'l?55";
  $DBH = new PDO("sqlsrv:Server=$host;Database=$dbname;", $user, $pass);   
}  
catch(PDOException $e) {  
    echo $e->getMessage();  
}



$query = "select author from books";
$STH = $DBH->query($query);
while ($row = $STH->fetch()) {
     print_r($row['author']."\n");
}
Прошу

?>