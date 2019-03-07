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
</style>

<div id="first">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_site_heading">
                            <?= lang('title_test_done_by_site'); ?>
                        </div>
                        <div class="display_date"></div>
                    </div>
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_site" onclick="switch_source_site1()" value="<?= lang('label.switch_routine_tests_trends'); ?>">
                    </div>
                </div>
                <div class="panel-body" id="siteOutcomes">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="second">
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="chart_title vl_site_heading">
                        <?= lang('label.testing_trends') ?> 
                    </div>
                    <div class="display_range"></div>
                </div>
                <div class="panel-body" id="tsttrends">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="chart_title vl_site_heading">
                        <?= lang('label.testing_trends_by_sample') ?>
                    </div>
                    <div class="display_range"></div>
                </div>
                <div class="panel-body" id="stoutcomes">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_site_vl_heading">
                            <?= lang('label.vl_outcomes'); ?>
                        </div>
                        <div class="display_date"></div>
                    </div>
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_site_vl" onclick="switch_source_vl_site()" value="<?= lang('label.switch_routine_tests_trends'); ?>">
                    </div>
                </div>
                <div id="vlOutcomes">
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
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_site_age" onclick="switch_source_siteAge()" value="<?= lang('label.switch_all_tests'); ?>">
                    </div>
                </div>
                <div class="panel-body" id="ageGroups">
                    <center><div class="loader"></div></center>
                </div>
                <div>
                    <center>
                        <a type="button" class="btn btn-default"  style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;" onclick="switch_age_cat()" > 
                            <?= lang('label_click_to_switch_agecat'); ?></a>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_gender_heading">
                            <?= lang('title_tested_patients_by_gender'); ?>
                        </div>
                        <div class="display_date"></div>
                    </div>
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_site_gender" onclick="switch_source_siteGender()" value="<?= lang('label.switch_all_tests'); ?>">
                    </div>
                </div>
                <div class="panel-body" id="gender" style="padding-bottom:0px;">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>

        <!-- Map of the country -->
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading chart_title" style="min-height: 4em;">
                    <?= lang('label.site_justification_for_tests') ?> <div class="display_date"></div>
                </div>
                <div class="panel-body" id="justification" >
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>


</div>
<div class="row" style="display: none;">
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= lang('label.site_longitudinalpatient_trackg.statistics') ?> <div class="display_date"></div>
            </div>
            <div class="panel-body" id="pat_stats">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= lang('label.site_patients.outcomes') ?> <div class="display_date"></div>
                    </div>
                    <div class="panel-body" id="pat_out">
                        <center><div class="loader"></div></center>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= lang('label.site_patients.graphs') ?> <div class="display_date"></div>
                    </div>
                    <div class="panel-body" id="pat_graph">
                        <center><div class="loader"></div></center>
                    </div>
                </div>
            </div>
        </div>
    </div>	
</div>

</div>


<?php
$this->load->view('sites_view_footer')?>