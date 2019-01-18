<p>
	<?= lang('label.pie.suppress_patient'); ?> : <?php echo number_format($outcomes['total'][0]) ; ?> <br />
	<?= lang('label.pie_non_suppress_patient'); ?> : <?php echo number_format($outcomes['total'][1]) ; ?> <br />
</p>
<div id="<?php echo $div_name; ?>"></div>

<script type="text/javascript">

	$(function () {
				$('#<?php echo $div_name; ?>').highcharts({
			        chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
		            },
		            title: {
		                text: ''
		            },
		            tooltip: {
		                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		            },
		            plotOptions: {
		                pie: {
		                    allowPointSelect: true,
		                    cursor: 'pointer',
		                    dataLabels: {
			                    enabled: true,
			                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
			                    style: {
			                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
			                    }
			                },
		                    showInLegend: true
		                }
		            },
		            series: [<?php echo json_encode($outcomes['vl_outcomes']); ?>]

		        });
		    });
</script>