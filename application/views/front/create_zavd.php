<?php $this->load->view('front/header'); ?>

	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/moment.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker.js"></script>
	<script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>
	<link type="text/css" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />

	<script>


	var min_date;//��������� ����� ��� ��������� ����
	function initDatepicker(){ //������� ������� ���� ��� ��� �������� ��� ����� vud
        /////////////////////////////////////////////////////////////////
        var next_week_selector = <?php echo $next_week; ?>;
                //////////////////////////////////////////////////////////////////////
        var max_Date1;
        var selectedVal= $('#select').val();
		var prev_week = new Date();// ������� ����� �������� ������������ ����� (������������� ���������� ����)
		var this_week = new Date();// ������� �������� ��������� �����
		var max_date = new Date();//������� ������� ��������� �����
        var next_week = new Date(); //������� ����� �������� ���������� ���
        var next_week_end = new Date(); //������� ����� ������� ���������� ���
			var prev_date = prev_week.getDate();// ����� ��� ������ ����� ��� ��������� �����

			var numberofweek = prev_week.getDay();// ����� ��� ������ ����� �������������  ��� ��������� ����� 
				var b = prev_date - numberofweek+1;//����� ��� ������ ����� ���  �������� ��������� ����� 
				var a  = prev_date - (6 +numberofweek);//����� ��� ������ ����� ���  �������� ������������ ����� 
				var c= prev_date +(5-numberofweek);//����� ��� ������ ����� ���  ������� ��������� �����
                var d =prev_date +(8-numberofweek);//����� ��� ������ ����� ���  �������� ���������� �����
                var e=prev_date +(12-numberofweek);//����� ��� ������ ����� ���  ������� ���������� �����
                        max_date.setDate(c);//����������� ����
                        this_week.setDate(b);//����������� ����
                        prev_week.setDate(a);//����������� ����
                        next_week.setDate(d);//����������� ����
                        next_week_end.setDate(e);//����������� ����
        this_week = ((moment(this_week)).format("DD.MM.YYYY")); //���������� ���� �� ������� DD.MM.YYYY
        prev_week = ((moment(prev_week)).format("DD.MM.YYYY"));//���������� ���� �� ������� DD.MM.YYYY
        max_date = ((moment(max_date)).format("DD.MM.YYYY"));//���������� ���� �� ������� DD.MM.YYYY
        next_week = ((moment(next_week)).format("DD.MM.YYYY"));//���������� ���� �� ������� DD.MM.YYYY
        next_week_end = ((moment(next_week_end)).format("DD.MM.YYYY"));//���������� ���� �� ������� DD.MM.YYYY


			
			
		 minDate = (selectedVal == 3) ? prev_week :this_week;  /*'<? echo date('d.m.Y', strtotime($d_v)) ?>'*/   //�������� �������� ��� ������������ ��������
																													//��� �������� (selectedVal == 3) �������������� ���� ��������� �����
		        if(next_week_selector==0){ //�������� �������� ����� ������� ��� ��������� �� ���������� �����
		                    (selectedVal == 3) ? $('input[name=date_begin]').val(this_week) :$('input[name=date_begin]').val(this_week); //������������ ���� ��� ��������� ���� ��������� (���� ������� ��������)
                            $('input[name=date_zapl_zaversh]').val(max_date);
                            max_Date1=max_date;
                            min_date = minDate;
                        }
                else{
                            $('input[name=date_begin]').val(next_week) ; //������������ ���� ��� ��������� ���� ��������� (���� ������� ��������)
                            $('input[name=date_zapl_zaversh]').val(next_week_end);
                            max_Date1=next_week_end;
                            minDate=next_week;
                }




        $( ".datepicker" ).datepicker("destroy");
		$( ".datepicker" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate: minDate, 
			maxDate: max_Date1										//'<? echo date('d.m.Y', strtotime($d_do))?>'
		});
		};
		function addDays(days){ //������� ������� (����� ��������)
				var today = new Date();
				var next = new Date(today);
				tomorrow.setDate(today.getDate()+1);
		}
	$(function() {
        $("#zapl_chas").keypress(function (event) {
            if (event.which < 44
                || event.which > 57  || event.which ==47 || event.which ==45) {
                alert("��������� ������� ����� �����, ��������� 40 ��� 40.5");
                event.preventDefault();
            }
        });

		initDatepicker();//����������� ������� ������������ ����

		$( ".datepicker2" ).datepicker({
			dateFormat: "dd.mm.yy",
			minDate:  min_date                                      /*'<?echo date("d.m.Y", strtotime($d_v))?>'*/
		});
		$(".ui-datepicker").css("font-size", "0.9em");
		
		// Dialog
		$('#dialog').dialog({
			autoOpen: false,
			width: 300,
			buttons: {
				"��������": function() {
					$(this).dialog("close");
					$('input[name=date_end_povtor]').val($('#repeat_date_end').val());
					if($("#povtor_result").is(':checked')) {
						$('input[name=povtor_result]').val('1');
					} else {
						$('input[name=povtor_result]').val('0');	
					}
				},
				"�������": function() {
					$(this).dialog("close");
					$('input[name=date_end_povtor]').val('');
					$('input[name=povtor_result]').val('');
				}
			}
		});
		
		// Dialog Link
		$('#dialog_link').click(function(){
			$('#dialog').dialog('open');
			return false;
		});		

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
		$('#select').click(function(){//������ ������� ������������ ���� ��� ��� �������� ������ vud

			initDatepicker();
		});

	});
	</script>

