<?PHP
$n = '';
if ($div_name == 'quarterly_trends') {
    $n = 'q';
} elseif ($div_name == 'partner_county_outcomes') {
    $n = 'pvl';
} elseif ($div_name == 'regimen_county_outcomes') {
    $n = 'rco';
} elseif ($div_name == 'age_county_outcomes') {
    $n = 'aco';
}
?>
<div id="<?php echo $div_name; ?>">
    <?PHP if ($div_name == 'age_summary_outcomes_sup' || $div_name == 'regimen_summary_outcomes_sup' || $div_name == 'regimen_county_outcomes' || $div_name == 'age_county_outcomes') { ?>
        <div class = "panel-body" id = "vl_county_pie_pat<?PHP echo $n; ?>" style = "height:450px;padding-bottom:0px;">
            <center><div class = "loader"></div></center>
        </div>
        <?php
    } elseif ($div_name == 'county_subcounties_outcomes') {
        $n = 's';
        ?>
        <div class="col-md-6 col-sm-12 col-xs-12" >
            <div class="panel panel-default">
                <div class="panel-heading" id="heading" style="min-height: 4em;">
                    <div class="col-sm-12">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('title_test_done_by_subcounty') ?>
                        </div>
                        <div class="display_date"></div>
                    </div> 
                </div>
                <div class="panel-body" id="vl_county_pie_tests<?PHP echo $n; ?>" >
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xs-12" >
            <div class="panel panel-default">
                <div class="panel-heading" id="heading" style="min-height: 4em;">
                    <div class="col-sm-12">
                        <div class="chart_title vl_subcounty_heading">
                            <?= lang('title_tested_patients_by_subcounty') ?>
                        </div>
                        <div class="display_date"></div>
                    </div> 
                </div>
                <div class="panel-body" id="vl_county_pie_pat<?PHP echo $n; ?>" >
                    <center><div class="loader"></div></center>
                </div>
            </div>
        </div>
        <?PHP
    } else {
        //
        ?>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <?PHP if ($div_name != 'current_sup_gender' && $div_name != 'current_sup_age') { ?>
                <div class="panel-body" id="vl_county_pie_tests<?PHP echo $n; ?>" style="height:450px;padding-bottom:0px;">
                    <center><div class="loader"></div></center>
                </div>
            <?PHP } ?>
            <?PHP if ($div_name != 'quarterly_trends' && $div_name != 'partner_trends') { ?>
                <div  class="panel-body" id="vl_county_pie_pat<?PHP echo $n; ?>" style="height:450px;padding-bottom:0px;">
                    <center><div class="loader"></div></center>
                </div>
            </div>
        <?PHP } ?>
    </div>
<?PHP } ?>
<br/>
<script type="text/javascript">
    $(function () {
<?PHP if ($n == 's' || $div_name == 'age_summary_outcomes_sup' || $div_name == 'regimen_summary_outcomes_sup' || $div_name == 'regimen_county_outcomes' || $div_name == 'age_county_outcomes') { ?>
        $("#vl_county_pie_pat<?PHP echo $n; ?>").show();
<?PHP } else {
    ?>
        $("#vl_county_pie_pat<?PHP echo $n; ?>").hide();
    <?PHP
}
?>
    $('#vl_county_pie_tests<?PHP echo $n; ?>').highcharts({
    plotOptions: {
    column: {
    stacking: 'normal'
    }
    },
            chart: {
            zoomType: 'xy'
            },
            title: {
            text: '<?php echo $trends['title']; ?>'
            },
            xAxis: [{
            categories: <?php echo json_encode($trends['categories']); ?>
            }],
            yAxis: [{// Primary yAxis
            min: 0,
                    max: 100,
                    labels: {
                    formatter: function () {
                    return '';
                    },
                            style: {

                            }
                    },
                    gridLineWidth: 0,
                    title: {
                    text: '',
                            style: {

                            }
                    },
                    opposite: true

            }, {// Secondary yAxis && $div_name != 'summary_county_outcomes_patient'
<?PHP if ($div_name != 'subcounty_subcounties_outcomes' && $div_name != 'site_sites_outcomes') { ?>
                stackLabels: {
                enabled: true
                },
<?PHP } ?>
            gridLineWidth: 1,
                    title: {
                    text: '<?= lang('label.tests') ?>',
                            style: {
                            color: '#4572A7'
                            }
                    },
                    labels: {
                    formatter: function () {
                    return this.value + '';
                    },
                            style: {
                            color: '#4572A7'
                            }
                    }
            // min: 0, 
            // max: 70000,
            // tickInterval: 1
            }],
            tooltip: {
            shared: true
            },
            legend: {
            layout: 'horizontal',
                    align: 'right',
                    verticalAlign: 'bottom',
                    floating: false,
                    backgroundColor: '#FFFFFF'
            }, colors: [
            '#e8ee1d',
            '#2f80d1',
            '#00ff99',
            '#000000',
            '#257766'
    ],
            series: <?php echo json_encode($trends['outcomes']); ?>
    });
    $('#vl_county_pie_pat<?PHP echo $n; ?>').highcharts({
    plotOptions: {
    column: {
    stacking: 'normal'
    }
    },
            chart: {
            zoomType: 'xy'
            },
            title: {
            text: '<?php echo $trends['title']; ?>'
            },
            xAxis: [{
            categories: <?php echo json_encode($trends['categories']); ?>
            }],
            yAxis: [
            {
            // Primary yAxis
            min: 0,
                    max: 100,
                    labels: {
                    formatter: function () {
                    return this.value + '%';
                    },
                            style: {
                            color: '#89A54E'
                            }
                    },
                    title: {
                    text: '<?= lang('label.percentage') ?>',
                            style: {
                            color: '#89A54E'
                            }
                    },
                    opposite: true
            },
            {// Secondary yAxis
<?PHP if ($div_name != 'subcounty_subcounties_outcomes' && $div_name != 'site_sites_outcomes') { ?>
                stackLabels: {
                enabled: true
                },
<?PHP } ?>
            gridLineWidth: 1,
                    title: {
                    text: '<?= lang('label.tests') ?>',
                            style: {
                            color: '#4572A7'
                            }
                    },
                    labels: {
                    formatter: function () {
                    return this.value + '';
                    },
                            style: {
                            color: '#4572A7'
                            }
                    }
            // min: 0, 
            // max: 70000,
            // tickInterval: 1
            }],
            tooltip: {
            shared: true
            },
            legend: {
            layout: 'horizontal',
                    align: 'right',
                    verticalAlign: 'bottom',
                    floating: false,
                    backgroundColor: '#FFFFFF'
            }, colors: [
            '#f72109',
            '#DAA520', //'#40bf80',
            '#00ff99'
    ],
            series: <?php echo json_encode($trends['outcomes2']); ?>
    });
    });

</script>
