<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Планування</title>


</head>
<body>




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
?>




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <title>testing directory tree</title>
 <style>
 body, html {
  margin:0px;
  padding:0px;
  width:100%;
  height:100%;
  background-color:#c9c9c9;
  font-family:Verdana;
  font-size:12pt;
}

.tree {
  margin:10px 10px 10px 10px;
  width:40%;
  display:inline-block;
  float:left;
}

.tree_lvl_1 {
  padding-left:20px;
  list-style-image:url("desing/1299993783_folder.gif");
}

.tree_lvl_1 a {
  COLOR: #000; TEXT-DECORATION: none;
}
.tree_lvl_1 a:link {
  COLOR: #000; TEXT-DECORATION: none;
}
.tree_lvl_1 a:hover {
  COLOR: #0a0aa1; TEXT-DECORATION: none;
}
.tree_lvl_1 a:unknown {
  COLOR: #000; TEXT-DECORATION: none;
}

.tree_lvl_2 {
  padding-left:24px;
  margin-left:-12px;
  margin-top:-3px;
  list-style-image:url("desing/folder_2.gif");
  border-left:1px solid #000;
}
 
 </style>
	<script type="text/javascript" src="http://10.93.1.52/zvity/application/views/files/datapicer2/js/jquery-1.8.0.min.js"></script>
	
<script type="text/javascript">
	//var sap_url= "http://10.93.1.56:8084/SAPEmployee/Employee";
	var sap_url= "http://10.93.10.62:9090/SAPService/Employee";
	$(document).ready(function() {
		$('a').click(function(){
			loadUser($(this).attr("id"));		
		});
   	});


function loadUser(id) {
    $.ajax({
        type: "GET",
        url: "http://10.93.10.62:9090/SAPService/Employee",
        data: {
            "task": "COMMUNIC",
            "employee_id": "",
            "only_communic": "",
			"last_name": "",
            "first_name": "",
            "org_name": "",
            "job_name": "",
            "org_id": id,
            "job_id": ""
        },
        dataType: "xml",
        success: function(xml) {
            var items = new Array();
            var i = 0; var strList = "";
            $(xml).find('item').each(function() {
                //alert(jQuery(this).find('TABNO').text());
                items[i] = new Array(
                /*00*/jQuery(this).find('TABNO').text(),
                /*01*/jQuery(this).find('FAMILY').text(),
                /*02*/jQuery(this).find('NAME').text(),
                /*03*/jQuery(this).find('FATHER').text(),
                /*04*/jQuery(this).find('BIRTHDATE').text(),
                /*05*/jQuery(this).find('JOB').text(),
                /*06*/jQuery(this).find('JOBTXT').text(),
                /*07*/jQuery(this).find('ORG_UNIT').text(),
                /*08*/jQuery(this).find('ORGTXT').text(),
                /*09*/jQuery(this).find('TEL_ATS').text(),
                /*10*/jQuery(this).find('TEL_HOME').text(),
                /*11*/jQuery(this).find('TEL_MISTO').text(),
                /*12*/jQuery(this).find('KOD_ATS').text(),
                /*13*/jQuery(this).find('KOD_MISTO').text(),
                /*14*/jQuery(this).find('TEL_MOB1').text(),
                /*15*/jQuery(this).find('TEL_MOB2').text(),
                /*16*/jQuery(this).find('EMAIL').text(),
                /*17*/jQuery(this).find('TEL_FAX').text(),
                /*18*/jQuery(this).find('POL').text()
                    );
                strList += "<li name='" + i.toString() + "'>" + items[i][1] + ' ' + items[i][2] + ' ' + items[i][3] + "<br/><span>" + items[i][6] + "</span>" + "</li>";
                i = i + 1;
            });

            $("#rez_search").html("<ul id='filter'>" + strList + "</ul>");
			//return false;
        }
    });  

}
	
</script>	
	
	
	
</head>
<body>
<div class="page">
  <div class="tree">
  <?php view_tree(); ?>
  </div>
  
 
  
    <div id="rez_search">
  bvbvb
  </div>  
  
</div>

</body>
</html>



	
	
</body>
</html>	
	