<div id="container">
	<h1>�������� �������� <?php if($next_week == 1) { echo '�� ��������� �������'; }  ?></h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">�����</a></div><br />
	<div id="body">


<div id="inner">
<?php
$attributes = array('id' => 'modalform');
echo form_open('main/create_zavd_true', $attributes);
?>
<input type="hidden" name="next_week" value="<?php echo $next_week; ?>" />
<table border="0" align="center" class="createZavd">
  <tr>
    <td>��� ��������</td>
	<td>
		<select name="vud" id="select" class="form-control">
			<!--<option value="2">������</option>
			<option value="3">����������</option>-->
<?php if($next_week == 1 or $lock_potochni == 0) { echo '<option value="2" >������</option>'; }  ?>
<?php if($next_week == 0) { echo '<option value="3">����������</option>'; }  ?>
		</select>
    </td>
    <td style="text-align: right; padding-right: 20px;">
		<label>����� ������������ ����� <input type="checkbox" name="strateg" style="vertical-align: middle; margin: 0px;"  /></label>
	</td>
  </tr>
  <tr>
    <td>����� ��������</td>
    <td colspan="2"><label>
      <input type="text" name="nazva" id="textfield" class="form-control" />
    </label></td>
  </tr>
  <tr>
    <td>��������� ��������</td>
    <td colspan="2"><label>
      <textarea name="rezult" id="textarea" cols="65" rows="5" class="form-control"></textarea>
    </label></td>
  </tr>
  <tr>
    <td>���� ������� ��������</td>
    <td><input type="text" name="date_begin" class="form-control datepicker" value="<?php /*echo date('d.m.Y', strtotime($d_v))*/ ?>" /></td>
    <td style="text-align:right;">����������� ���� ���������� <input type="text" name="date_zapl_zaversh" class="form-control datepicker" value="<?php /*echo date('d.m.Y', strtotime($d_do))*/ ?>" /></td>
  </tr>
  <tr>
    <td>������������ ��� (� ���.)</td>
    <td><input id="zapl_chas" type="text" name="zapl_chas" class="form-control"/></td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
   <tr>
    <td colspan="3" style="text-align: center; padding-top: 5px;">
		<!-- ��������� �������� -->
		<input type="hidden" name="date_end_povtor" value="" />
		<input type="hidden" name="povtor_result" value="" />
		<input type="button" class="but orange" style="float:none;" name="button4" value="��������" id="create" />
		<input type="button" class="but orange" style="float:none;" id="dialog_link" value="����������� ��������" />
		<input type="button" class="but orange" style="float:none;" onclick="location.href='<? echo site_url("main/index") ?>'" value="���������" />
	</td>
  </tr>
</table>



		<!-- ui-dialog -->
		<div id="dialog" title="����������� �������� �������">
		<p><strong>ʳ����� ���� ����������</strong></p><input type="text" id="repeat_date_end" class="datepicker2">
		<p><label><input type="checkbox" id="povtor_result" style="vertical-align: middle; margin: 0px;" /> ����������� ���������</label></p>
		</div>
		<!-- ui-dialog -->		
</form>	

		
<br /><br />
</div>
	</div>
<?php $this->load->view('front/footer');?>