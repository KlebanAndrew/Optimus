<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<? echo base_url() ?>application/views/style.css">
<title>���������� �� ��������� ���������� ���</title>
</head>
<body>

<div id="container">
	<h1>���������� �� ��������� ���������� ���</h1>

	<div id="body">


	  <div class="code">
	  <font style="color:red;"><?php echo @$message ?></font>
        
<?php echo form_open('main/index') ?>        
	  <table width="429" border="0" align="center">
	    <tr>
	      <td width="113">����</td>
	      <td width="306"><input type="text" name="user_login" class="text" /></td>
        </tr>
	    <tr>
	      <td>������</td>
	      <td><input type="password" name="user_pass" class="text" /></td>
        </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td><input type="submit" name="button" id="button" value="����">
          </td>
        </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td><a href="<?php echo site_url("register/index") ?>">��������� ������ �����������</a></td>
        </tr>
	    <tr>
	      <td>&nbsp;</td>
	      <td><a href="">������ ������</a></td>
        </tr>
      </table>
</form>        
        </div>


</div>

	<p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>

</body>
</html>