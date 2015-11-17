<?php $this->load->view('front/header'); ?>
			<div class="content">
				<article>
					<h1><span><?php echo @$main[0]['title'] ?></span></h1>

<?php echo @$main[0]['texts'] ?>
					
				</article>
			</div>
<?php $this->load->view('front/footer'); ?>