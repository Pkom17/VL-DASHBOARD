<style type="text/css">
    #age_dropdown{
        margin-bottom: 1em;
    }
</style>
<center>
    <div id="age_dropdown">
        <select class="btn btn-primary js-example-basic-single" style="background-color: #C5EFF7;" id="age_category" name="age_category">
            <option value="0" disabled="true" selected="true"><?= lang('label.select_age_category:') ?></option>
            <option value="NA"><?= lang('label.all_age_categories') ?></option>
            <?php echo $age_filter; ?>
        </select>
    </div>
</center>
<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('title_tested_patients_by_age') ?> 
                        </div>
                        <div class="display_date"></div>
                    </div> 
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_vl_age" onclick="switch_source_vl_p_age1()" value="<?= lang('label.switch_all_tests'); ?>"> 
                    </div>
                </div>
                <div class="panel-body" id="age_outcomes">
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
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.vl_outcomes') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="vlOutcomes">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_tested_patients_by_gender') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="gender">
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


<?php $this->load->view('partner_age_view_footer'); ?>