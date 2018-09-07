<div class="row">

  <div class="col-md-4 col-md-offset-4">
    <div id="breadcrum" class="alert" style="background-color: #2f80d1;text-align: center;vertical-align: middle;" onclick="switch_source()">
          <span id="current_source"><?=  lang('label.toggle_quart_year')?></span>   
      </div>
         
  </div>
</div>

<div class="row">
  <div style="color:red;"><center><?=  lang('label.view_for_year_quart_selected')?> </center></div>

  <div id="first">

    <div id="stacked_graph" class="col-md-12">

    </div>
    
    <div id="graphs">
    
    </div>

  </div>

  <div id="second">

    <div id="q_outcomes" class="col-md-12">
    
    </div>
    
    <div id="q_graphs">
    
    </div>

  </div>
  
</div>

  


<script type="text/javascript">

  $().ready(function() {
    $("#year-month-filter").hide();
    $("#range").hide();
    $("#graphs").load("<?php echo base_url();?>charts/trends/positive_trends");
    $("#stacked_graph").load("<?php echo base_url();?>charts/trends/summary");
    localStorage.setItem("my_var", 0);


    $("select").change(function(){
      var county_id = $(this).val();

      var posting = $.post( "<?php echo base_url();?>template/filter_county_data", { county: county_id } );
      posting.done(function( data ) {
            /*$.get("<?php echo base_url();?>template/breadcrum/"+data, function(data){
              $("#breadcrum").html(data);
            });*/
      });
      var a = localStorage.getItem("my_var");

      if(a == 0){
        $("#first").show();
        $("#second").hide();

        $("#graphs").load("<?php echo base_url();?>charts/trends/positive_trends/"+county_id);
        $("#stacked_graph").load("<?php echo base_url();?>charts/trends/summary/"+county_id);
      }
      else{
        $("#first").hide();
        $("#second").show();
        $("#q_outcomes").load("<?php echo base_url();?>charts/trends/quarterly_outcomes/"+county_id);
        $("#q_graphs").load("<?php echo base_url();?>charts/trends/quarterly/"+county_id);
      }

    });
  });

  function switch_source(){
    var a = localStorage.getItem("my_var");

    if(a == 0){
      localStorage.setItem("my_var", 1);
      $("#first").hide();
      $("#second").show();
      $("#q_outcomes").load("<?php echo base_url();?>charts/trends/quarterly_outcomes/");
      $("#q_graphs").load("<?php echo base_url();?>charts/trends/quarterly/");
    }
    else{
      localStorage.setItem("my_var", 0);
      $("#first").show();
      $("#second").hide();

      $("#graphs").load("<?php echo base_url();?>charts/trends/positive_trends/");
      $("#stacked_graph").load("<?php echo base_url();?>charts/trends/summary/");
    }
  }
  
 

  function get_graphs(year){
    $.ajax({
           url: "<?php echo base_url('charts/trends/positive_trends'); ?>/" + year,
           
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