<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" >
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('label.subcounties_outcomes') ?>
                        </div>
                        <div class="display_date"></div>
                    </div> 
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_subcounty" onclick="switch_source_vl_subcounty1()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                    </div>
                </div>
                <div class="panel-body" id="regimen_outcomes">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-12" >
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="chart_title">
                        <?= lang('label.subcounties') ?> 
                    </div>
                    <div class="display_date"></div>
                </div>
                <div class="panel-body" id="subcounty_summary">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="second">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div id="samples_heading"><?= lang('title_test_done_by_sample') ?></div> <div class="display_range"></div>
                </div>
                <div class="panel-body" id="samples">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
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
        <div class="col-md-4 col-sm-4 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 6em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_gender_heading">
                            <?= lang('title_tested_patients_by_gender'); ?>
                        </div>
                        <div class="display_date"></div>
                    </div>
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-sm btn-primary switchButton" id="switchButton_gender" onclick="switch_source_gender1()" value="<?= lang('label.switch_all_tests'); ?>">
                    </div>
                </div>
                <div class="panel-body" id="gender">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 6em;">
                    <div class="col-sm-8">
                        <div class="chart_title vl_age_heading">
                            <?= lang('title_tested_patients_by_age'); ?>
                        </div>
                        <div class="display_date"></div>
                    </div>
                    <div class="col-sm-4">
                        <input type="submit" class="btn btn-sm btn-primary switchButton" id="switchButton_age" onclick="switch_source_age1()" value="<?= lang('label.switch_all_tests'); ?>">
                    </div>
                </div>
                <div class="panel-body" id="age">
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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" id="heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_county_heading">
                            <?= lang('title_test_done_by_site') ?>
                        </div>
                        <div class="display_date"></div>
                    </div> 
                </div>
                <div class="panel-body" id="sub_counties">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>


</div>

<?php $this->load->view('subcounty_view_footer'); ?>