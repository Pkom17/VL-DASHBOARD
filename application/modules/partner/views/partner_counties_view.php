<style type="text/css">
    .display_date {
        width: 130px;
        display: inline;
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
    #excels {
        padding-top: 0.5em;
        padding-bottom: 2em;
    }
</style>

<div class="row" id="partners_all">
    <div class="col-md-12">
        <!--        <div class="panel panel-default">
                    <div class="panel-heading" style="min-height: 4em;">
                        <div class="col-sm-7">
                            <div class="chart_title vl_subcounty_heading">
        <?= lang('title_test_partner_outcomes') ?> 
                            </div>
                            <div class="display_date"></div>
                        </div> 
                        <div class="col-sm-5">
                            <input type="submit" class="btn btn-primary switchButton" id="switchButton_cpart1" onclick="switch_source_cpart1()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                        </div>
                    </div>
        
                    <div class="panel-body" id="partnerOutcomes">
                        <center><div class="loader"></div></center>
                    </div>
                </div>-->
        <div class="panel panel-default">
            <div class="panel-heading chart_title" style="min-height: 4em;"> 
                <?= lang('title_partners_counties') ?> <div class="display_date"></div>
            </div>
            <div class="panel-body" id="partnerCountiesAll">
                <center><div class="loader"></div></center>
            </div>
            <hr>
            <hr>
        </div>
    </div>
</div>

<div class="row" id="partner_counties">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 4em;">
                <div class="col-sm-7">
                    <div class="chart_title vl_subcounty_heading">
                        <?= lang('title_test_done_by_county') ?> 
                    </div>
                    <div class="display_date"></div>
                </div> 
                <div class="col-sm-5">
                    <input type="submit" class="btn btn-primary switchButton" id="switchButton_cpart2" onclick="switch_source_cpart2()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                </div>
            </div>
            <div class="panel-body" id="partnerCountyOutcomes">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading chart_title" style="min-height: 4em;"> 
                <?= lang('label.partners_counties') ?> <div class="display_date"></div>
            </div>
            <div class="panel-body" id="partnerCounties">
                <center><div class="loader"></div></center>
            </div>
            <hr>
            <hr>
        </div>
    </div>
</div>
<?php
$this->load->view('partner_counties_footer_view')?>