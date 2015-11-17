

<?php
define ('DB_HOST', 'localhost');
define ('DB_LOGIN', 'root');
define ('DB_PASSWORD', '');
define ('DB_NAME', 'cod_zvity');
$mysql_connect = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die("MySQL Error: " . mysql_error());
mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die ("MySQL Error: " . mysql_error());
//mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
mysql_select_db(DB_NAME) or die ("<br>Invalid query: " . mysql_error());

  
  
echo '<table border=1>' ; 
  
  
  
    $query = mysql_query("SELECT * FROM users WHERE perm = 1") or die("Извините, произошла ошибка");
    while ($row = mysql_fetch_array($query)) {
		echo '<tr><td>';
		echo "(".$row['id'].")".$row['name']."<br>";
		echo '</td>';

		$query1 = mysql_query("SELECT * FROM permissions 
LEFT JOIN users ON permissions.perm_user_id = users.id
WHERE permissions.user_id = ".$row['id']) or die("Извините, произошла ошибка");
				echo '<td>&nbsp;';
		while ($row1 = mysql_fetch_array($query1)) {

			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row1['name']."<br>";


		
		}		echo '</td>';
		echo '</tr>';
		
		}
	

  
  
  
  
  
  
?>



</table>	