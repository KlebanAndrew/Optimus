<!DOCTYPE html>
<html> 
<head>
	<meta charset="UTF-8">
	<title>Index</title>
</head>
<body>
	<form name=form id=form method=post>
		<input name="val" value="<?php echo date('Y-m-d'); ?> "/>
		<input type="submit"/>
	</form>
	<?
	if(!empty($_POST['val']))
	{
		$d = new DateTime($_POST['val']);
		$s = date("w", $d->getTimestamp())-1;
		if($s<0){
			$s = 6;
			$monday = date_sub($d, new DateInterval("P".$s."D"));
			echo "monday = ".$monday->format('Y-m-d')."<br/>";
			$friday = date_add($monday, new DateInterval("P4D"));
			echo "friday = ".$friday->format('Y-m-d')."<br/>"; 
			$nedil = date_add($friday, new DateInterval("P2D"));
			echo "nedil = ".$nedil->format('Y-m-d')."<br/>";
		}else{
			$monday = date_sub($d, new DateInterval("P".$s."D"));
			echo "monday = ".$monday->format('Y-m-d')."<br/>";
			$friday = date_add($monday, new DateInterval("P4D"));
			echo "friday = ".$friday->format('Y-m-d')."<br/>";
			$nedil = date_add($friday, new DateInterval("P2D"));
			echo "nedil = ".$nedil->format('Y-m-d')."<br/>";
		}
	}
	?>
</body>
</html>