
<?php
echo "<div id=" . $div_name . " class='col-md-6' style='margin-bottom:10px;'>

</div><br/>";
?>


<script type="text/javascript">


    $("#<?php echo $div_name; ?>").highcharts({
        title: {
            text: "<?php echo $title; ?>",
            x: -20, //center
            style: {
                fontWeight: 'bold',
                fontSize: '1.5em',
                textTransform: 'uppercase'
            }
        },
        xAxis: {
            categories: ['<?= lang('label.month1'); ?>', '<?= lang('label.month2'); ?>', '<?= lang('label.month3'); ?>']
        },
        yAxis: {
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
            valueSuffix: "<?php echo $suffix; ?>",

        },
        legend: {
            layout: 'horizontal',
            align: 'right',
            verticalAlign: 'bottom',
            floating: false,
            borderWidth: 0,
            padding: 5
        },
        series: <?php echo json_encode($trends); ?>

    });



</script>