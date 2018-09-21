<div id="sample_types"></div>
<div class="row" id="excels">
    <div class="col-md-6 col-md-offset-3">
        <center><a href="<?php echo $link; ?>"><button id="download_link" class="btn btn-primary" style="background-color: #2f80d1;color: white;"><?= lang('label.export_excel') ?></button></a></center>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#sample_types').highcharts({
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            xAxis: [{
                    categories: <?php echo json_encode($outcomes['categories']); ?>
                }],
            yAxis: [{// Primary yAxis
                    labels: {
                        formatter: function () {
                            return this.value + '%';
                        },
                        style: {

                        }
                    },
                    title: {
                        text: 'Percentage',
                        style: {
                            color: '#89A54E'
                        }
                    },
                    opposite: true
                }, {// Secondary yAxis
                    gridLineWidth: 0,
                    title: {
                        text: 'Tests',
                        style: {
                            color: '#4572A7'
                        }
                    },
                    labels: {
                        formatter: function () {
                            return this.value + '';
                        },
                        style: {
                            color: '#4572A7'
                        }
                    }
                    // min: 0, 
                    // max: 70000,
                    // tickInterval: 1
                }],
            tooltip: {
                borderRadius: 2,
                borderWidth: 1,
                borderColor: '#999',
                shadow: false,
                shared: true,
                useHTML: true,
                yDecimals: 0,
                valueDecimale: 0,
                headerFormat: '<table class="tip"><caption>{point.key}</caption>' + '<tbody>',
                pointFormat: '<tr><th style="color:{series.color}">{series.name}:</th>' + '<td style="text-align:right">{point.y}</td></tr>',
                footerFormat: '<tr><th><?= lang('label.total_') ?></th>' + '<td style="text-align:right"><b>{point.total}</b></td></tr>' + '</tbody></table>'
                        // formatter: function() {
                        // 	return this.value;
                        // }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                x: -100,
                verticalAlign: 'top',
                y: 40,
                floating: true,
                backgroundColor: '#FFFFFF'
            }, navigation: {
                buttonOptions: {
                    verticalAlign: 'bottom',
                    y: -20
                }
            },
            colors: [
                '#AA9999',
                '#E26A6A',
                '#A15B98',
                '#913D88'
            ],
            series: <?php echo json_encode($outcomes['sample_types']); ?>
        });
    });

</script>