<?php $this->load->view('admin/header'); ?>
		
	  <td id="zcenter">
		<span id="dle-info"></span>

		<div id="dle-content">
			
			<div id="example" class="flora">
<!-- Main content -->
			

<ul>
<?php
if(@!$error) {
	echo "<h3>Your file was successfully uploaded!</h3>";
	foreach ($upload_data as $item => $value) {
		echo "<li>".$item.": ".$value."</li>";
	}
} else {
	print_r($error);
}
?>
</ul>

<p><?php //echo anchor('upload', 'Upload Another File!'); ?></p>			
					
			
<!-- Main content -->
			</div>
        </div>
<?php $this->load->view('admin/footer'); ?>