<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!-- about this site -->
        <meta name="description" content="<?=lang('site_description');?>" />
        <meta name="keywords" content="<?=lang('site_keywords');?>" />
        <meta name="author" content="<?=lang('site_author');?>">
        <meta name="Resource-type" content="Document">
        <?php
        $this->load->view('utils/dynamicLoads');
        ?>
        <link rel=icon href="<?php echo base_url('assets/img/logo_moh_2.jpg'); ?>" type="image/jpeg" />
        <title>
            <?=lang('site_title');?>
        </title>
        <style type="text/css">
            @import url("https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700");
            @import url("https://fonts.googleapis.com/css?family=Roboto:400,300,500,700");
            /*
             *
             *   INSPINIA - Responsive Admin Theme
             *   version 2.6.2
             *
            */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                font-weight: 100;
            }
            h1 {
                font-size: 30px;
            }
            h2 {
                font-size: 24px;
            }
            h3 {
                font-size: 16px;
            }
            h4 {
                font-size: 14px;
            }
            h5 {
                font-size: 12px;
            }
            h6 {
                font-size: 10px;
            }
            h3,
            h4,
            h5 {
                margin-top: 5px;
                font-weight: 600;
            }
            .navbar-inverse {
                border-radius: 0px;
            }
            .navbar .container-fluid .navbar-header .navbar-collapse .collapse .navbar-responsive-collapse .nav .navbar-nav {
                border-radius: 0px;
            }
            .panel {
                border-radius: 0px;
            }
            .panel-primary {
                border-radius: 0px;
            }
            .panel-heading {
                border-radius: 0px;
            }
            .btn {
                margin: 0px;
            }
            .alert {
                margin-bottom: 0px;
                padding: 8px;
            }
            .filter {
                margin: 2px 20px;
            }
            #filter {
                background-color: white;
                margin-bottom: 1.2em;
                margin-right: 0.1em;
                margin-left: 0.1em;
                padding-top: 0.5em;
                padding-bottom: 0.5em;
            }
            #year-month-filter {
                font-size: 12px;
            }
            .nav {
                color: black;
            }
         .list-group{
            overflow: auto;
        }
        </style>
    </head>
    <body>
        <?php //echo "<pre>";print_r($_SERVER['REQUEST_URI']);die();?>
        <!-- Begining of Navigation Bar -->
        <div class="navbar navbar-default" style="height: 85px;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="javascript:void(0)" style="padding:0px;padding-top:4px;padding-left:1px;"><img src="<?php echo base_url(); ?>assets/img/logo_moh.jpg" style="width:100px;height:80px;"/></a>
                </div>
                <div class="navbar-collapse collapse navbar-responsive-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <form action = "<?php echo base_url(); ?>/template/load_lang" class="navbar-form" method="post" accept-charset="utf-8">
                                <label class=""  for="select_user_lang"><?= lang('label_select_lang') ?> </label>
                                <select class="form-control" id="select_user_lang" name = "language" onchange = "javascript:this.form.submit();">
                                    <?php
                                    echo $user_lang;
                                    ?>
                                </select>
                                </form>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-left">
                        <li><a href="<?php echo base_url(); ?>"><?php echo lang('menu_label.summary'); ?></a></li>
                        <li><a href="<?php echo base_url(); ?>trends"><?=lang('menu_label.trends'); ?></a></li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"> <?=lang('menu_organization'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>county"><?=lang('menu_label.county'); ?></a></li>
                                <li><a href="<?php echo base_url(); ?>county/subCounty"><?=lang('menu_label.sub-county'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>sites"><?=lang('menu_label.facilities'); ?> </a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?=lang('menu_label.partners'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>partner"><?=lang('menu_label.summary'); ?></a></li>
                                <li><a href="<?php echo base_url(); ?>partner/trends"><?=lang('menu_label.trends'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/sites"><?=lang('menu_label.partner_facilities'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/age"><?=lang('menu_label.partner_age'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/regimen"><?=lang('menu_label.partner_regimen'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>partner/counties"><?=lang('menu_label.partner_counties'); ?> </a></li>
                              <!--   <li><a href="<?php echo base_url(); ?>partner/pmtct"><?=lang('menu_label.partner_PMTCT'); ?> </a></li> -->
                                <li><a href="<?php echo base_url(); ?>partner/current"><?=lang('menu_label.partner_current_supp'); ?> </a></li>
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>labs"><?=lang('menu_label.lab_perform'); ?></a></li>
                        <li class="dropdown">
                            <a href="bootstrap-elements.html" data-target="#" class="dropdown-toggle" data-toggle="dropdown"><?=lang('menu_label.non_supp'); ?>
                                <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo base_url(); ?>suppression/nosuppression"><?=lang('menu_label.non_supp'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>regimen"><?=lang('menu_label.regimen_analysis'); ?> </a></li>
                                <li><a href="<?php echo base_url(); ?>age"><?=lang('menu_label.age_analysis'); ?> </a></li>
                                <!--<li><a href="<?php echo base_url(); ?>sample"><?=lang('menu_label.sample_analysis'); ?> </a></li>-->
                                <li><a href="<?php echo base_url(); ?>current"><?=lang('menu_label.current_supp'); ?> </a></li>
                               <!--  <li><a href="<?php echo base_url(); ?>pmtct"><?=lang('menu_label.PMTCT_analysis'); ?> </a></li> -->
                            </ul>
                        </li>
                        <li><a href="<?php echo base_url(); ?>csv"><?=lang('menu_label.import_file'); ?> </a></li>
                        <li><a href=""><?=lang('menu_help'); ?> </a></li>
                        <!-- <li><a href="<?php echo base_url(); ?>live"><?=lang('menu_label.live_data'); ?> </a></li>
                        <li><a href="<?php echo base_url(); ?>contacts"><?=lang('menu_label.contact'); ?> </a></li> -->
                        <!-- <li><a href="<?php echo base_url(); ?>county">County View</a></li> -->
                        <!-- <li><a href="http://eid.nascop.org/login.php"><?=lang('menu_label.login'); ?> </a></li>
                        <li><a href="http://eid.nascop.org"><?=lang('menu_label.eid_view'); ?></a></li> -->
                    </ul>
                </div>
            </div>
        </div>
        <!-- End of Navigation Bar -->
        <!-- Begining of Dashboard area -->
        <div class="container-fluid">
<!--            <h3 class="center alert alert-danger" style="text-align: center;color: #f71818;">
                <b class="glyphicon glyphicon-alert"></b> &nbsp;<span> Données en cours de consolidation ...</span>
            </h3>-->
