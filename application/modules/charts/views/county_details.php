<table id="example" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="background:#CCC;">
	<thead>
		<tr class="colhead">
			<th><?=  lang('label.partner')?></th>
			<th><?=  lang('label.facility')?></th>
			<th><?=  lang('label.tests')?> Tests</th>
			<th><?=  lang('label.suppressed')?> </th>
			<th><?=  lang('label.non_sp_suppressed')?></th>
			<th><?=  lang('label.rejected')?> </th>
			<th><?=  lang('label.adults')?></th>
			<th><?=  lang('label.children')?> </th>
		</tr>
	</thead>
	<tbody>
		<?php echo $outcomes;?>
	</tbody>
</table>
<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
  	$('#example').DataTable({
        "order": [[ 2, "desc" ]]

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
    //   theme: "paper",
    //   striped: true,
    //   sortable: true,
    //   condensed: true
    // });
  });
</script>