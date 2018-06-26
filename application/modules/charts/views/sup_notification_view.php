<div class="alert" style="background-color: #DADFE1;color:black;">
<center>
	<?php //echo $suppressions['county'];?> <?=  lang('label.non_suppression')?> <?php echo $suppressions['year'].$suppressions['month'];?>: 
	<?php echo number_format($suppressions['sustxfail']);?>&nbsp;(<strong><?php echo $suppressions['rate'];?>%</strong>)
</center>
</div>
