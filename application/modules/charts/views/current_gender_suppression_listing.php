<div class="list-group" style="height: 362px;">
    <?php echo $cont['ul']; ?>
</div>
<button class="btn btn-primary"  onclick="expand_modal( & quot; #<?= @$cont['div']; ?> & quot; );" style="background-color: #2f80d1;color: white; margin-top: 1em;margin-bottom: 1em;"><?= lang('label.view_full_listing') ?></button>

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
                            <th><?= lang('label.table_male_suppress') ?></th>
                            <th><?= lang('label.table_male_non_suppress') ?></th>
                            <th><?= lang('label.table_female_suppress') ?></th>
                            <th><?= lang('label.table_female_non_suppress') ?></th>
                            <th><?= lang('label.table_no_gender_suppress') ?></th>
                            <th><?= lang('label.table_no_gender_non_suppress') ?></th>
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