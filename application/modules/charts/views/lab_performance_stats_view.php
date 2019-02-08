
<table id="example" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="max-width: 100%;">
    <thead>
        <tr class="colhead">
        <tr class="colhead">
            <th rowspan="2"><?= lang('label.table_no') ?></th>
            <th rowspan="2"><?= lang('label.table_name') ?></th>
            <th colspan="5" class="text-center"><?= lang('test_done') ?></th>
            <th colspan="4" class="text-center"><?= lang('tested_patient') ?></th>
        </tr>
        <tr class="colhead">
            <th><?= lang('label.total') ?></th>
            <th><?= lang('result_invalids') ?></th>
            <th><?= lang('result_undetectable') ?></th>
            <th><?= lang('result_lt1000') ?></th>
            <th><?= lang('result_gt1000') ?></th>
            <th><?= lang('label.total') ?></th>
            <th><?= lang('patient_undetectable') ?></th>
            <th><?= lang('patient_lt1000') ?></th>
            <th><?= lang('patient_nonsuppressed') ?></th>
        </tr>

    </thead>
    <tbody>
        <?php echo $stats; ?>
    </tbody>
</table>
<div class="row" style="display: none;">
    <div class="col-md-12">
        <center><a href="<?php echo $link; ?>"><button id="download_link" class="btn btn-primary" style="background-color: #2f80d1;color: white;"><?= lang('label.export_excel') ?></button></a></center>
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
            ],
            language: {
                decimal: "<?= lang('dt.decimal') ?>",
                processing: "<?= lang('dt.processing') ?>",
                search: "<?= lang('dt.search') ?>",
                lengthMenu: "<?= lang('dt.lengthMenu') ?>",
                info: "<?= lang('dt.info') ?>",
                infoEmpty: "<?= lang('dt.infoEmpty') ?>",
                infoFiltered: "<?= lang('dt.infoFiltered') ?>",
                infoPostFix: "<?= lang('dt.infoPostFix') ?>",
                thousands: "<?= lang('dt.thousands') ?>",
                loadingRecords: "<?= lang('dt.loadingRecords') ?>",
                zeroRecords: "<?= lang('dt.zeroRecords') ?>",
                emptyTable: "<?= lang('dt.emptyTable') ?>",
                paginate: {
                    first: "<?= lang('dt.first') ?>",
                    previous: "<?= lang('dt.previous') ?>",
                    next: "<?= lang('dt.next') ?>",
                    last: "<?= lang('dt.last') ?>"
                },
                aria: {
                    sortAscending: "<?= lang('dt.sortAscending') ?>",
                    sortDescending: "<?= lang('dt.sortDescending') ?>"
                }
            }
        });

        // $("table").tablecloth({
        //   theme: "paper",
        //   striped: true,
        //   sortable: true,
        //   condensed: true
        // });

    });
</script>