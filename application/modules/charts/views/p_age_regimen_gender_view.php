<div id="p_age_gender_pat">
    <div class="col-md-6" id="p_age_gender_pat_male" style="height:540px;padding-right:0;">

    </div>
    <div class="col-md-6"  id="p_age_gender_pat_female" style="height:540px;padding-left:0;">

    </div>
</div>
<div id="p_age_gender_tests">
    <div class="col-md-6"  id="p_age_gender_tests_male" style="height:540px;padding-right:0;">

    </div>
    <div class="col-md-6"  id="p_age_gender_tests_female" style="height:540px;padding-left:0;">

    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#p_age_gender_tests').hide();
        $('#p_age_gender_pat_male').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: '<?= lang('label.male') ?>'
            },
            exporting: {
                buttons: {
                    contextButton: {
                        align: 'left',
                        x: -12
                    }
                }
            },
            xAxis: [{
                    categories: <?php echo json_encode($outcomes['categories']['age']); ?>,
                    reversed: false,
                    opposite: true,
                    labels: {
                        step: 1
                    }
                }
            ],
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    formatter: function () {
                        return Math.abs(this.value);
                    }
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                formatter: function () {
                    var s = '<b>' + this.x + '</b>',
                            sum = 0;
                    $.each(this.points, function (i, point) {
                        s += '<br/> <span style="color: '+point.series.color+'">\u25CF</span> '+ point.series.name + ': ' +
                               '<b>'+Highcharts.numberFormat(Math.abs(point.y),0)+'</b>  ('+ Highcharts.numberFormat(point.percentage,1) +'%)';
                        sum += point.y;
                    });

                    s += '<br/>Total:  <b>' + Highcharts.numberFormat(Math.abs(sum),0)+'</b>';

                    return s;
                },
                shared: true

            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: <?php echo json_encode($outcomes['gender']['male']); ?>
        });
        // ---
        $('#p_age_gender_tests_male').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: '<?= lang('label.male') ?>'
            },
            exporting: {
                buttons: {
                    contextButton: {
                        align: 'left',
                        x: -12
                    }
                }
            },
            xAxis: [{
                    categories: <?php echo json_encode($outcomes['categories']['age']); ?>,
                    reversed: false,
                    opposite: true,
                    labels: {
                        step: 1
                    }
                }
            ],
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    formatter: function () {
                        return Math.abs(this.value);
                    }
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                formatter: function () {
                    var s = '<b>' + this.x + '</b>',
                            sum = 0;
                    $.each(this.points, function (i, point) {
                        s += '<br/> <span style="color: '+point.series.color+'">\u25CF</span> '+ point.series.name + ': ' +
                               '<b>'+Highcharts.numberFormat(Math.abs(point.y),0)+'</b>  ('+ Highcharts.numberFormat(point.percentage,1) +'%)';
                        sum += point.y;
                    });

                    s += '<br/>Total:  <b>' + Highcharts.numberFormat(Math.abs(sum),0)+'</b>';

                    return s;
                },
                shared: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: <?php echo json_encode($outcomes['gender_all']['male']); ?>
        });
        // ---
        $('#p_age_gender_pat_female').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: '<?= lang('label.female') ?>'
            },
            exporting: {
                buttons: {
                    contextButton: {
                        align: 'right',
                        x: 12
                    }
                }
            },
            xAxis: [
                {
                    categories: <?php echo json_encode($outcomes['categories']['age']); ?>,
                    reversed: false,
                    //opposite: true,
                    labels: {
                        step: 1
                    }
                }
            ],
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    formatter: function () {
                        return Math.abs(this.value);
                    }
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                formatter: function () {
                    var s = '<b>' + this.x + '</b>',
                            sum = 0;
                    $.each(this.points, function (i, point) {
                        s += '<br/> <span style="color: '+point.series.color+'">\u25CF</span> '+ point.series.name + ': ' +
                               '<b>'+Highcharts.numberFormat(Math.abs(point.y),0)+'</b>  ('+ Highcharts.numberFormat(point.percentage,1) +'%)';
                        sum += point.y;
                    });

                    s += '<br/>Total:  <b>' + Highcharts.numberFormat(Math.abs(sum),0)+'</b>';

                    return s;
                },
                shared: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: <?php echo json_encode($outcomes['gender']['female']); ?>
        });
        // --- 
        $('#p_age_gender_tests_female').highcharts({
            chart: {
                type: 'bar'
            },
            title: {
                text: '<?= lang('label.female') ?>'
            },
            exporting: {
                buttons: {
                    contextButton: {
                        align: 'right',
                        x: 12
                    }
                }
            },
            xAxis: [{
                    categories: <?php echo json_encode($outcomes['categories']['age']); ?>,
                    reversed: false,
                    //opposite: true,
                    labels: {
                        step: 1
                    }
                }
            ],
            yAxis: {
                title: {
                    text: null
                },
                labels: {
                    formatter: function () {
                        return Math.abs(this.value);
                    }
                }
            },
            legend: {
                align: 'right',
                x: -30,
                verticalAlign: 'bottom',
                y: 0,
                floating: false,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: true
            },
            tooltip: {
                formatter: function () {
                    var s = '<b>' + this.x + '</b>',
                            sum = 0;
                    $.each(this.points, function (i, point) {
                        s += '<br/> <span style="color: '+point.series.color+'">\u25CF</span> '+ point.series.name + ': ' +
                               '<b>'+Highcharts.numberFormat(Math.abs(point.y),0)+'</b>  ('+ Highcharts.numberFormat(point.percentage,1) +'%)';
                        sum += point.y;
                    });

                    s += '<br/>Total:  <b>' + Highcharts.numberFormat(Math.abs(sum),0)+'</b>';

                    return s;
                },
                shared: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: <?php echo json_encode($outcomes['gender_all']['female']); ?>
        });
    });
</script>