<?php
/*
Скрипт повертає id зареєстрованого користувача або додає нового і повертає його id
*/
//header("Content-type: text/html;charset=utf-8");

include('connection.php');
  
$login=$_POST['login'];
//$pass=$_POST['pass'];

		// перевіряємо чи такий юзер є в базі
		$res = mysql_query("SELECT * FROM users WHERE login='$login'");
		$row = mysql_num_rows($res);
        if($row>=1) {
			//echo "Такий логін є в системі !";
			echo "1";
		} else {
			echo "2";
		}

?> 