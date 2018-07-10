
<div id="jstification_pie">

</div>
<script type="text/javascript">
	$(function(){
	    $('#jstification_pie').highcharts({
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
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            colors:[
            '#E26A6A',
            '#5283E9',
            '#91D388',
            '#883DA1',
            '#AA9999'
            ],
            series: [<?php echo json_encode($outcomes['justification']); ?>]
        });
    });

</script>