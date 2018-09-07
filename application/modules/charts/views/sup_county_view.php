<?php //echo"<pre>";print_r($outcomes); ?>
<div class="panel-body" id="sup_county">
	
</div>
<script type="text/javascript">
	$(function () {
			    $('#sup_county').highcharts({
			        chart: {
			            type: 'column'
			        },
			        title: {
			            text: ''
			        },
			        xAxis: {
			            categories: <?php echo json_encode($suppressions['categories']);?>
			        },
			        yAxis: {
			            min: 0,
			            title: {
			                text: '%'
			            },
			            stackLabels: {
			            	rotation: -75,
			                enabled: true,
			                style: {
			                    fontWeight: 'bold',
			                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
			                },
			                y:-20
			            }
			        },
			        legend: {
			            align: 'right',
			            x: -30,
			            verticalAlign: 'bottom',
			            y: 25,
			            floating: false,
			            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
			            borderColor: '#CCC',
			            borderWidth: 1,
			            shadow: true
			        },
			        tooltip: {
			            headerFormat: '<b>{point.x}</b><br/>',
			            pointFormat: '{series.name}: {point.y}<br/>% <?=  lang('label.contribution')?> {point.percentage:.1f}%'
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
			        },colors: [
				        //'#F2784B',
				        //'#1BA39C',
                                        '#e8ee1d',
                                        '#2f80d1'
				    ],
			        series: <?php echo json_encode($suppressions['sup_county']);?>
			    });
			});
</script>
