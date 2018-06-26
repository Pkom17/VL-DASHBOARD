<div class="panel-body">
	<div id="vlOutcomes_pie" style="height: 300px;"></div>
</div>
<div>
	<!-- <ul>
		<?php echo $outcomes['ul'];?>
	</ul> -->
	<center>
	    <table>
	    	<?php echo $outcomes['ul'];?>
	    </table>
	</center>
</div>

<script type="text/javascript">
	$().ready(function(){
		$("table").tablecloth({
	      striped: true,
	      sortable: false,
	      condensed: true
	    });
	});
	// $().ready(function() {
	// 	$.get("<?php //echo base_url('charts/summaries/suppressiondata');?>", function(data) {

	// 	});
	// 	$("#samples").load("<?php //echo base_url('charts/summaries/sample_types'); ?>");
	// });

	$(function () {
				$('#vlOutcomes_pie').highcharts({
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
<style type="text/css">
	td {
		padding: 0px;
	}
</style>