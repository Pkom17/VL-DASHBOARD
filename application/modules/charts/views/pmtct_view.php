<div id="<?= @$div_name; ?>" style="height:450px;">
	
</div>
<script type="text/javascript">
	$(function () {
                $("#<?= @$div_name;?>").highcharts({
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: <?php echo json_encode($outcomes['categories']);?>,
                        crosshair: true
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: '<?= lang('label.tests'); ?>'
                        },
                        stackLabels: {
                            rotation: 0,
                            enabled: true,
                            style: {
                                fontWeight: 'bold',
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                            },
                            y:-10
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
                        pointFormat: '{series.name}: {point.y}<br/>% <?= lang('label_contribution'); ?> {point.percentage:.1f}%'
                    },
                    plotOptions: {
                        column: {
                            stacking: <?php echo json_encode($type);?>,
                            dataLabels: {
                                enabled: false,
                                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                                style: {
                                    textShadow: '0 0 3px black'
                                }
                            }
                        }
                    },colors: [
                        //'#F2784B',
				        //'#1BA39C',
                                        '#e8ee1d',
                                        '#2f80d1'
                    ],
                    series: <?php echo json_encode($outcomes['pmtct']);?>
                });
            });
</script>