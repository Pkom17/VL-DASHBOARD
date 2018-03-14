<script type="text/javascript">
	$().ready(function(){
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['year']+" )");
    	});

		$("#samples").html("<center><div class='loader'></div></center>");
		$("#age").html("<center><div class='loader'></div></center>");
		$("#gender").html("<center><div class='loader'></div></center>");

		$("#county").html("<center><div class='loader'></div></center>");
		$("#subcounty").html("<center><div class='loader'></div></center>");
		$("#partner").html("<center><div class='loader'></div></center>");
		$("#facility").html("<center><div class='loader'></div></center>");

		$("#notification").load("<?php echo base_url('charts/baseline/notification');?>/0/0");
		$("#samples").load("<?php echo base_url('charts/baseline/samples');?>/0/0");
		$("#age").load("<?php echo base_url('charts/baseline/age');?>/0/0");
		$("#gender").load("<?php echo base_url('charts/baseline/gender');?>/0/0");

		$("#county").load("<?php echo base_url('charts/baseline/baseline_list');?>/1");		
		$("#subcounty").load("<?php echo base_url('charts/baseline/baseline_list');?>/2");		
		$("#partner").load("<?php echo base_url('charts/baseline/baseline_list');?>/3");		
		$("#facility").load("<?php echo base_url('charts/baseline/baseline_list');?>/4");	
		

		$("button").click(function () {
		    var first, second;
		    first = $(".date-picker[name=startDate]").val();
		    second = $(".date-picker[name=endDate]").val();
		    
		    var new_title = set_multiple_date(first, second);

		    $(".display_date").html(new_title);
		    
		    from = format_date(first);
		    /* from is an array
		     	[0] => month
		     	[1] => year*/
		    to 	= format_date(second);
		    var error_check = check_error_date_range(from, to);
		    
		    if (!error_check) {

				$("#samples").html("<center><div class='loader'></div></center>");
				$("#age").html("<center><div class='loader'></div></center>");
				$("#gender").html("<center><div class='loader'></div></center>");

				$("#county").html("<center><div class='loader'></div></center>");
				$("#subcounty").html("<center><div class='loader'></div></center>");
				$("#partner").html("<center><div class='loader'></div></center>");
				$("#facility").html("<center><div class='loader'></div></center>");

				$("#notification").load("<?php echo base_url('charts/baseline/notification');?>/0/0/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#samples").load("<?php echo base_url('charts/baseline/samples');?>/0/0/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#age").load("<?php echo base_url('charts/baseline/age');?>/0/0/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#gender").load("<?php echo base_url('charts/baseline/gender');?>/0/0/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);

				$("#county").load("<?php echo base_url('charts/baseline/baseline_list');?>/1/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);		
				$("#subcounty").load("<?php echo base_url('charts/baseline/baseline_list');?>/2/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);		
				$("#partner").load("<?php echo base_url('charts/baseline/baseline_list');?>/3/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);		
				$("#facility").load("<?php echo base_url('charts/baseline/baseline_list');?>/4/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);	


			    
			}
		    
		});
	});

	function date_filter(criteria, id)
 	{
 		// $("#partner").html("<div>Loading...</div>");

 		if (criteria === "monthly") {
 			year = null;
 			month = id;
 		}else {
 			year = id;
 			month = null;
 		}

 		var posting = $.post( '<?php echo base_url();?>template/filter_date_data', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {

			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
			
		});


		$("#samples").html("<center><div class='loader'></div></center>");
		$("#age").html("<center><div class='loader'></div></center>");
		$("#gender").html("<center><div class='loader'></div></center>");

		$("#county").html("<center><div class='loader'></div></center>");
		$("#subcounty").html("<center><div class='loader'></div></center>");
		$("#partner").html("<center><div class='loader'></div></center>");
		$("#facility").html("<center><div class='loader'></div></center>");

		$("#notification").load("<?php echo base_url('charts/baseline/notification');?>/0/0/"+year+"/"+month);
		$("#samples").load("<?php echo base_url('charts/baseline/samples');?>/0/0/"+year+"/"+month);
		$("#age").load("<?php echo base_url('charts/baseline/age');?>/0/0/"+year+"/"+month);
		$("#gender").load("<?php echo base_url('charts/baseline/gender');?>/0/0/"+year+"/"+month);

		$("#county").load("<?php echo base_url('charts/baseline/baseline_list');?>/1/"+year+"/"+month);		
		$("#subcounty").load("<?php echo base_url('charts/baseline/baseline_list');?>/2/"+year+"/"+month);	
		$("#partner").load("<?php echo base_url('charts/baseline/baseline_list');?>/3/"+year+"/"+month);	
		$("#facility").load("<?php echo base_url('charts/baseline/baseline_list');?>/4/"+year+"/"+month);
	 	
 	}

</script>