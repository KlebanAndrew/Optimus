<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7 ie6 offline-page" lang="ru"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7 offline-page" lang="ru"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8 offline-page" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!-->
<html slick-uniqueid="8" class="no-js offline-page" lang="ru"><!--<![endif]--><head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="title" content="���������� �� ��������� ���������� ���">
  <title>���������� �� ��������� ���������� ��� "��������������������"</title>
  <link rel="stylesheet" href="<? echo base_url() ?>application/views/login/files/base.css" type="text/css" media="all">
  <link rel="stylesheet" href="<? echo base_url() ?>application/views/login/files/typography.css" type="text/css" media="all">
  <link rel="stylesheet" href="<? echo base_url() ?>application/views/login/files/system.css" type="text/css" media="all">
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
</head>
<body>
    <div class="content container">
        <h1>���������� �� ��������� ���������� ��� "��������������������"</h1>
		<br>
     		<p>��� ��������� ��������� ������ ������� <a href="<?php echo site_url("register/index") ?>">���������</a>.</p>
    	
<?php echo form_open('main/index') ?>  
            <fieldset>

				<legend>�����������</legend>

                <div class="row">
                    <input name="user_login" id="username" alt="�����" size="18" type="text">
                    <label for="username">����</label>
                </div>

                <div class="row">
                    <input name="user_pass" size="18" alt="������" id="passwd" type="password">
                    <label for="passwd">������</label>
                </div>
				<!--
                <div class="row">
                    <input name="remember" value="yes" alt="��������� ����" id="remember" type="checkbox">
                    <label for="remember">��������� ����</label>
                </div>
				-->
                <input name="Submit" class="button" value="�����" type="submit">&nbsp;&nbsp;<?php echo $message ?>
            </fieldset>
       	</form>
    </div>
</body></html>