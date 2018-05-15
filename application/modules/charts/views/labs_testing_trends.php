<div id="testing_trends">
    
</div>

<script type="text/javascript">
    $(function () {
        $('#testing_trends').highcharts({
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: ['<?=  lang('cal_jan')?>', '<?=  lang('cal_feb')?>', '<?=  lang('cal_mar')?>', '<?=  lang('cal_apr')?>', 
        '<?=  lang('cal_may')?>', '<?=  lang('cal_jun')?>','<?=  lang('cal_jul')?>',<?=  lang('cal_aug')?>',
 '<?=  lang('cal_sep')?>', '<?=  lang('cal_oct')?>', '<?=  lang('cal_nov')?>', '<?=  lang('cal_dec')?>']
            },
            yAxis: {
                title: {
                    text: '<?=  lang('label.tests')?>'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ''
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'center',
                borderWidth: 0
            },
            series: <?php echo json_encode($trends['test_trends']);?>
        });
    });
</script>