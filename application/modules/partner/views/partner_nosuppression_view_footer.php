<script type="text/javascript">
	$().ready(function() {
		part = 1;
		// console.log(part);
		//Selecting the appropriate partner and filling the breadcrum with their data
		// $.get("<?php echo base_url();?>template/breadcrum/"+part+"/"+1, function(data){
	 	//    	$("#breadcrum").html(data);
	 //    });
	    //Fetching data for the above selected partner
		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+null+"/"+null+"/"+null+"/"+part);
 		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+null+"/"+null+"/"+null+"/"+part);
		$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+null+"/"+null+"/"+null+"/"+part);
		$("#regimen").load("<?php echo base_url('charts/nonsuppression/regimen');?>/"+null+"/"+null+"/"+null+"/"+part);
		$("#sampleType").load("<?php echo base_url('charts/nonsuppression/sample_type');?>/"+null+"/"+null+"/"+null+"/"+part);
		$("#sites_listing").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+null+"/"+null+"/"+part);

		// $(".display_date").load("<?php echo base_url('charts/nonsuppression/display_date'); ?>");

		// fetching the data for a specific partner
		$("select").change(function(){
			part = $(this).val();
			console.log(part);
			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_partner_data", { partner: part } );
	        
	        // Put the results in a div
	        posting.done(function( data ) {
	        	/*$.get("<?php echo base_url();?>template/breadcrum/"+data+"/"+1, function(data){
	        		$("#breadcrum").html(data);
	        	});*/
	    //     	$.get("<?php echo base_url();?>template/dates", function(data){
	    //     		obj = $.parseJSON(data);
			
					// if(obj['month'] == "null" || obj['month'] == null){
					// 	obj['month'] = "";
					// }
					// $(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					
	    //     	});

	        	$("#genderGrp").html("<center><div class='loader'></div></center>"); 
	        	$("#ageGrp").html("<center><div class='loader'></div></center>"); 
				$("#justification").html("<center><div class='loader'></div></center>");
				$("#regimen").html("<center><div class='loader'></div></center>");
				$("#sampleType").html("<center><div class='loader'></div></center>");
				$("#sites_listing").html("<center><div class='loader'></div></center>");
				
				data = "<?php echo json_decode("+data+")?>";

				$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+null+"/"+null+"/"+null+"/"+data);
		 		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+null+"/"+null+"/"+null+"/"+data);
				$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+null+"/"+null+"/"+null+"/"+data);
				$("#regimen").load("<?php echo base_url('charts/nonsuppression/regimen');?>/"+null+"/"+null+"/"+null+"/"+data);
				$("#sampleType").load("<?php echo base_url('charts/nonsuppression/sample_type');?>/"+null+"/"+null+"/"+null+"/"+data);
				$("#sites_listing").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+null+"/"+null+"/"+data);
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
				    $("#genderGrp").html("<div><?=lang('label.loading')?></div>"); 
			        $("#ageGrp").html("<div><?=lang('label.loading')?></div>"); 
					$("#justification").html("<div><?=lang('label.loading')?></div>");
					$("#regimen").html("<div><?=lang('label.loading')?></div>");
					$("#sampleType").html("<div><?=lang('label.loading')?></div>");
					$("#sites_listing").html("<center><div class='loader'><?=lang('label.loading')?></div></center>");

					$.get("<?php echo base_url('partner/check_partner_select')?>", function(data) {
			 			data = "<?php echo json_decode("+data+")?>";
			 			partner = data;
			 			// console.log(partner);
				 		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
				 		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#regimen").load("<?php echo base_url('charts/nonsuppression/regimen');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#sampleType").load("<?php echo base_url('charts/nonsuppression/sample_type');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+partner+"/"+to[1]+"/"+to[0]);
						$("#sites_listing").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
					});

				}
			    
			});
		});
	
	function date_filter(criteria, id)
 	{
 		$("#genderGrp").html("<div><?=lang('label.loading')?></div>"); 
        $("#ageGrp").html("<div><?=lang('label.loading')?></div>"); 
		$("#justification").html("<div><?=lang('label.loading')?></div>");
		$("#regimen").html("<div><?=lang('label.loading')?></div>");
		$("#sampleType").html("<div><?=lang('label.loading')?></div>");
		$("#sites_listing").html("<center><div class='loader'><?=lang('label.loading')?></div></center>");

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
 		$.get("<?php echo base_url('partner/check_partner_select')?>", function(data) {
 			data = "<?php echo json_decode("+data+")?>";
 			partner = data;
 			// console.log(partner);
	 		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+year+"/"+month+"/"+null+"/"+partner);
	 		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+year+"/"+month+"/"+null+"/"+partner);
			$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+year+"/"+month+"/"+null+"/"+partner);
			$("#regimen").load("<?php echo base_url('charts/nonsuppression/regimen');?>/"+year+"/"+month+"/"+null+"/"+partner);
			$("#sampleType").load("<?php echo base_url('charts/nonsuppression/sample_type');?>/"+year+"/"+month+"/"+null+"/"+partner);
			$("#sites_listing").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+year+"/"+month+"/"+null);
		});
	}
</script>