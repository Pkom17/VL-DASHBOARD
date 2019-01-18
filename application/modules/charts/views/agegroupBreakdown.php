<div class="">
    <div class="">
        <div class="" id="ageGroupsbreakdownChildren_pie"></div>
        <div class="">
            <ul>
                <?php echo $outcomes['ul']['children']; ?>
            </ul>
        </div>
    </div>
    <div class="">
        <div  class="" id="ageGroupsbreakdownAdults_pie"></div>
        <div class="">
            <ul>
                <?php echo $outcomes['ul']['adults']; ?>
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#ageGroupsbreakdownChildren_pie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '<?= lang('label.children') ?> (<?= lang('total_tested_patient') ?> :' +<?php echo $outcomes['ctotal']; ?> + ')'
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
            }, colors: [
                '#EC644B',
                '#1BBC9B',
                '#5C97BF'
            ],
            series: [<?php echo json_encode($outcomes['children']); ?>]
        });
    });

    $(function () {
        $('#ageGroupsbreakdownAdults_pie').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '<?= lang('label.adults') ?> (<?= lang('total_tested_patient') ?> :' +<?php echo $outcomes['atotal']; ?> + ')'
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
            }, colors: [
                '#EC644B',
                '#1BBC9B',
                '#5C97BF'
            ],
            series: [<?php echo json_encode($outcomes['adults']); ?>]
        });
    });
</script>