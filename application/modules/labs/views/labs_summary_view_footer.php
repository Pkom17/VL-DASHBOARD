<script type="text/javascript">
	$().ready(function() {

		localStorage.setItem("my_lab", 0);

		$("#lab_perfomance_stats").load("<?php echo base_url();?>charts/labs/lab_performance_stats");
		$("#rejected").load("<?php echo base_url();?>charts/labs/rejection_trends");
		$("#test_trends").load("<?php echo base_url('charts/labs/testing_trends');?>");
		$("#samples").load("<?php echo base_url();?>charts/labs/sample_types");
		$("#ttime").load("<?php echo base_url();?>charts/labs/turn_around_time");
		$("#results").load("<?php echo base_url();?>charts/labs/results_outcome");

		$("#lab_rejections").load("<?php echo base_url();?>charts/labs/rejections/0");

		$(".display_date").load("<?php echo base_url('charts/labs/display_date'); ?>");

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
			    $("#lab_perfomance_stats").html("<div><?=lang('label.loading')?></div>"); 
		 		$("#rejected").html("<div><?=lang('label.loading')?></div>"); 
				$("#test_trends").html("<div><?=lang('label.loading')?></div>");
				$("#samples").html("<div><?=lang('label.loading')?></div>");
				$("#ttime").html("<div><?=lang('label.loading')?></div>");
				$("#results").html("<div><?=lang('label.loading')?></div>");

				$("#rejected").load("<?php echo base_url();?>charts/labs/rejection_trends/"+from[1]);
				$("#test_trends").load("<?php echo base_url('charts/labs/testing_trends');?>/"+from[1]);
				$("#ttime").load("<?php echo base_url();?>charts/labs/turn_around_time/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#lab_perfomance_stats").load("<?php echo base_url();?>charts/labs/lab_performance_stats/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#samples").load("<?php echo base_url();?>charts/labs/sample_types/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#results").load("<?php echo base_url();?>charts/labs/results_outcome/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);

				var em = localStorage.getItem("my_lab");

				$("#lab_rejections").load("<?php echo base_url();?>charts/labs/rejections/"+em+"/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);

			}
		    
		});

		$("select").change(function(){
			em = $(this).val();
			em = parseInt(em);
			localStorage.setItem("my_lab", em);
			
			if(em == 0){

			
				$("#first").show();
	        	$("#second").hide();
	        	$("#breadcrum").hide();

	        	$("#lab_perfomance_stats").load("<?php echo base_url();?>charts/labs/lab_performance_stats");
				$("#rejected").load("<?php echo base_url();?>charts/labs/rejection_trends");
				$("#test_trends").load("<?php echo base_url('charts/labs/testing_trends');?>");
				$("#samples").load("<?php echo base_url();?>charts/labs/sample_types");
				$("#ttime").load("<?php echo base_url();?>charts/labs/turn_around_time");
				$("#results").load("<?php echo base_url();?>charts/labs/results_outcome");

				$(".display_date").load("<?php echo base_url('charts/labs/display_date'); ?>");

			}
			else{
				
				$("#first").hide();
	        	$("#second").show();
	        	$("#breadcrum").show();
	        	var t = $("#my_list option:selected").text();
	        	$("#breadcrum").html(t);
	        	$("#lab_summary").load("<?php echo base_url();?>charts/labs/summary/"+em);
	        	$("#graphs").load("<?php echo base_url();?>charts/labs/lab_trends/"+em);
				
			}
			$("#lab_rejections").html("<div><?=lang('label.loading')?></div>");
			$("#lab_rejections").load("<?php echo base_url();?>charts/labs/rejections/"+em);
			
	    });

	});

	function date_filter(criteria, id)
 	{
 		
	        	
 		if (criteria === "monthly") {
 			year = null;
 			month = id;
 		}else {
 			year = id;
 			month = null;
 		}
 		// console.log(year+"<___>"+month);
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


		$("#lab_summary").html("<div><?=lang('label.loading')?></div>");
		$("#lab_rejections").html("<div><?=lang('label.loading')?></div>");
 		
 		
 		$("#lab_perfomance_stats").html("<div><?=lang('label.loading')?></div>"); 
 		$("#rejected").html("<div><?=lang('label.loading')?></div>"); 
		$("#test_trends").html("<div><?=lang('label.loading')?></div>");
		$("#samples").html("<div><?=lang('label.loading')?></div>");
		$("#ttime").html("<div><?=lang('label.loading')?></div>");
		$("#results").html("<div><?=lang('label.loading')?></div>");

		var em = localStorage.getItem("my_lab");
		$("#lab_summary").load("<?php echo base_url();?>charts/labs/summary/"+em+"/"+year);
		$("#lab_rejections").load("<?php echo base_url();?>charts/labs/rejections/"+em+"/"+year+"/"+month);

		$("#rejected").load("<?php echo base_url();?>charts/labs/rejection_trends/"+year);
		$("#test_trends").load("<?php echo base_url('charts/labs/testing_trends');?>/"+year);
		$("#ttime").load("<?php echo base_url();?>charts/labs/turn_around_time/"+year+"/"+month);
		$("#lab_perfomance_stats").load("<?php echo base_url();?>charts/labs/lab_performance_stats/"+year+"/"+month);
		$("#samples").load("<?php echo base_url();?>charts/labs/sample_types/"+year+"/"+month);
		$("#results").load("<?php echo base_url();?>charts/labs/results_outcome/"+year+"/"+month);

		
	}
</script>