<?php $this->load->view('front/header'); ?>
			<div class="content kinder">
				<div class="kinder-top">
					<div class="kinder-bot">
						<article>
							<h1>Для детей</h1>
							<ul class="kinder-menu">
<?php
for($i=0; $i<count($produkts); $i++) {		
	echo '						<li>
									<a href="'.site_url("main/category/".$produkts[$i]['id_category']).'"><img src="'.base_url().'images/produkt/'.$produkts[$i]['images'].'" alt="'.$produkts[$i]['name'].'" /></a>
									<h4><a href="'.site_url("main/category/".$produkts[$i]['id_category']).'">'.$produkts[$i]['name'].'</a></h4>
								</li>';	
}
?>							
							</ul>
<?php echo @$main[0]['texts'] ?>
						</article>
					</div>
				</div>
			</div>
<?php $this->load->view('front/footer'); ?>