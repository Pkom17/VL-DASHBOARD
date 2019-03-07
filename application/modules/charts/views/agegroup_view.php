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
                '#e8ee1d',
                '#2f80d1',
                '#00ff99',
                '#000000'
            ],
            series: <?php echo json_encode($outcomes['ageGnd']); ?>
        });
        // ---
        $('#ageGroups_pie_pat').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: <?php echo json_encode($outcomes['categories']); ?>
            },
            yAxis: [{
                    gridLineWidth: 1,
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
                {
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
                }
            ],
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
                shared: true
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            }, colors: [
                '#f72109',
                '#DAA520', //'#40bf80',
                '#00ff99'
            ],
            series: <?php echo json_encode($outcomes['ageGnd2']); ?>
        });
    });
</script>