<??>
<script type="text/javascript">
	$().ready(function(){
		$("#notification").load("<?php echo base_url('charts/nonsuppression/notification');?>");
		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>");
		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>");
		$("#countys").load("<?php echo base_url('charts/nonsuppression/county_listings');?>");
		$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>");
		$("#gender").load("<?php echo base_url('charts/nonsuppression/gender');?>");
		$("#age").load("<?php echo base_url('charts/nonsuppression/age');?>");
		$("#countiesGraph").load("<?php echo base_url('charts/nonsuppression/county');?>");
		$("#partners").load("<?php echo base_url('charts/nonsuppression/partner_listing');?>");
		$("#subcounty").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>");
		$("#facilities").load("<?php echo base_url('charts/nonsuppression/site_listings');?>");

		$(".display_date").load("<?php echo base_url('charts/nonsuppression/display_date'); ?>");

		$("select").change(function(){
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: em } );
	     
	        // Put the results in a div
	        posting.done(function( data ) {
	        	/*$.get("<?php echo base_url();?>template/breadcrum", function(data2){
	        		$("#breadcrum").html(data2);
	        	});*/
	        	$.get("<?php echo base_url();?>template/dates", function(data2){
	        		obj = $.parseJSON(data2);
			
					if(obj['month'] == "null" || obj['month'] == null){
						obj['month'] = "";
					}
					$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
					
	        	});
	        	
	        	$("#notification").html("<div><?=  lang('label.loading')?></div>");
				$("#genderGrp").html("<div><?=  lang('label.loading')?></div>");
				$("#ageGrp").html("<div><?=  lang('label.loading')?></div>");
				$("#countys").html("<div><?=  lang('label.loading')?></div>");
				$("#justification").html("<div><?=  lang('label.loading')?></div>");
				$("#gender").html("<div><?=  lang('label.loading')?></div>");
				$("#age").html("<div><?=  lang('label.loading')?></div>");
				$("#countiesGraph").html("<div><?=  lang('label.loading')?></div>");
				$("#partners").html("<div><?=  lang('label.loading')?></div>");
				$("#subcounty").html("<div><?=  lang('label.loading')?></div>");
				$("#facilities").html("<div><?=  lang('label.loading')?></div>");

				if(data!=""){
	        		data = JSON.parse(data);
	        	}

				$("#notification").load("<?php echo base_url('charts/nonsuppression/notification');?>/"+null+"/"+null+"/"+data);
				$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+null+"/"+null+"/"+data);
				$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+null+"/"+null+"/"+data);
				$("#countys").load("<?php echo base_url('charts/nonsuppression/county_listings');?>/"+null+"/"+null+"/"+data);
				$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+null+"/"+null+"/"+data);
				$("#gender").load("<?php echo base_url('charts/nonsuppression/gender');?>/"+null+"/"+null+"/"+data);
				$("#age").load("<?php echo base_url('charts/nonsuppression/age');?>/"+null+"/"+null+"/"+data);
				$("#countiesGraph").load("<?php echo base_url('charts/nonsuppression/county');?>/"+null+"/"+null+"/"+data);
				$("#partners").load("<?php echo base_url('charts/nonsuppression/partner_listing');?>/"+null+"/"+null+"/"+data);
				$("#subcounty").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>/"+null+"/"+null+"/"+data);
				$("#facilities").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+null+"/"+null+"/"+data);
				
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
		    // alert(error_check);
		    if (!error_check) {
			    $("#notification").html("<div><?=  lang('label.loading')?></div>");
				$("#genderGrp").html("<div><?=  lang('label.loading')?></div>");
				$("#ageGrp").html("<div><?=  lang('label.loading')?></div>");
				$("#countys").html("<div><?=  lang('label.loading')?></div>");
				$("#justification").html("<div><?=  lang('label.loading')?></div>");
				$("#gender").html("<div><?=  lang('label.loading')?></div>");
				$("#age").html("<div><?=  lang('label.loading')?></div>");
				$("#countiesGraph").html("<div><?=  lang('label.loading')?></div>");
				$("#partners").html("<div><?=  lang('label.loading')?></div>");
				$("#subcounty").html("<div><?=  lang('label.loading')?></div>");
				$("#facilities").html("<div><?=  lang('label.loading')?></div>");

				$("#notification").load("<?php echo base_url('charts/nonsuppression/notification');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#countys").load("<?php echo base_url('charts/nonsuppression/county_listings');?>/"+from[1]+"/"+from[0]+"/"+to[1]+"/"+to[0]);
				$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#gender").load("<?php echo base_url('charts/nonsuppression/gender');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#age").load("<?php echo base_url('charts/nonsuppression/age');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#countiesGraph").load("<?php echo base_url('charts/nonsuppression/county');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#partners").load("<?php echo base_url('charts/nonsuppression/partner_listing');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#subcounty").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				$("#facilities").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
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

 		var posting = $.post( '<?php echo base_url();?>suppression/Nosuppression/set_filter_date', { 'year': year, 'month': month } );

 		// Put the results in a div
		posting.done(function( data ) {
			obj = $.parseJSON(data);
			
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			
		});
 		
 		$("#notification").html("<div><?=  lang('label.loading')?></div>");
		$("#genderGrp").html("<div><?=  lang('label.loading')?></div>");
		$("#ageGrp").html("<div><?=  lang('label.loading')?></div>");
		$("#countys").html("<div><?=  lang('label.loading')?></div>");
		$("#justification").html("<div><?=  lang('label.loading')?></div>");
		$("#gender").html("<div><?=  lang('label.loading')?></div>");
		$("#age").html("<div><?=  lang('label.loading')?></div>");
		$("#countiesGraph").html("<div><?=  lang('label.loading')?></div>");
		$("#partners").html("<div><?=  lang('label.loading')?></div>");
		$("#subcounty").html("<div><?=  lang('label.loading')?></div>");
		$("#facilities").html("<div><?=  lang('label.loading')?></div>");

		$("#notification").load("<?php echo base_url('charts/nonsuppression/notification');?>/"+year+"/"+month);
		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+year+"/"+month);
		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+year+"/"+month);
		$("#countys").load("<?php echo base_url('charts/nonsuppression/county_listings');?>/"+year+"/"+month);
		$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+year+"/"+month);
		$("#gender").load("<?php echo base_url('charts/nonsuppression/gender');?>/"+year+"/"+month);
		$("#age").load("<?php echo base_url('charts/nonsuppression/age');?>/"+year+"/"+month);
		$("#countiesGraph").load("<?php echo base_url('charts/nonsuppression/county');?>/"+year+"/"+month);
		$("#partners").load("<?php echo base_url('charts/nonsuppression/partner_listing');?>/"+year+"/"+month);
		$("#subcounty").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>/"+year+"/"+month);
		$("#facilities").load("<?php echo base_url('charts/nonsuppression/site_listings');?>/"+year+"/"+month);

	}

	function county_filter(data)
	{
		/*$.get("<?php echo base_url();?>template/breadcrum", function(data){
			console.log(data);
    		$("#breadcrum").html(data);
    	});*/

		$("#genderGrp").html("<div><?=  lang('label.loading')?></div>"); 
    	$("#ageGrp").html("<div><?=  lang('label.loading')?></div>"); 
		$("#justification").html("<div><?=  lang('label.loading')?></div>");
		$("#regimen").html("<div><?=  lang('label.loading')?></div>");
		$("#sampleType").html("<div><?=  lang('label.loading')?></div>");
		$("#countys").html("<div><?=  lang('label.loading')?></div>");
		$("#partners").html("<div><?=  lang('label.loading')?></div>");
		$("#subcounty").html("<div><?=  lang('label.loading')?></div>");
		$("#facilities").html("<div><?=  lang('label.loading')?></div>");
		
		$("#notification").load("<?php echo base_url('charts/nonsuppression/notification');?>/"+null+"/"+null+"/"+data);
 		$("#genderGrp").load("<?php echo base_url('charts/nonsuppression/gender_group');?>/"+null+"/"+null+"/"+data);
 		$("#ageGrp").load("<?php echo base_url('charts/nonsuppression/age_group');?>/"+null+"/"+null+"/"+data);
		$("#countys").load("<?php echo base_url('charts/nonsuppression/county_listings');?>/"+null+"/"+null+"/"+data);
		$("#justification").load("<?php echo base_url('charts/nonsuppression/justification');?>/"+null+"/"+null+"/"+data);
		$("#regimen").load("<?php echo base_url('charts/nonsuppression/regimen');?>/"+null+"/"+null+"/"+data);
		$("#sampleType").load("<?php echo base_url('charts/nonsuppression/sample_type');?>/"+null+"/"+null+"/"+data);
		$("#partners").load("<?php echo base_url('charts/nonsuppression/partner_listing');?>/"+null+"/"+null+"/"+data);
		$("#subcounty").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>/"+null+"/"+null+"/"+data);
		$("#facilities").load("<?php echo base_url('charts/nonsuppression/subcounty_listings');?>/"+null+"/"+null+"/"+data);
	}
</script>