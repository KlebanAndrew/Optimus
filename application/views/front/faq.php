<?php $this->load->view('front/header'); ?>

<script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

$('#one').click(function(){
	if ($(".one").is(":hidden")) {
		$(".one").show("slow");
		$(".two").hide();
		$(".tree").hide();
		$(".four").hide();
    } else {
        $(".one").hide();
    }
});

$('#two').click(function(){
	if ($(".two").is(":hidden")) {
		$(".two").show("slow");
		$(".one").hide();
		$(".tree").hide();
		$(".four").hide();
    } else {
        $(".two").hide();
    }
});

$('#tree').click(function(){
	if ($(".tree").is(":hidden")) {
		$(".tree").show("slow");
		$(".one").hide();
		$(".two").hide();
		$(".four").hide();
    } else {
        $(".tree").hide();
    }
});

$('#four').click(function(){
	if ($(".four").is(":hidden")) {
		$(".four").show("slow");
		$(".one").hide();
		$(".two").hide();
		$(".tree").hide();
    } else {
        $(".four").hide();
    }
});

function hid_all() {
$(".one").hide();
$(".two").hide();
$(".tree").hide();
$(".four").hide();

}

hid_all();
});
</script>






<div id="container">
	<h1>�������</h1>
	<div id="body">


	<div style="height:400px; margin:50px;">
		
<a href="#" id="one">� ������ �������� �����</a><br />
<div class="one">		
<p style="margin-left: 20px;">1. �������� ���� �� ��������� ������� ������� "<a href="<? echo site_url("main/plan_next") ?>">����������</a>", ��������� ����������</p>
<p style="margin-left: 20px;">2. �������� ������ &quot;���������� ����&quot;</p>
<p style="margin-left: 20px;">3. ϳ��� ������������ ����� ����� ��������� �'������� ����������� �� ���� �����������</p>
</div>

<a href="#" id="two">�� ���������� ����������, �������� �����, �� ���?</a><br />
<div class="two">		
<p style="margin-left: 20px;">1. ������� "<a href="<? echo site_url("main/index") ?>">�������� �������</a>" �� ������������ �� ������ ����� ������������� ����������</p>
<p style="margin-left: 20px;">2. � ������� �� �������� ��������� ������� (������������ ���� ���� � ��� ����)</p>
<p style="margin-left: 20px;">3. ����� � ������� ��������� �� ��������� ������� (������� "<a href="<? echo site_url("main/plan_next") ?>">����������</a>")</p>
</div>

<a href="#" id="tree">�� ����������� ����� ���� � �� ���� ����������</a><br />
<div class="tree">		
<p style="margin-left: 20px;">1. г���� ���� ������������ ���������� �� ���� ������� � ������ ������</p>
<p style="margin-left: 20px;">2. ��� �������� �������� ��������� ������� � �������, ������� ����������, �� �����.</p>
<p style="margin-left: 20px;">3. ϳ��� ��������� ��������, ���� ���� ������ � �������� "������", ������� ����� � ��������� ������� ����������. ��� ����� �������� �� ��������, ������ ����� ������, ��� ����, �� ������ �������� ������� (���� ��������-������� �������������� �����������).</p>
</div>

<a href="#" id="four">� ���� ������� ����� � ������� ��������, �� ��������������?</a><br />
<div class="four">		
<p style="margin-left: 20px;">1. ��� �������� ��������� �������� �������� ������ "����������� ��������"</p>
<p style="margin-left: 20px;">2. � ����������� ��� ������ ������ ���� ����������.</p>
<p style="margin-left: 20px;">3. ϳ��� ��������� ��������, ���� ����������� ������������� � ��������� ������ �� ��������� ���� ������. ����������� ����� ����� ������� ��������.</p>
</div>


	</div>
	
	
	

	</div>

<?php $this->load->view('front/footer'); ?>