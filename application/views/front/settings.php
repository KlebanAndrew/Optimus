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
            //плагін для списку працівників
            $(".chosen-select").chosen({
                no_results_text: "Нічого не знайдено!",
                width: "100%"
            });
            //ajax функція для запису в бд інформації про нового секретаря
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
                            $('#secretar_list').append('<li data-id="'+data.id_sec+'">'+data.name+'(us'+data.tab_nomer+')<button class="del_but" id="del_'+data.id+'" data-id="'+data.id+'">Видалити</button></li>');


                        }
                        $().el7r_notify({'text':data.msg, 'place_v':'bottom', 'place_h':'left','icon':'', 'skin':'default', 'delay':'4000', 'ex':'true', 'effect':'slide'});
                        setInterval(function(){
                            // window.location.reload(0);
                        },3000); // 10sec (10000)

                    }
                });
            });
            //ajax функція видалення секретаря
            $("body").on("click", ".del_but", function(){// розширений обробник для того, щоб нові елементи (які добавлені методом append() теж мали обробник on click
            //$('.del_but').click(function(){
               var id = $(this).data("id");//ідентифікатор запису в бд
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
        <a href="<? echo site_url("main/exit_user") ?>">Вихід</a>
    </div>
    <h1>Налаштування</h1>

    <div id="body">


        <div style=" margin:25px;">
            <a href="#" id="faq">Допомога</a><br />
        <div class="faq" style="margin:15px;">

            <a href="#" id="one">Я вперше заповнюю плани</a><br />
            <div class="one">
                <p style="margin-left: 20px;">1. Заповніть план на наступний тиждень вкладка "<a href="<? echo site_url("main/plan_next") ?>">Планування</a>", поточними завданнями</p>
                <p style="margin-left: 20px;">2. Натисніть кнопку &quot;Затвердити план&quot;</p>
                <p style="margin-left: 20px;">3. Після затвердження плану вашим керівником з'явиться повідомлення що план затверджено</p>
            </div>

            <a href="#" id="two">Моє планування затведжене, минулого тижня, що далі?</a><br />
            <div class="two">
                <p style="margin-left: 20px;">1. Вкладку "<a href="<? echo site_url("main/index") ?>">Поточний тиждень</a>" ви дозаповнюєте на протязі тижня позачерговими завданнями</p>
                <p style="margin-left: 20px;">2. В пятницю ви звітуєтесь відповідною кнопкою (проставляєте дату факт і час факт)</p>
                <p style="margin-left: 20px;">3. Також в пятницю плануєтесь на наступний тиждень (вкладка "<a href="<? echo site_url("main/plan_next") ?>">Планування</a>")</p>
            </div>

            <a href="#" id="tree">Як заповнювати річний план і що таке деталізація</a><br />
            <div class="tree">
                <p style="margin-left: 20px;">1. Річний план заповнюється завданнями які потім падають в планові роботи</p>
                <p style="margin-left: 20px;">2. При створенні завдання необхідно вказати № процесу, задіяних працівників, та період.</p>
                <p style="margin-left: 20px;">3. Після створення завдання, коли воно попало в категорію "планові", кожного тижня є можливість вносити деталізацію. Для цього натисніть на завдання, внесіть назву роботи, час план, та оберіть потрібний тиждень (дати понеділка-пятниці проставляються автоматично).</p>
            </div>

            <a href="#" id="four">В мене кожного тижня є однакові завдання, як автоматизувати?</a><br />
            <div class="four">
                <p style="margin-left: 20px;">1. При створенні поточного завдання натисніть кнопку "Повторювати завдання"</p>
                <p style="margin-left: 20px;">2. У випадаючому вікні оберіть кінцеву дату повторення.</p>
                <p style="margin-left: 20px;">3. Після створення завдання, воно автоматично продублюється в наступних тижнях до вказаного вами періоду. Повторювати можна тільки поточне завдання.</p>
            </div>


        </div>
            <a href="#" id="settings">Налаштування</a><br />
        <div class="settings" style="margin:15px;">
            <a href="#" id="profile">Профіль</a><br />
            <div class="profile">
                <form action="<? echo site_url("main/user_update") ?>" method="post">
                    <div class="perDiv">
                        <table class="perTab">
                            <tr>
                                <td>ПІБ:</td>
                                <td><? echo $user->name; ?></td>
                            </tr>
                            <tr>
                                <td>Телефон:</td>
                                <td><? echo $user->tel; ?></td>
                            </tr>
                            <tr>
                                <td>Таб. номер:</td>
                                <td><? echo $user->tab_nomer; ?></td>
                            </tr>
                            <tr>
                                <td>Підрозділ:</td>
                                <td><? echo $user->description; ?></td>
                            </tr>
                            <tr>
                                <td>Посада:</td>
                                <td><? echo $user->posada; ?></td>
                            </tr>
                            <tr>
                                <td>Продукт посади:</td>
                                <td><input type="text" name="product_posadu" value="<? echo $user->product_posadu; ?>" class="form-control" /></td>
                            </tr>
                        </table>
                        <div style="height: 30px;margin: 0px 20px 0px 20px;text-align:center;">
                            <input type="submit" class="but orange" style="float:none;" value="Зберегти" />
                        </div>
                    </div>
                </form>
            </div>
            <a href="#" id="secretary">Мої секретарі</a><br />
            <div class="secretary" style="margin: 50px 50px;width:400px;">
                <h1>Мої секретарі</h1>
                <ul id="secretar_list">
                <?php
                if(isset($zast_user)){
                    foreach($zast_user as $z){
                        echo '<li data-id="'.$z['secretar_id'].'">'.$z['name'].' (us'.$z['tab_nomer'].')<button id="del_'.$z['id'].'" data-id="'.$z['id'].'"class="del_but">Видалити</button></li>' ;
                    }
                }?>
                </ul>
                  <div style="width:280px; float:left; margin-right:20px;padding-top: 20px;"><select id="secretar" name="secretar_id" data-placeholder="Виберіть заступника" class="chosen-select" style="width:350px;" tabindex="2">
                        <option value=""></option>
                        <?php foreach($users as $us){
                            echo '<option value="'.$us['id'].'">'.$us['name'].'(us'.$us['tab_nomer'].')</option>';
                        }?>
                    </select>
                  </div>
                  <div style="float: right; margin-top: 20px"><input type="button" id="but_sec_add" class="but_sec_add"  value="Додати" /></div>


            </div>
            <div style="height: 30px;"></div>
        </div>




    </div>

<?php $this->load->view('front/footer'); ?>