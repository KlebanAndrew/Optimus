<?php $this->load->view('front/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
		
	<script>
	$(function() {
        $("#zapl_chas").keypress(function (event) {
            if (event.which < 46
                || event.which > 57 && event.which!=47) {
                alert("��������� ������� ����� �����, ��������� 40 ��� 40.5");
                event.preventDefault();
            }
        });

		$( ".datepicker" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate: '<? echo date('d.m.Y', strtotime($zavdannya->date_begin)) ?>',
			maxDate: '<? echo date('d.m.Y', strtotime($zavdannya->date_zapl_zaversh))?>'
		});	
	
		$(".ui-datepicker").css("font-size", "0.9em");
		
		$('#create').click(function(){
			if($('input[name=nazva]').val()=='') {  
				alert('�������� ����� �������� !');
			}
			if($('input[name=zapl_chas]').val()=='') {  
				alert('�������� ������������ ��� !');
			} 
			if(($('input[name=nazva]').val()!='') & ($('input[name=zapl_chas]').val()!='')) {
				$('#modalform').trigger('submit');	// ������������ �����
				return false;
			}
		});					
		
	});
	</script>

<div id="container">
	<h1>���������� ��������</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">�����</a></div><br />
	<div id="body">


<div id="inner">
<?php
$attributes = array('id' => 'modalform');
echo form_open('main/edit_zavd_true', $attributes);
?>	
<table border="0" align="center" class="createZavd">
  <tr>
    <td>��� ��������</td>
    <td>
		<select name="vud" id="select" class="form-control">
			<!--<option value="2" <?php //if($zavdannya->vud == 2) echo 'selected="selected"'; ?>>������</option>
			<option value="3" <?php //if($zavdannya->vud == 3) echo 'selected="selected"'; ?>>����������</option>-->
<?php if($zavdannya->vud == 2) { echo '<option value="2">������</option>'; } ?>
<?php if($zavdannya->vud == 3) { echo '<option value="3">����������</option>'; } ?>			
		</select>
	</td>
	<td style="text-align: right; padding-right: 20px;">
		<label>����� ������������ ����� <input type="checkbox" name="strateg" style="vertical-align: middle; margin: 0px;" <?php if($zavdannya->strateg == 1) { echo 'checked="checked"'; } ?>  /></label>
	</td>
  </tr>
  
  <tr>
    <td>����� ��������</td>
    <td colspan="2"><label>
      <input type="text" name="nazva" id="textfield" value="<?php echo $zavdannya->nazva ?> " class="form-control"/>
    </label></td>
  </tr>
  <tr>
    <td>��������� ��������</td>
    <td colspan="2"><label>
      <textarea name="rezult" id="textarea" cols="65" rows="5" class="form-control"><?php echo $zavdannya->rezult ?></textarea>
    </label></td>
  </tr>
   <tr>
    <td>���� ������� ��������</td>
    <td><input type="text" name="date_begin" class="form-control datepicker" value="<?php echo date('d.m.Y', strtotime($zavdannya->date_begin)) ?>"   /></td>
    <td style="text-align:right;">����. ���� �����. <input type="text" name="date_zapl_zaversh" class="form-control datepicker" value="<?php echo date('d.m.Y', strtotime($zavdannya->date_zapl_zaversh)) ?>"  /></td>
  </tr> 
  <tr>
    <td>������������ ��� (� ���.)</td>
    <td><input type="text" id="zapl_chas" name="zapl_chas" class="form-control" value="<?php echo $zavdannya->zapl_chas ?>"/></td>
    <td style="text-align:right;">������� <input type="text" name="prymitky" class="form-control" value="<?php echo $zavdannya->prymitky ?>"  /></td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td colspan="3" style="text-align: center; padding-top: 5px;">
		<input type="hidden" name="id" value="<?php echo $zavdannya->id ?>" />
		<input type="button" class="but orange" style="float:none;" value="��������" id="create" />
		<input type="button" class="but orange" style="float:none;" onclick="location.href='<? echo site_url("main/index") ?>'" value="���������" />
	</td>
  </tr>
</table>
		
</form>	
<br /><br />
</div>
	</div>

<?php $this->load->view('front/footer'); ?>