<script type="text/javascript">
	$(document).ready(function(){
		$.get("<?php echo base_url();?>template/dates", function(data){
    		obj = $.parseJSON(data);
	
			if(obj['month'] == "null" || obj['month'] == null){
				obj['month'] = "";
			}
			$(".display_date").html("( "+obj['year']+" "+obj['month']+" )");
			$(".display_range").html("( "+obj['prev_year']+" - "+obj['year']+" )");
    	});
		$("#partner_counties").hide();
		$("#partners_all").show();
		$("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>");
		
		$("select").change(function(){
			$("#partnerCounties").html("<center><div class='loader'></div></center>");
			em = $(this).val();

			// Send the data using post
	        var posting = $.post( "<?php echo base_url();?>template/filter_partner_data", { partner: em } );
	        
			posting.done(function( data ) {
	        	if (data=="") {data = 0;}
	        	/*$.get("<?php echo base_url();?>template/breadcrum/"+data+"/"+1, function(data){
	        		
	        		$("#breadcrum").html(data);
	        	});*/
	        });
			if (em == "NA") {
				$("#partner_counties").hide();
				$("#partners_all").show();
				$("#partnerOutcomes").html("<center><div class='loader'></div></center>");
				$("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>");
			} else {
				$("#partners_all").hide();
				$("#partner_counties").show();
				
				$("#partnerCounties").html("<center><div class='loader'></div></center>");
				$("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table');?>/"+null+"/"+null+"/"+em);

				$("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
				$("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes');?>/"+null+"/"+null+"/"+em);
			}
		});

		// $("#partner_sites_excels").click(function(){
		// 	$(location).("<?php echo base_url('partner/excel_test');?>");
		// });
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
			    $.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
				var partner = data;
				partner = $.parseJSON(partner);
				
				if (partner==0) {
					$("#partner_counties").hide();
					$("#partners_all").show();
					$("#partnerOutcomes").html("<center><div class='loader'></div></center>");
					$("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>/"+from[1]+"/"+from[0]+"/"+null+"/"+to[1]+"/"+to[0]);
				} else {
					$("#partners_all").hide();
					$("#partner_counties").show();

					$("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
					$("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes');?>/"+from[1]+"/"+from[0]+"/"+partner+"/"+to[1]+"/"+to[0]);

					$("#partnerCounties").html("<center><div class='loader'></div></center>");
					$("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table');?>/"+from[1]+"/"+from[0]+"/"+partner+"/"+to[1]+"/"+to[0]);
				}
			});
			}
		    
		});
	function date_filter(criteria, id)
	{
		// console.log(criteria+":"+id);
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
			
			$.get("<?php echo base_url();?>partner/check_partner_select", function (data) {
				var partner = data;
				partner = $.parseJSON(partner);
				
				if (partner==0) {
					$("#partner_counties").hide();
					$("#partners_all").show();
					$("#partnerOutcomes").html("<center><div class='loader'></div></center>");
					$("#partnerOutcomes").load("<?php echo base_url('charts/sites/site_outcomes');?>/"+year+"/"+month);
				} else {
					$("#partners_all").hide();
					$("#partner_counties").show();

					$("#partnerCountyOutcomes").html("<center><div class='loader'></div></center>");
					$("#partnerCountyOutcomes").load("<?php echo base_url('charts/partner_summaries/partner_counties_outcomes');?>/"+year+"/"+month+"/"+partner);

					$("#partnerCounties").html("<center><div class='loader'></div></center>");
					$("#partnerCounties").load("<?php echo base_url('charts/partner_summaries/partner_counties_table');?>/"+year+"/"+month+"/"+partner);
				}
			});
		});
	}
</script>