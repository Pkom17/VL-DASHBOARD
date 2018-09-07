<div class="row">
    <div id="justification_chart" style="height:450px;" class="col-md-4">

    </div>
    <div class="col-md-2">

    </div>
    <div id="justification_chart_percentage" style="height:450px;" class="col-md-4">

    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('#justification_chart').highcharts({
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
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    },
                    y: -20
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 25,
                floating: true,
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
                //'#2f80d1',
                '#2f80d1',
                '#e8ee1d'
            ],
            series: <?php echo json_encode($outcomes['just_breakdown']); ?>
        });

        $('#justification_chart_percentage').highcharts({
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
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    },
                    y: -20
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 25,
                floating: true,
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
                    stacking: 'percent',
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
                '#2f80d1'
            ],
            series: <?php echo json_encode($outcomes['just_breakdown']); ?>
        });
    });
</script>