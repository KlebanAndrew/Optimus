<?php
/*
������ ������� id �������������� ����������� ��� ���� ������ � ������� ���� id
*/
//header("Content-type: text/html;charset=utf-8");

include('connection.php');
  
$login=$_POST['login'];
//$pass=$_POST['pass'];

		// ���������� �� ����� ���� � � ���
		$res = mysql_query("SELECT * FROM users WHERE login='$login'");
		$row = mysql_num_rows($res);
        if($row>=1) {
			//echo "����� ���� � � ������ !";
			echo "1";
		} else {
			echo "2";
		}

?> 