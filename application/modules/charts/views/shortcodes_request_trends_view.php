<div id="request_trends_grraph"></div>
<script type="text/javascript">
	$(function () {
	    $('#request_trends_grraph').highcharts({
	        title: {
	            text: '',
	        },
	        subtitle: {
	            text: '',
	            x: -20
	        },
	        xAxis: {
	            categories:  <?php echo json_encode($outcomes['categories']);?>
	        },
	        yAxis: {
	            title: {
	                text: ''
	            },
	            plotLines: [{
	                value: 0,
	                width: 1,
	                color: '#808080'
	            }]
	        },
	        legend: {
	            enabled: false
	        },
	        series: <?php echo json_encode($outcomes['requests']);?>
	    });
	});
</script>