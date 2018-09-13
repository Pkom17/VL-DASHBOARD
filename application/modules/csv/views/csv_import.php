<!DOCTYPE html>
<html>
    <head>
        <title>  <?= lang('site_title'); ?> </title>
        <meta  charset="UTF-8"/>
        <link rel=icon href="<?php echo base_url('assets/img/logo_moh_2.jpg'); ?>" type="image/jpeg" />
        <script src="<?PHP echo base_url(); ?>assets/plugins/jquery/jquery-2.2.3.min.js"></script>
        <link  rel="stylesheet" href="<?PHP echo base_url(); ?>assets/plugins/bootstrap/css/bootstrap.min.css"/>
        <link  rel="stylesheet" href="<?PHP echo base_url(); ?>assets/plugins/select2/css/select2.min.css"/>
        <script src="<?PHP echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?PHP echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
        <?php
        ?>
        <style type="text/css">

        </style>
    </head>
    <body>
        <div class="navbar navbar-default" style="height: 135px;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="javascript:void(0)" style="padding:0px;padding-top:4px;padding-left:1px;"><img src="<?php echo base_url(); ?>assets/img/logo_moh.jpg" style="width:160px;height:130px;"/></a>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <?php
                            echo form_open('/template/load_lang', 'class="navbar-form"');
                            ?>
                            <label class=""  for="select_user_lang"><?= lang('label_select_lang') ?> </label>
                            <select class="form-control" id="select_user_lang" name = "language" onchange = "javascript:this.form.submit();">
                                <?php
                                echo $user_lang;
                                ?>
                            </select>
                            <?php
                            form_close();
                            ?>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="<?php echo base_url(); ?>"><?php echo lang('menu_label.summary'); ?></a></li>
                        <li><a href="<?php echo base_url(); ?>trends"><?= lang('menu_label.trends'); ?></a></li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"> <?= lang('menu_label.county-sub-county'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>county"><?= lang('menu_label.county'); ?></a></li>
                                <li><a href="<?php echo base_url(); ?>county/pmtct"> <?= lang('menu_label.county_PMTCT'); ?></a></li>
                                <li><a href="<?php echo base_url(); ?>county/subCounty"><?= lang('menu_label.sub-county'); ?> </a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?= lang('menu_label.partners'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>partner"><?= lang('menu_label.summary'); ?></a></li>
                                <li><a href="<?php echo base_url(); ?>partner/trends"><?= lang('menu_label.trends'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/sites"><?= lang('menu_label.partner_facilities'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/age"><?= lang('menu_label.partner_age'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/regimen"><?= lang('menu_label.partner_regimen'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/counties"><?= lang('menu_label.partner_counties'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/current"><?= lang('menu_label.partner_current_supp'); ?> </a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>labs"><?= lang('menu_label.lab_perform'); ?></a></li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?= lang('menu_label.facilities'); ?> 
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>sites"><?= lang('menu_label.facilities'); ?> </a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?= lang('menu_label.non_supp'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>suppression/nosuppression"><?= lang('menu_label.non_supp'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>regimen"><?= lang('menu_label.regimen_analysis'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>age"><?= lang('menu_label.age_analysis'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>sample"><?= lang('menu_label.sample_analysis'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>current"><?= lang('menu_label.current_supp'); ?> </a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>csv"><?= lang('menu_label.import_file'); ?> </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="col-md-9">
                <form></form>
                <h3><?= lang("load.csv.file"); ?></h3>
                <form class="form-horizontal" role="form" method="post" id="import_csv" action="<?php echo base_url(); ?>csv/upload" enctype="multipart/form-data">
                    <div class="form-group ">
                    </div>
                    <div class="form-group">
                        <label for="csv_file" class="control-label col-sm-2"><?= lang("select.csv.file"); ?></label>
                        <div class="form-group col-sm-7">
                            <input class="form-control" type="file" name="csv_file" id="csv_file" required accept=".csv"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-3">
                            <button type="submit" name="import_csv_btn" class="btn btn-primary" id="import_csv_btn"><?= lang("import.csv"); ?></button>
                        </div>
                        <div class="col-sm-4" id="loader">

                        </div>
                    </div>
                </form>
                <br/>
                <?PHP if(isset($_SESSION['uploaded']) && $_SESSION['uploaded']==TRUE) {?>
                <div class=" well well-sm" id="imported_csv_data">
                    <?PHP
                    if($_SESSION['success'] == TRUE){
                        echo $_SESSION['message'].'<br/>';
                        echo lang('rows.num').' '.$_SESSION['nbread'].'<br/>';
                        echo lang('processing.time').' '.$_SESSION['time'].' s<br/>';
                    }else{
                        echo $_SESSION['message'].'<br/>';
                    }
                    ?>
                </div>
                <?PHP }
                ?>
            </div>
            <div class="col-md-3">
                <ul>
                    <li><a href="<?php echo base_url(); ?>csv/refresh"><?= lang('menu.refresh'); ?> </a></li>
                    <li><a href="<?php echo base_url(); ?>csv/import_history"><?= lang('menu.import.history'); ?> </a></li>
                </ul>
            </div>
        </div>
    </body>
</html>
