<style type="text/css">
    .display_date {
        width: 130px;
        display: inline;
    }
    .display_range {
        width: 130px;
        display: inline;
    }

</style>
<div id="first">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading chart_title">
                <?= lang('label.labs_performance.stat') ?>  <div class="display_date"></div>
            </div>
            <div class="panel-body" id="lab_perfomance_stats">
                <center><div class="loader"></div></center>
            </div>
        </div>
    </div>


    <div class="col-md-12">
        <!-- Map of the country -->
        <div style="color:red;"><center><?= lang('label.labs_click_labs_legende_view_labs') ?></center></div>
        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.labs_testing.trends') ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="test_trends">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.labs_routine.vls_tested.sampletype') ?>  <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="samples">
                    <div><?= lang('label.loading') ?></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Map of the country -->
    <!-- Map of the country -->
    <!-- <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
              <div class="panel-heading">
    <?= lang('label.labs_rejection.trends') ?> <div class="display_date" ></div>
              </div>
              <div class="panel-body" id="rejected">
                    <center><div class="loader"></div></center>
              </div>
              
            </div>
    </div> -->
    <!-- Map of the country -->
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading chart_title">
                <?= lang('label.labs_turn_around.time') ?> <div class="display_date" ></div>
            </div>
            <div class="panel-body" id="ttime">
                <div></div>
            </div>

        </div>
    </div>
    <div class="col-md-12">
        <!-- Map of the country -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">

                <div class="panel-heading" style="min-height: 4em;">
                    <div class="col-sm-7">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('title_test_lab_outcomes') ?> 
                        </div>
                        <div class="display_date"></div>
                    </div> 
                    <div class="col-sm-5">
                        <input type="submit" class="btn btn-primary switchButton" id="switchButton_lab" onclick="switch_source_lab1()" value="<?= lang('label.switch_routine_tests_trends'); ?>"> 
                    </div>
                </div>

                <div class="panel-body" id="results">
                    <div><?= lang('label.loading') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<br/> <br/>
<div id="second">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-heading chart_title">
                <?= lang('label.vl_outcomes') ?> <div class="display_date" ></div>
            </div>
            <div class="panel-body" id="lab_summary">
                <div></div>
            </div>

        </div>
    </div>
    <br/> <br/>
    <div class="col-md-12">
        <div id="graphs">

        </div>
    </div>

</div>
<!-- 
<div id="third">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
              <div class="panel-heading">
<?= lang('label.labs_samples.rejections') ?> <div class="display_date"></div>
              </div>
              <div class="panel-body" id="lab_rejections">
                <center><div class="loader"></div></center>
              </div>
            </div>
          </div>
        </div>
        
</div> -->

<?php $this->load->view('labs_summary_view_footer'); ?>