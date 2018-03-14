<div class="list-group" style="height: 362px;">
    <?php echo $cont['ul']; ?>
</div>
<button class="btn btn-primary"  onclick="expand_modal( & quot; #<?= @$cont['div']; ?> & quot; );" style="background-color: #1BA39C;color: white; margin-top: 1em;margin-bottom: 1em;"><?= lang('label.view_full_listing') ?></button>

<div class="modal fade" tabindex="-1" role="dialog" id="<?= @$cont['div']; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?= lang('label.modal_close') ?>"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= @$cont['title']; ?></h4>
            </div>
            <div class="modal-body">
                <table id="<?= @$cont['table_div']; ?>" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="max-width: 100%;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?= lang('label.table.name') ?></th>
                            <th><?= lang('label.table_no_age_no_supp') ?></th>
                            <th><?= lang('label.table_non_suppressed_t1') ?></th>
                            <th><?= lang('label.table_non_suppressed_t2') ?></th>
                            <th><?= lang('label.table_non_suppressed_t3') ?></th>
                            <th><?= lang('label.table_non_suppressed_t4') ?></th>
                            <th><?= lang('label.table_non_suppressed_t5') ?></th>
                            <th> <?= lang('label.table_non_suppressed_t6') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= @$cont['table']; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $("#<?= @$cont['table_div']; ?>").DataTable({
            // dom: 'Bfrtip',
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
        // 	theme: "paper",
        // 	striped: true,
        // 	sortable: true,
        // 	condensed: true
        // });
    });

    function call_expander() {
        var modal_name = "#<?= @$cont['div']; ?>";
        expand_modal(modal_name);
    }

</script>