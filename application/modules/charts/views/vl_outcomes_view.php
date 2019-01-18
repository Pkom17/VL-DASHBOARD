<div class="" id="vl_outcomes_pie_tests">
    <div class="col-md-12" id="vlOutcomes_pie_tests" style="height: 360px;"></div>
    <center>
        <table class="table table-bordered">
            <?php echo $outcomes['ul']; ?>
        </table>
    </center>
</div>
<div class="" id="vl_outcomes_pie_pat">
    <div class="col-md-12" id="vlOutcomes_pie_pat" style="height: 360px;width: 100%;"></div>
    <center>
        <table class="table table-bordered">
            <?php  echo $outcomes['ul2']; ?>
        </table>
    </center>
</div>

<script type="text/javascript">
    $().ready(function () {
       $("#vl_outcomes_pie_pat").hide();
        $("table").tablecloth({
            striped: true,
            sortable: false,
            condensed: true
        });
    });
    $(function () {
        $('#vlOutcomes_pie_tests').highcharts({
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
                        format: '<b> {point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [<?php echo json_encode($outcomes['vl_outcomes']); ?>]

        });
        $('#vlOutcomes_pie_pat').highcharts({
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
                        format: '<b> {point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [<?php echo json_encode($outcomes['vl_outcomes2']); ?>]

        });
    });
</script>
<style type="text/css">
    td {
        padding: 0px;
    }
</style>