<div class="row">
  <div style="color:red;"><center><?=lang('label.click_on_selected')?></center></div>
  <div id="stacked_graph" class="col-md-12">

  </div>
  
  <div id="graphs">
  
  </div>


  
</div>

  


<script type="text/javascript">

  $().ready(function() {
    $("#year-month-filter").hide();
    $("#graphs").load("<?php echo base_url();?>charts/partner_trends/positive_trends");
    $("#stacked_graph").load("<?php echo base_url();?>charts/partner_trends/summary");


    $("select").change(function(){
      var partner_id = $(this).val();

      var posting = $.post( "<?php echo base_url();?>template/filter_partner_data", { partner: partner_id } );
      posting.done(function( data ) {
            $.get("<?php echo base_url();?>template/breadcrum/"+data+"/"+1, function(data){
              $("#breadcrum").html(data);
            });
      });

      $("#graphs").load("<?php echo base_url();?>charts/partner_trends/positive_trends/"+partner_id);
      $("#stacked_graph").load("<?php echo base_url();?>charts/partner_trends/summary/"+partner_id);
    });
  });
  
  $("select").change(function(){
    var partner_id = $(this).val();

    $("#graphs").load("<?php echo base_url();?>charts/partner_trends/positive_trends/"+partner_id);
    $("#stacked_graph").load("<?php echo base_url();?>charts/partner_trends/summary/"+partner_id);
  });

  function get_graphs(year){
    $.ajax({
           url: "<?php echo base_url('charts/partner_trends/positive_trends'); ?>/" + year,
           
           error: function(data) {
              $("#test").append(data);
           },
           dataType : "json",
           success: function(data) {
                
                
                $("#graphs").empty().append(data);
           },
           type: 'GET'
        });
  }

	
   
</script>