<?php $this->load->view('front/header'); ?>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
<div id="container">
	<h1>Перегляд завдання</h1>
	<div class="login_name"><?php echo $this->session->userdata('user_name').' (<a href="'.site_url("main/edit_user").'">'.$this->session->userdata('user_login') ?></a>)&nbsp;&nbsp;&nbsp;<a href="<? echo site_url("main/exit_user") ?>">Вихід</a></div>
	<div id="body">

<div class="perDiv">
	<table class="perTab">
		<tr>
			<td>Вид завдання:</td>
			<td>
				<?php
				if($zavdannya->vud == 1) { $vud="Планові"; }
				if($zavdannya->vud == 2) { $vud="Поточні"; }
				if($zavdannya->vud == 3) { $vud="Позачергові"; }
				echo $vud;?>
			</td>
		</tr>
		<tr>
			<td>Згідно стратегічного плану:</td>
			<td>
				<?php if($zavdannya->strateg == 1) { echo "Так"; } else { echo "Ні"; } ?>
			</td>
		</tr>
		<tr>
			<td>Назва завдання:</td>
			<td>
				<? echo $zavdannya->nazva ?>
			</td>
		</tr>
		<tr>
			<td>Результат завдання:</td>
			<td>
				<? echo nl2br($zavdannya->rezult); ?>
			</td>
		</tr>
		<tr>
			<td>Дата початку завдання:</td>
			<td>
				<? echo date('d.m.Y', strtotime($zavdannya->date_begin)) ?>
			</td>
		</tr>
		<tr>
			<td>Запланована дата завершення:</td>
			<td>
				<? echo date('d.m.Y', strtotime($zavdannya->date_zapl_zaversh)) ?>
			</td>
		</tr>
		<tr>
			<td>Запланований час на виконання:</td>
			<td>
				<? echo $zavdannya->zapl_chas ?> год.
			</td>
		</tr>
		<tr>
			<td>Фактично затрачений час:</td>
			<td>
				<? echo $zavdannya->chas_fakt; ?> год.
			</td>
		</tr>
		<tr>
			<td>Фактична дата завершення:</td>
			<td>
				<? echo $zavdannya->data_fakt; ?>
			</td>
		</tr>		
		<tr>
			<td>Періоди повторень:</td>
			<td>
				<?php		
					foreach ($repeat->result() as $row) {
						echo $row->date_begin.' - '.$row->date_zapl_zaversh.'<br />';
					}
				?>
			</td>
		</tr>
		<tr>
			<td>Примітка:</td>
			<td>
				<? echo $zavdannya->prymitky; ?>
			</td>
		</tr>
	</table>
	<div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
		<input type="button" class="but orange" style="float:none;" onclick="location.href='<?php echo site_url("main/index") ?>'" value="Закрити" />
	</div>	  
</div>	
	

	</div>

<?php $this->load->view('front/footer'); ?>