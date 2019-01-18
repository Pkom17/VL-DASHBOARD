
<?php
echo "<div id=" . $div_name . " class='col-md-6' style='margin-bottom:10px;'>

</div>";
?>


<script type="text/javascript">

    $("#<?php echo $div_name; ?>").highcharts({
        title: {
            text: "<?php echo $title; ?>",
            x: -20, //center
            style: {
                fontWeight: 'bold',
                fontSize: '1.5em',
                textTransform:'uppercase'
            }
        },
        zoomType: 'x',
        xAxis: {
            categories: ['<?= lang('cal_jan') ?>', '<?= lang('cal_feb') ?>', '<?= lang('cal_mar') ?>', '<?= lang('cal_apr') ?>',
                '<?= lang('cal_may') ?>', '<?= lang('cal_jun') ?>', '<?= lang('cal_jul') ?>', '<?= lang('cal_aug') ?>',
                '<?= lang('cal_sep') ?>', '<?= lang('cal_oct') ?>', '<?= lang('cal_nov') ?>', '<?= lang('cal_dec') ?>']
        },
        yAxis: {
<?PHP
if ($div_name == 'suppression') {
    echo 'tickInterval: 20,';
    echo 'min: 0,';
    echo 'max: 100,';
}
?>
            title: {
                text: "<?php echo $yAxis; ?>"
            },
            plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
        },
        tooltip: {
            valueSuffix: "<?php echo $suffix; ?>"
        },
        legend: {
            layout: 'horizontal',
            align: 'right',
            verticalAlign: 'bottom',
            floating: false,
            borderWidth: 0,
            padding: 20
        },
        series: <?php echo json_encode($trends); ?>

    });



</script>