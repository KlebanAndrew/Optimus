

<?php
define ('DB_HOST', 'localhost');
define ('DB_LOGIN', 'root');
define ('DB_PASSWORD', '');
define ('DB_NAME', 'cod_zvity');
$mysql_connect = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die("MySQL Error: " . mysql_error());
mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD) or die ("MySQL Error: " . mysql_error());
//mysql_query("set names utf8") or die ("<br>Invalid query: " . mysql_error());
mysql_select_db(DB_NAME) or die ("<br>Invalid query: " . mysql_error());
?>


<?php
  function view_tree() {
    $query = mysql_query("SELECT * FROM `struktura`") or die("Извините, произошла ошибка");
    while ($row = mysql_fetch_row($query)) {
      if ($row[1] == '0') {
        $one_lvl[] = array ($row[0], $row[1], $row[2], $row[3]);
      } else {
        $next_lvl[] = array ($row[0], $row[1], $row[2], $row[3]);
      }
    }
    print '<ul class="tree_lvl_1">';
    foreach ($one_lvl as $key){
      print '<li><a id="'.$key[3].'">'.$key[2].'</a>';
    view_tree_next_level($key[0], $next_lvl);
      print '</li>';
    }
    print '</ul>';
  }

  
  

  

  function view_tree_next_level($family, $next_lvl) {
    foreach ($next_lvl as $key) {
      if ($key[1]==$family) {
        print '<ul class="tree_lvl_2"><li><a id="'.$key[3].'">'.$key[2].'</a>';
        view_tree_next_level($key[0], $next_lvl);
        print '</li></ul>';
      }
    }
  }
  
  
  
  
  
  
  
  
  
    $query = mysql_query("SELECT * FROM users WHERE d1<> ''") or die("Извините, произошла ошибка");
    while ($row = mysql_fetch_array($query)) {
		echo $row['name']."<br>";
		

		$query1 = mysql_query("SELECT * FROM users WHERE d2 = ".$row['d1']) or die("Извините, произошла ошибка");
		while ($row1 = mysql_fetch_array($query1)) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$row1['name']."<br>";
		
		}

		
		}
	

  
  
  
  
  
  
?>



	