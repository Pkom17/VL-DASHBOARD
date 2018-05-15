
<?php
    echo "<div id=" . $div_name . " class='col-md-12'>

</div>";

?>


<script type="text/javascript">

  
    $("#<?php echo $div_name; ?>").highcharts({
        title: {
            text: "<?php echo $title; ?>",
            x: -20 //center
        },
        xAxis: { 
            categories: ['<?=  lang('cal_jan')?>', '<?=  lang('cal_feb')?>', '<?=  lang('cal_mar')?>', '<?=  lang('cal_apr')?>', 
        '<?=  lang('cal_may')?>', '<?=  lang('cal_jun')?>','<?=  lang('cal_jul')?>',<?=  lang('cal_aug')?>',
 '<?=  lang('cal_sep')?>', '<?=  lang('cal_oct')?>', '<?=  lang('cal_nov')?>', '<?=  lang('cal_dec')?>']
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
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            floating: true,
            borderWidth: 0
        },
        series: <?php echo json_encode($trends);?>
            
    });
  

 
</script>