
<?PHP if ($div_name != 'current_sup_gender') { ?>
    <div id="gender_pie_tests" style="height:540px;">
    </div>
<?PHP } ?>
<div id="gender_pie_pat" style="height:540px;">

</div>

<script type="text/javascript">
    $(function () {
        <?PHP if ($div_name != 'current_sup_gender') { ?>
        $("#gender_pie_tests").hide();
        <?PHP } ?>
        $('#gender_pie_tests').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo json_encode($outcomes['categories']); ?>
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?= lang('label.tests') ?>'
                },
                stackLabels: {
                    rotation: 0,
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    },
                    y: -10
                }
            },

            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>% <?= lang('label.contribution') ?>  {point.percentage:.1f}%'
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                        style: {
                            textShadow: '0 0 3px black'
                        }
                    }
                }
            }, colors: [
                '#e8ee1d',
                '#2f80d1',
                '#00ff99',
                '#000000'
            ],
            series: <?php echo json_encode($outcomes['gender']); ?>
        });
        $('#gender_pie_pat').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo json_encode($outcomes['categories']); ?>
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?= lang('label.tests') ?>'
                },
                stackLabels: {
                    rotation: 0,
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    },
                    y: -10
                }
            },

            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>% <?= lang('label.contribution') ?>  {point.percentage:.1f}%'
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: false,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                        style: {
                            textShadow: '0 0 3px black'
                        }
                    }
                }
            }, colors: [
                '#f72109',
                '#40bf80',
                '#00ff99'
            ],
            series: <?php echo json_encode($outcomes['gender2']); ?>
        });
    });
</script>