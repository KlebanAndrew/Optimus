<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" >
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>Админ-панель</title>
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/admin/files/style.css">
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/admin/files/engine.css">



<!-- TinyMCE -->
<script type="text/javascript" src="<? echo base_url() ?>application/views/admin/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",

		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example word content CSS (should be your site CSS) this one removes paragraph margins
		content_css : "css/word.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
</script>
<!-- /TinyMCE -->





</head><body>

<div id="header">
	<table id="ztop" width="100%"><tbody>
		<tr>
			<td>
				<br>
				<center><font color="white"><h2>Админ-панель</h2></font></center>
			</td>
		</tr>
		<tr>
			<td height="69">
				<div class="logo"></div>
			 </td>
			<td align="right">
				<div class="rotator"></div>
			</td>
		</tr>
	</tbody></table>
</div>

<table align="center" width="100%">
<tbody><tr>
<td width="99%">

<div id="menu">
<ul class="lm" id="nav">
	<li><a href="<? echo site_url("admin/index") ?>">Настройки</a></li>
	<li><a href="<? echo site_url("admin/add_category_product") ?>">Добавить категорию продуктов</a></li>
	<li><a href="<? echo site_url("admin/slider") ?>">Слайдер</a></li>
	<li><a href="<? echo site_url("admin/exit") ?>">Выход</a></li>
</ul>
</div>

</td>
</tr>
</tbody></table>
<div id="main">
<table id="zmain">
	<tbody><tr>
	  <td id="zleft">
	  	<h3>Категории</h3>
<?php
for($i=0; $i<count($this->menu); $i++) {
	echo '<div><a href="'.site_url("admin/category/".$this->menu[$i]['id']).'" class="mainlevel">'.$this->menu[$i]['nazva'].'</a></div>';				

}
?>		
		<br>
     	<h3>Тексты</h3>
		<div><a href="<? echo site_url("admin/content/1") ?>" class="mainlevel">Телефоны на главной</a></div>
		<div><a href="<? echo site_url("admin/content/2") ?>" class="mainlevel">О ресторане</a></div>
		<div><a href="<? echo site_url("admin/content/3") ?>" class="mainlevel">Доставка</a></div>
		<div><a href="<? echo site_url("admin/content/4") ?>" class="mainlevel">Для детей</a></div>
		<div><a href="<? echo site_url("admin/content/5") ?>" class="mainlevel">Спецпредложение</a></div>
		<div><a href="<? echo site_url("admin/content/6") ?>" class="mainlevel">Вакансии</a></div>
		<div><a href="<? echo site_url("admin/content/7") ?>" class="mainlevel">Контакты</a></div>
		<div><a href="<? echo site_url("admin/content/8") ?>" class="mainlevel">Статья внизу</a></div>
		<div><a href="<? echo site_url("admin/content/9") ?>" class="mainlevel">Футер</a></div>
		<br>
     	<h3>Другое</h3>
		<div><a href="<? echo site_url("admin/comment") ?>" class="mainlevel">Отзывы</a></div>
		<div><a href="<? echo site_url("admin/gallery") ?>" class="mainlevel">Галерея</a></div>
	  </td>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3><?php echo $main[0]['title']."&nbsp;<font color='#FF0000'>".@$message."</font>" ?></h3>
			
			        <div id="example" class="flora">
			<!-- вкладка с формой добавления полного раздела -->

			
<?php echo form_open('admin/save_content'); ?>

	<textarea id="elm1" name="elm1" rows="15" cols="80" style="width: 80%">
		<?php echo $main[0]['texts']; ?>
	</textarea>

	<br />
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
	<input type="submit" name="save" value="Сохранить" />
</form>
			
			
			

<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>