<style type="text/css">
    #age_dropdown{
        margin-bottom: 1em;
    }
</style>
<center>
    <div id="age_dropdown">
        <select class="btn btn-primary js-example-basic-single" style="background-color: #C5EFF7;" id="regimen" name="regimen">
            <option value="0" disabled="true" selected="true"><?= lang('label.select_regimen') ?></option>
            <option value="NA"><?= lang('label.all_regimen') ?></option>
            <!-- <optgroup value="Counties"> -->
            <?php echo $regimen; ?>
            <!-- </optgroup> -->
        </select>
    </div>
    <!-- <div> All Age Categories </div> -->
</center>
<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('title_tested_done_by_reg') ?> 
                        </div>
                        <div class="display_date"></div>
                    </div> 
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_vl_reg" onclick="switch_source_vl_regimen1()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                    </div>
                </div>


                <div class="panel-body" id="regimen_outcomes">
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
                <div class="panel-heading chart_title">
                    <?= lang('title_test_done_by_sample') ?> <div class="display_range"></div>
                </div>
                <div class="panel-body" id="samples">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-3 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.vl_outcomes') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="vlOutcomes">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_tested_patients_by_gender') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="gender">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_tested_patients_by_age') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="age">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <!-- Map of the country -->
        <!-- <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="panel panel-default">
                  <div class="panel-heading" id="heading">
                        County Outcomes <div class="display_date"></div>
                  </div>
                  <div class="panel-body" id="county">
                    <center><div class="loader"></div></center>
                  </div>
                </div>
        </div> -->
    </div>

</div>

<?php $this->load->view('partner_regimen_view_footer'); ?>