<?php $this->load->view('front/header'); ?>
    <script src="<? echo base_url() ?>application/views/js/jquery-1.6.2.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
    <script src="<? echo base_url() ?>application/views/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>

    <script src="<? echo base_url() ?>application/views/front/files/datapicer/jquery.ui.datepicker-uk.js"></script>

    <link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-1.8.14.custom.css">
    <link type="text/css" rel="stylesheet" href="<? echo base_url() ?>application/views/front/files/datapicer/css/ui-lightness/jquery-ui-timepicker-addon.css">
    <script type="text/javascript" src="<? echo base_url() ?>application/views/js/DatePickerScript.js"></script>

    <!-- Підказки -->
    <script src="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.jq.js" type="text/javascript"></script>
    <link href="<? echo base_url() ?>application/views/front/files/EL7R_NOTIFY/el7r_notify.min.jq.css" rel="stylesheet" type="text/css" />
    <link type="text/css" href="<? echo base_url() ?>application/views/front/css/my.css" rel="stylesheet" />
    <link type="text/css" href="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip_index.css" rel="stylesheet" />
    <script src="<? echo base_url() ?>application/views/js/tooltip/jquery.tooltip.js"></script>

    <script>

    </script>
    <div id="container">
        <div id="body">
            <div class="login_name"></div>
            <h1>Звіт для статистики</h1>
            <form style="display: inline-block;" action="<? echo site_url("servicedesk/import_closed_tasks") ?>" method="post" id="export">
                        <input type="submit" class="but orange" style="float:none;" value="Імпорт позачергових закритих завдань" />
            </form>
            <form style="display: inline-block;" action="<? echo site_url("servicedesk/import_open_tasks") ?>" method="post" id="export">
                        <input type="submit" class="but orange" style="float:none;" value="Імпорт відкритих планових завдань на наступний тиждень" />
            </form>
        </div>
        <div style="margin-top: 25px;">
            <?php

            ?>
        </div>
    </div>

<?php $this->load->view('front/footer'); ?>