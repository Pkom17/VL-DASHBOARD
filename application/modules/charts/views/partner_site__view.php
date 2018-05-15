<table id="example" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="background:#CCC;">
    <thead>
        <tr class="colhead">
            <th rowspan="2"><?= lang('label.no'); ?></th>
            <th rowspan="2"><?= lang('label.code.MFL'); ?></th>
            <th rowspan="2"><?= lang('label.table_name'); ?></th>
            <th rowspan="2"><?= lang('label.table_county'); ?></th>
            <th rowspan="2"><?= lang('label.table_received_samp'); ?></th>
            <th rowspan="2"><?= lang('label.table_rejected'); ?></th>
            <th rowspan="2"><?= lang('label.table_all_test_done'); ?></th>
            <th rowspan="2"><?= lang('label.table_redraw'); ?></th>
            <th colspan="2"><?= lang('label.table_routine_vl_tests'); ?></th>
            <th colspan="2"><?= lang('label.table_baseline_vl_tests'); ?></th>
            <th colspan="2"><?= lang('label.table_confirmatory_repeat_tests'); ?></th>
            <th colspan="2"><?= lang('label.table_total_tests_valid'); ?></th>
        </tr>
        <tr>
            <th><?= lang('label.tests'); ?></th>
            <th>&gt; 1000</th>
            <th><?= lang('label.tests'); ?></th>
            <th>&gt; 1000</th>
            <th><?= lang('label.tests'); ?></th>
            <th>&gt; 1000</th>
            <th><?= lang('label.tests'); ?></th>
            <th>&gt; 1000</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $outcomes; ?>
    </tbody>
</table>
<div class="row" id="excels"  style="display: none;">
    <div class="col-md-6">
            <!-- <center><button class="btn btn-primary" style="background-color: #009688;color: white;">List of all supported sites</button></center> -->
    </div>
    <div class="col-md-6">
        <center><a href="<?php echo $link; ?>"><button id="download_link" class="btn btn-primary" style="background-color: #009688;color: white;"><?= lang('label.export_excel') ?></button></a></center>
    </div>
</div>
<script type="text/javascript" charset="utf-8">
    $(document).ready(function () {

        $('#example').DataTable({
            dom: '<"btn btn-primary"B>lTfgtip',
            responsive: true,
            buttons: [
                {
                    text: '<?= lang('label.export_csv') ?>',
                    extend: 'csvHtml5',
                    title: '<?= lang('label.download') ?>'
                },
                {
                    text: '<?= lang('label.export_excel') ?>',
                    extend: 'excelHtml5',
                    title: '<?= lang('label.download') ?>'
                }
            ]
        });

        // $("table").tablecloth({
        //   theme: "paper",
        //   striped: true,
        //   sortable: true,
        //   condensed: true
        // });
    });
</script>