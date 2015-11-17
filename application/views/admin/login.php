<?php $this->load->view('admin/header'); ?>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			<h3>Авторизация</h3>
			
			<div id="example" class="flora">
<!-- Main content -->

			
<?php echo form_open('admin/index') ?>
    <table align="center">
        <tr>
            <td>Логин:</td>
            <td><input type="text" name="login" class="text" maxlength="5" value="admin" /></td>
        </tr>
        <tr>
            <td>Пароль:</td>
            <td><input type="password" name="password" class="text" /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Вход" class="button" /></td>
        </tr>
    </table>
</form>
			
<b><center><font color="#FF0000"><?php echo $message ?></font></center></b>
			
			

<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>