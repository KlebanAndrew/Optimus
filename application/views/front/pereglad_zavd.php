<?php $this->load->view('front/header'); ?>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
<div id="container">
	<h1>�������� ��������</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">�����</a></div>
	<div id="body">

<div class="perDiv">
	<table class="perTab">
		<tr>
			<td>��� ��������:</td>
			<td>
				<?php
				if($zavdannya->vud == 1) { $vud="������"; }
				if($zavdannya->vud == 2) { $vud="������"; }
				if($zavdannya->vud == 3) { $vud="����������"; }
				echo $vud;?>
			</td>
		</tr>
		<tr>
			<td>����� ������������ �����:</td>
			<td>
				<?php if($zavdannya->strateg == 1) { echo "���"; } else { echo "ͳ"; } ?>
			</td>
		</tr>
		<tr>
			<td>����� ��������:</td>
			<td>
				<? echo $zavdannya->nazva ?>
			</td>
		</tr>
		<tr>
			<td>��������� ��������:</td>
			<td>
				<? echo nl2br($zavdannya->rezult); ?>
			</td>
		</tr>
		<tr>
			<td>���� ������� ��������:</td>
			<td>
				<? echo date('d.m.Y', strtotime($zavdannya->date_begin)) ?>
			</td>
		</tr>
		<tr>
			<td>����������� ���� ����������:</td>
			<td>
				<? echo date('d.m.Y', strtotime($zavdannya->date_zapl_zaversh)) ?>
			</td>
		</tr>
		<tr>
			<td>������������ ��� �� ���������:</td>
			<td>
				<? echo $zavdannya->zapl_chas ?> ���.
			</td>
		</tr>
		<tr>
			<td>�������� ���������� ���:</td>
			<td>
				<? echo $zavdannya->chas_fakt; ?> ���.
			</td>
		</tr>
		<tr>
			<td>�������� ���� ����������:</td>
			<td>
				<? echo $zavdannya->data_fakt; ?>
			</td>
		</tr>		
		<tr>
			<td>������ ���������:</td>
			<td>
				<?php		
					foreach ($repeat->result() as $row) {
						echo $row->date_begin.' - '.$row->date_zapl_zaversh.'<br />';
					}
				?>
			</td>
		</tr>
		<tr>
			<td>�������:</td>
			<td>
				<? echo $zavdannya->prymitky; ?>
			</td>
		</tr>
	</table>
	<div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
		<input type="button" class="but orange" style="float:none;" onclick="location.href='<?php echo site_url("main/index") ?>'" value="�������" />
	</div>	  
</div>	
	

	</div>

<?php $this->load->view('front/footer'); ?>