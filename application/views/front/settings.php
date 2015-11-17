<?php $this->load->view('front/header'); ?>
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/chosen.min.css" rel="stylesheet" />
    <script type="text/javascript" src="<? echo base_url() ?>application/views/front/files/datapicer/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="<? echo base_url() ?>application/views/front/js/chosen.jquery.min.js"></script>

    <script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
    <link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        $(document).ready(function(){
            //functions for div faq
            $('#one').click(function(){
                if ($(".one").is(":hidden")) {
                    $(".one").show("slow");
                    $(".two").hide("slow");
                    $(".tree").hide("slow");
                    $(".four").hide("slow");
                } else {
                    $(".one").hide("slow");
                }
            });

            $('#two').click(function(){
                if ($(".two").is(":hidden")) {
                    $(".two").show("slow");
                    $(".one").hide("slow");
                    $(".tree").hide("slow");
                    $(".four").hide("slow");
                } else {
                    $(".two").hide("slow");
                }
            });

            $('#tree').click(function(){
                if ($(".tree").is(":hidden")) {
                    $(".tree").show("slow");
                    $(".one").hide("slow");
                    $(".two").hide("slow");
                    $(".four").hide("slow");
                } else {
                    $(".tree").hide("slow");
                }
            });

            $('#four').click(function(){
                if ($(".four").is(":hidden")) {
                    $(".four").show("slow");
                    $(".one").hide("slow");
                    $(".two").hide("slow");
                    $(".tree").hide("slow");
                } else {
                    $(".four").hide("slow");
                }
            });
            //end faq div functions
            //functions for settings div settings
            $('#profile').click(function(){
                if($(".profile").is(":hidden")){
                    $(".profile").show("slow");
                    $(".secretary").hide("slow");
                } else{
                    $(".profile").hide("slow");
                }
            });
            $('#secretary').click(function(){
                if($(".secretary").is(":hidden")){
                    $(".secretary").show("slow");
                    $(".profile").hide("slow");
                } else{
                    $(".secretary").hide("slow");
                }
            });
            ////end settings div functions
             //new functions for main divs
            $('#faq').click(function(){
                if($(".faq").is(":hidden")){
                    $(".faq").show("slow");
                    $(".settings").hide("slow");
                } else{
                    $(".faq").hide("slow");
                }
            });
            $('#settings').click(function(){
                if($(".settings").is(":hidden")){
                    $(".settings").show("slow");
                    $(".faq").hide("slow");
                } else{
                    $(".settings").hide("slow");
                }
            });
            //end functions for main divs
            //hide function for faq's nested divs
            function hid_all_faq() {
                $(".one").hide();
                $(".two").hide();
                $(".tree").hide();
                $(".four").hide();

            }
            //end function
            function hid_all_settings() {
                $(".profile").hide();
                $(".secretary").hide();

            }
            //hide function for main's nested divs
            function hid_all_main(){
                $(".faq").hide();
                $(".settings").hide();
            }
            //end
            function hid_all(){
                hid_all_faq();
                hid_all_settings();
                hid_all_main();

            }
            hid_all();
            //����� ��� ������ ����������
            $(".chosen-select").chosen({
                no_results_text: "ͳ���� �� ��������!",
                width: "100%"
            });
            //ajax ������� ��� ������ � �� ���������� ��� ������ ���������
            $('#but_sec_add').click(function(){
                $.ajax({
                    type: "POST",
                    url: "<? echo site_url("ajax/set_secretar") ?>",
                    data: {
                        "id_secretar" : $('#secretar').val()
                    },
                    dataType: "json",
                    success: function(data){
                        //console.log(data);
                        if(typeof data.id_sec!='undefined'){
                            $('#secretar_list').append('<li data-id="'+data.id_sec+'">'+data.name+'(us'+data.tab_nomer+')<button class="del_but" id="del_'+data.id+'" data-id="'+data.id+'">��������</button></li>');


                        }
                        $().el7r_notify({'text':data.msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
                        setInterval(function(){
                            // window.location.reload(0);
                        },3000); // 10sec (10000)

                    }
                });
            });
            //ajax ������� ��������� ���������
            $("body").on("click", ".del_but", function(){// ���������� �������� ��� ����, ��� ��� �������� (�� �������� ������� append() ��� ���� �������� on click
            //$('.del_but').click(function(){
               var id = $(this).data("id");//������������� ������ � ��
                $.ajax({
                    type: "POST",
                    url: "<? echo site_url("ajax/del_secretar") ?>",
                    data: {
                        "id" : id
                    },
                    dataType: "json",
                    success: function(data){
                        var sel = '#del_'+data.id;
                        if(data.id > 0){$(sel).parent().remove();}
                        $().el7r_notify({'text':data.msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
                        setInterval(function(){
                            // window.location.reload(0);
                        },3000); // 10sec (10000)

                    }
                });
            });
        });
    </script>






<div id="container">
    <div style="float:right; margin:15px 15px;"><?php echo $this->session->userdata('user_name');?>
        &nbsp;&nbsp;&nbsp;
        <a href="<? echo site_url("main/exit_user") ?>">�����</a>
    </div>
    <h1>������������</h1>

    <div id="body">


        <div style=" margin:25px;">
            <a href="#" id="faq">��������</a><br />
        <div class="faq" style="margin:15px;">

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
            <a href="#" id="settings">������������</a><br />
        <div class="settings" style="margin:15px;">
            <a href="#" id="profile">�������</a><br />
            <div class="profile">
                <form action="<? echo site_url("main/user_update") ?>" method="post">
                    <div class="perDiv">
                        <table class="perTab">
                            <tr>
                                <td>ϲ�:</td>
                                <td><? echo $user->name; ?></td>
                            </tr>
                            <tr>
                                <td>�������:</td>
                                <td><? echo $user->tel; ?></td>
                            </tr>
                            <tr>
                                <td>���. �����:</td>
                                <td><? echo $user->tab_nomer; ?></td>
                            </tr>
                            <tr>
                                <td>ϳ������:</td>
                                <td><? echo $user->description; ?></td>
                            </tr>
                            <tr>
                                <td>������:</td>
                                <td><? echo $user->posada; ?></td>
                            </tr>
                            <tr>
                                <td>������� ������:</td>
                                <td><input type="text" name="product_posadu" value="<? echo $user->product_posadu; ?>" class="form-control" /></td>
                            </tr>
                        </table>
                        <div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
                            <input type="submit" class="but orange" style="float:none;" value="��������" />
                        </div>
                    </div>
                </form>
            </div>
            <a href="#" id="secretary">�� ��������</a><br />
            <div class="secretary" style="margin: 50px 50px;width:400px;">
                <h1>�� ��������</h1>
                <ul id="secretar_list">
                <?php
                if(isset($zast_user)){
                    foreach($zast_user as $z){
                        echo '<li data-id="'.$z['secretar_id'].'">'.$z['name'].' (us'.$z['tab_nomer'].')<button id="del_'.$z['id'].'" data-id="'.$z['id'].'"class="del_but">��������</button></li>' ;
                    }
                }?>
                </ul>
                  <div style="width:280px; float:left; margin-right:20px;padding-top: 20px;"><select id="secretar" name="secretar_id" data-placeholder="������� ����������" class="chosen-select" style="width:350px;" tabindex="2">
                        <option value=""></option>
                        <?php foreach($users as $us){
                            echo '<option value="'.$us['id'].'">'.$us['name'].'(us'.$us['tab_nomer'].')</option>';
                        }?>
                    </select>
                  </div>
                  <div style="float: right; margin-top: 20px"><input type="button" id="but_sec_add" class="but_sec_add"  value="������" /></div>


            </div>
            <div style="height: 30px;"></div>
        </div>




    </div>

<?php $this->load->view('front/footer'); ?>