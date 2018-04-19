<div class="list-group" style="height: 362px;">
	<?php echo $outcomes['ul'];?>
</div>
<button class="btn btn-primary"  onclick="expandPartListing();" style="background-color: #1BA39C;color: white; margin-top: 1em;margin-bottom: 1em;"><?=  lang('label.view_full_listing')?></button>

<div class="modal fade" tabindex="-1" role="dialog" id="expPartList">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?=  lang('label.modal_close')?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?= @$outcomes['title'];?></h4>
      </div>
      <div class="modal-body">
        <table id="partnerList" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="max-width: 100%;">
        	<thead>
        		<tr>
        			<th>#</th>
                                <th><?=  lang('label.name')?></th>
        		</tr>
        	</thead>
        	<tbody>
        		<?= @$outcomes['table']; ?>
        	</tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#partnerList').DataTable({
      dom: '<"btn btn-primary"B>lTfgtip',
      responsive: true,
        buttons : [
            {
              text:  '<?=  lang('label.export_csv')?>',
              extend: 'csvHtml5',
              title: '<?=  lang('label.download')?>'
            },
            {
              text:  '<?=  lang('label.export_excel')?>',
              extend: 'excelHtml5',
              title: '<?=  lang('label.download')?>'
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
	function expandPartListing()
	{
		$('#expPartList').modal('show');
	}
</script>