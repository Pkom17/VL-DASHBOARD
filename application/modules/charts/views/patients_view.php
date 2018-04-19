<p style="height: 200px;">
	<?= lang('label.total_patients_as_date'); ?>  <?php echo number_format($patients); ?>  <br />
	<?= lang('label.total_patients_with_vl_done'); ?> <?php echo number_format($patients_vl); ?>  <br />
        <?= lang('label.total_VL_done'); ?> <?php echo number_format($tests); ?>  <br />
</p>

<div style="height: 340px;">
<table id="example" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="max-width: 100%;">
	<thead>
		<tr class="colhead">
			<th><?= lang('label.table_total_VL_tests'); ?></th>
			<th><?= lang('label.table_1_VL'); ?></th>
			<th><?= lang('label.table_2_VL'); ?></th>
			<th><?= lang('label.table_3_VL'); ?></th>
			<th><?= lang('label.table_m3_VL'); ?></th>
		</tr>
		
	</thead>
	<tbody>
		<?php echo $stats;?>
	</tbody>
</table>
</div>