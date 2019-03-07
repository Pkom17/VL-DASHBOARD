<div id="first">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_tested_patients_by_age'); ?> <div class="display_date"></div>
                </div>
                <div class="panel-body" id="age_outcomes">
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
</div>
<div id="second">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_test_done_by_sample'); ?> <div class="display_range"></div>
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
                    <?= lang('label.vl_outcomes'); ?><div class="display_date" ></div>
                </div>
                <div class="panel-body" id="vlOutcomes">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('title_tested_patients_by_gender'); ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="gender">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.counties'); ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="countiesAge">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.partners'); ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="partnersAge">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.sub_counties'); ?><div class="display_date" ></div>
                </div>
                <div class="panel-body" id="subcountiesAge">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
        <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title">
                    <?= lang('label.facilities'); ?> <div class="display_date" ></div>
                </div>
                <div class="panel-body" id="facilitiesAge">
                    <center><div class="loader"></div></center>
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        <!-- Map of the country -->
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading chart_title" id="heading">
                    <?= lang('title_tested_patients_by_county'); ?><div class="display_date"></div>
                </div>
                <div class="panel-body" id="county">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
    </div>

</div>


<?php $this->load->view('age_view_footer'); ?>