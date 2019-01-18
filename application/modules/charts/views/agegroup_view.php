<?PHP if ($div_name != 'current_sup_age') { ?>
    <div id="ageGroups_pie_tests" style="height:450px;">
    </div>
<?PHP } ?>
<div id="ageGroups_pie_pat" style="height:450px;">

</div>
<script type="text/javascript">
    $(function () {
<?PHP if ($div_name != 'current_sup_age') { ?>
            $("#ageGroups_pie_tests").hide();
<?PHP } ?>
        $('#ageGroups_pie_tests').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo json_encode($outcomes['categories']); ?>,
                crosshair: true
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
                verticalAlign: 'bottom',
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
                //'#F2784B',
                //'#1BA39C',
                '#e8ee1d',
                '#2f80d1',
                '#00ff99',
                '#000000'
            ],
            series: <?php echo json_encode($outcomes['ageGnd']); ?>
        });
        $('#ageGroups_pie_pat').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo json_encode($outcomes['categories']); ?>,
                crosshair: true
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
                verticalAlign: 'bottom',
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
            series: <?php echo json_encode($outcomes['ageGnd2']); ?>
        });
    });
</script>