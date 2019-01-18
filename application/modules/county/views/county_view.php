<div class="row">
    <!-- Map of the country -->

</div>

<div id="first">
    <div class="row">
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
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="chart_title">
                        <?= lang('label.counties_cap') ?>  </div><div class="display_date"></div>
                </div>
                <div class="panel-body" id="county_sites">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="second">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12" id="subcounty">
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="chart_title">
                        <?= lang('label.sub-counties') ?>
                    </div>
                    <div class="display_date"></div>
                </div>
                <div class="panel-body" id="sub_counties">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="chart_title">
                        <?= lang('label.partners') ?>
                    </div>
                    <div class="display_date"></div>
                </div>
                <div class="panel-body" id="partners">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('county_view_footer'); ?>