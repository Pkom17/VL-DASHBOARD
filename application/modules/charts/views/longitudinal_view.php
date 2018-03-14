<div  style="margin-left:2em;">
    <p>
     <?=lang('label.table_patients_on_art_date')?> - <?php echo number_format($trends['total_patients']) ; ?> <br />
     <?=lang('label.total_un_patients_tested')?> - <?php echo number_format($trends['unique_patients']) ; ?> <br />

        <?php  
            for ($i=0; $i < $trends['size']; $i++) { 
                echo lang('label.no_patients_with')." " . $trends['categories'][$i] .  lang('label.tests')." - " . number_format($trends['outcomes'][0]['data'][$i]) . "<br />";
            }

        ?>
     <?php echo lang('label.total_tests_').' - '.number_format($trends['total_tests']) ; ?> <br />
    <?=lang('label.table_coverage')?> - <?php echo number_format($trends['coverage']) ; ?>% <br />
    </p>

    <div id="<?php echo $div_name; ?>">

    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#<?php echo $div_name; ?>').highcharts({
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo $trends['title'];?>'
            },
            xAxis: {
                categories: <?php echo json_encode($trends['categories']);?>
            },
            yAxis: {
                min: 0,
                title: {
                    text: '<?=lang('label.tests');?>'
                },
                stackLabels: {
                    rotation: 0,
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    },
                    y:-10
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                x: -50,
                verticalAlign: 'top',
                y: 30,
                floating: true,
                backgroundColor: '#FFFFFF'
            },
            series: <?php echo json_encode($trends['outcomes']);?>
        });
    });
    
</script>