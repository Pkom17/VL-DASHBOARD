<script type="text/javascript">
	$().ready(function () {
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
		$("#second").hide();

		$("#regimen_outcomes").load("<?php echo base_url('charts/samples/samples_outcomes');?>");


		$("select").change(function(){
			em = $(this).val();
			// console.log(em);
			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_sample_data", { sample: em } );
	     
	   //      // Put the results in a div
	        posting.done(function( data ) {
	        	// console.log(data);
	   //      	$.get("<?php echo base_url();?>template/breadcrum/"+data, function(data){
	   //      		$("#breadcrum").html(data);
	   //      	});
	        	$.get("<?php echo base_url();?>template/dates", function(data){
	        		obj = $.parseJSON(data);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
	        	});
	        	if (data=="") {
	        		$("#second").hide();
	        		$("#first").show();

	        		$("#regimen_outcomes").load("<?php echo base_url('charts/samples/samples_outcomes');?>");
	        	} else {
	        		data = JSON.parse(data);
	        		$("#first").hide();
	        		$("#second").show();

	        		$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#age").html("<center><div class='loader'></div></center>");
					$("#samples").html("<center><div class='loader'></div></center>");
					$("#county").html("<center><div class='loader'></div></center>");
					
					$("#vlOutcomes").load("<?php echo base_url('charts/samples/samples_vl_outcome'); ?>");
					$("#gender").load("<?php echo base_url('charts/samples/samples_gender'); ?>/"+null+"/"+null+"/"+data);
					$("#age").load("<?php echo base_url('charts/samples/samples_age'); ?>/"+null+"/"+null+"/"+data); 
					$("#samples").load("<?php echo base_url('charts/samples/suppression'); ?>/"+null+"/"+data);

					$("#county").load("<?php echo base_url('charts/samples/samples_county_outcomes'); ?>/"+null+"/"+null+"/"+data);
	        	}      	
	        });
	    });

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
			    $.get("<?php echo base_url('regimen/check_regimen_select');?>", function( data ){
					data = JSON.parse(data);
					// console.log(data);
					if (data==0) {
						$("#second").hide();
		        		$("#first").show();

		        		$("#regimen_outcomes").load("<?php echo base_url('charts/samples/samples_outcomes');?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
					} else {
						$("#first").hide();
		        		$("#second").show();

		        		$("#vlOutcomes").html("<center><div class='loader'></div></center>");
						$("#gender").html("<center><div class='loader'></div></center>");
						$("#age").html("<center><div class='loader'></div></center>");
						$("#samples").html("<center><div class='loader'></div></center>");
						$("#county").html("<center><div class='loader'></div></center>");
						
						$("#vlOutcomes").load("<?php echo base_url('charts/samples/samples_vl_outcome'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
						$("#gender").load("<?php echo base_url('charts/samples/samples_gender'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
						$("#age").load("<?php echo base_url('charts/samples/samples_age'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]); 
						$("#samples").load("<?php echo base_url('charts/samples/suppression'); ?>/"+from[1]+"/"+data);
						$("#county").load("<?php echo base_url('charts/samples/samples_county_outcomes'); ?>/"+from[1]+"/"+from[0]+"/"+data+"/"+to[1]+"/"+to[0]);
					}
				});
			}
		    
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

			$.get("<?php echo base_url('sample/check_sample_select');?>", function( data ){
				data = $.parseJSON(data);
				if (data==0) {
					$("#second").hide();
	        		$("#first").show();

	        		$("#regimen_outcomes").load("<?php echo base_url('charts/samples/samples_outcomes');?>/"+year+"/"+month);
				} else {
					$("#first").hide();
	        		$("#second").show();

	        		$("#vlOutcomes").html("<center><div class='loader'></div></center>");
					$("#gender").html("<center><div class='loader'></div></center>");
					$("#age").html("<center><div class='loader'></div></center>");
					$("#samples").html("<center><div class='loader'></div></center>");
					$("#county").html("<center><div class='loader'></div></center>");
					
					$("#vlOutcomes").load("<?php echo base_url('charts/samples/samples_vl_outcome'); ?>/"+year+"/"+month+"/"+data);
					$("#gender").load("<?php echo base_url('charts/samples/samples_gender'); ?>/"+year+"/"+month+"/"+data);
					$("#age").load("<?php echo base_url('charts/samples/samples_age'); ?>/"+year+"/"+month+"/"+data); 
					$("#samples").load("<?php echo base_url('charts/samples/suppression'); ?>/"+year+"/"+data);
					$("#county").load("<?php echo base_url('charts/samples/samples_county_outcomes'); ?>/"+year+"/"+month+"/"+data);
				}
			});
			
		}); 
	}
</script>