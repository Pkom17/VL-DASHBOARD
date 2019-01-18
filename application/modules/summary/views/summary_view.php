<style type="text/css">
    .display_date {
        width: 130px;
        display: inline;
    }
    .display_range {
        width: 140px;
        display: inline;
        font-weight: bold;
    }
    #nattatdiv {
        background-color: white;
        margin-right: 1em;
        margin-left: 1em;
        margin-bottom: 1em;
    }
    .title-name {
        color: blue;
    }
    #title {
        padding-top: 1.5em;
    }
    .key {
        font-size: 11px;
        margin-top: 0.5em;
    }
    .cr {
        background-color: rgba(255, 0, 0, 0.498039);
    }
    .rp {
        background-color: rgba(255, 255, 0, 0.498039);
    }
    .pd {
        background-color: rgba(0, 255, 0, 0.498039);
    }
    .cd {
        width: 0px;
        height: 0px;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 8px solid black;
    }   
</style>
<div class="row">
    <div class="col-md-12" id="nattatdiv">
        <div class="col-md-4">
            <div class="col-md-4 title-name" id="title">
                <center><?= lang('label.VL_coverage_percent'); ?> </center>
            </div>
            <div class="col-md-8">
                <div id="coverage"></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="col-md-4 title-name" id="title">
                <center><?= lang('label.national_tat'); ?> <l style="color:red;"><?= lang('label.days'); ?></l></center>
            </div>
            <div class="col-md-8">
                <div id="nattat"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="title-name"><?= lang('label.key'); ?></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="key cr"><center>  <?= lang('label.collection_receipt_cr'); ?></center></div>
                    <div class="key rp"><center> <?= lang('label.receipt_process'); ?></center></div>
                </div>
                <div class="col-md-6">
                    <div class="key pd"><center><?= lang('label.process_dispatch'); ?></center></div>
                    <div class="key"><center><div class="cd"></div><?= lang('label.collection_dispatch_cd'); ?></center></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 4em;">
                <div class="col-sm-4">
                    <div id="samples_heading">
                        <?= lang('label.testing_trends_by_sample'); ?></div> 
                    <div class="display_range"></div>
                </div>
            </div>
            <div class="panel-body" id="samples">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Map of the country -->
    <div class="col-md-7 col-sm-3 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading " style="min-height: 4em;">
                <div class="col-sm-7">
                    <div class="chart_title vl_outcomes_heading"> <?= lang('total_done_test'); ?> </div>
                    <div class="display_date" ></div>
                </div>
                <div class="col-sm-5">
                    <input type="submit" class="btn btn-primary switchButton" id="switchButton2" onclick="switch_source2()" value="<?= lang('label.switch_routine_tests_trends'); ?>">
                </div>
            </div>
            <div class="panel-body" id="vlOutcomes">
                <center><div class="loader"></div></center>
            </div>

        </div>
    </div>
    <div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 4em;">
                <div class="col-sm-7">
                    <div class="chart_title vl_gender_heading">
                        <?= lang('title_tested_patients_by_gender'); ?>
                    </div>
                    <div class="display_date"></div>
                </div>
                <div class="col-sm-5">
                    <input type="submit" class="btn btn-primary switchButton" id="switchButton_gender" onclick="switch_source_gender1()" value="<?= lang('label.switch_all_tests'); ?>">
                </div>
            </div>
            <div class="panel-body" id="gender" style="height:650px;padding-bottom:0px;">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-4 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="chart_title">
                    <?= lang('label.justification_tests') ?> 
                </div>
                <div class="display_date"></div>
            </div>
            <div class="panel-body" id="justification" style="height:500px;">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading" style="min-height: 4em;">
                <div class="col-sm-8">
                    <div class="chart_title vl_age_heading">
                        <?= lang('title_tested_patients_by_age'); ?>
                    </div>
                    <div class="display_date"></div>
                </div>
                <div class="col-sm-4">
                    <input type="submit" class="btn btn-primary switchButton" id="switchButton_age" onclick="switch_source_age1()" value="<?= lang('label.switch_all_tests'); ?>">
                </div>
            </div>
            <div class="panel-body" id="ageGroups">
                <center><div class="loader"></div></center>
            </div>
            <div>
                <center>
                    <a type="button" class="btn btn-default" data-toggle="modal" data-target="#agemodal" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;" > 
                        <?= lang('label.modal.click_breakdown'); ?></a>
                </center>
            </div>
        </div>
    </div>
    <!-- Map of the country -->


</div>
<div class="row" >
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading" id="heading" style="min-height: 4em;">
                <div class="col-sm-7">
                    <div class="chart_title vl_county_heading">
                        <?= lang('title_test_done_by_county') ?>
                    </div>
                    <div class="display_date"></div>
                </div> 
                <div class="col-sm-5">
                    <input type="submit" class="btn btn-primary switchButton" id="switchButton_county" onclick="switch_source_vl_county1()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                </div>
            </div>
            <div class="panel-body" id="county" >
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="agemodal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= lang('label.age_category_breakdown') ?></h4>
                <a type="button" class="close" data-dismiss="modal" aria-label="<?= lang('label.modal_close') ?>"><span aria-hidden="true">&times;</span></a>
            </div>
            <div class="modal-body" id="CatAge">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('summary_view_footer'); ?>