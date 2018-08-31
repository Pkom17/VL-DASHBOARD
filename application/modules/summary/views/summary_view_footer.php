<script type="text/javascript">
    $().ready(function () {
        localStorage.setItem("my_var", 0);
        $.get("<?php echo base_url(); ?>template/dates", function (data) {
            obj = $.parseJSON(data);
            // console.log(obj);
            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
        });
        $("#nattat").load("<?php echo base_url('charts/summaries/turnaroundtime'); ?>");
        $("#coverage").load("<?php echo base_url('charts/summaries/vl_coverage'); ?>");
        $("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>");
        $("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>");
        $("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>");
        $("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>");
        $("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>");
        $("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>");

        $(".display_date").load("<?php echo base_url('charts/summaries/display_date'); ?>");
        $(".display_range").load("<?php echo base_url('charts/summaries/display_range'); ?>");

        $("select").change(function () {
            em = $(this).val();
            var all = localStorage.getItem("my_var");

            // Send the data using post
            var posting = $.post("<?php echo base_url(); ?>template/filter_county_data", {county: em});

            // Put the results in a div
            posting.done(function (data) {
                if (data != "") {
                    data = JSON.parse(data);
                }
                /*$.get("<?php echo base_url(); ?>template/breadcrum/"+data, function(data){
                 $("#breadcrum").html(data);
                 });*/
                $.get("<?php echo base_url(); ?>template/dates", function (data) {
                    obj = $.parseJSON(data);

                    if (obj['month'] == "null" || obj['month'] == null) {
                        obj['month'] = "";
                    }
                    $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
                    $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
                });

                // alert(data);
                $('#heading').html('<?= lang('label.county_sites_outcomes') ?> <div class="display_date"></div>');
                $("#nattat").html("<div><?= lang('label.loading') ?></div>");
                $("#coverage").html("<div><?= lang('label.loading') ?></div>");
                $("#samples").html("<center><div class='loader'></div></center>");
                $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                $("#justification").html("<center><div class='loader'></div></center>");
                $("#ageGroups").html("<center><div class='loader'></div></center>");
                $("#gender").html("<center><div class='loader'></div></center>");

                $("#nattat").load("<?php echo base_url('charts/summaries/turnaroundtime'); ?>");
                if (data != "") {
                    $("#coverage").load("<?php echo base_url('charts/summaries/vl_coverage'); ?>/" + 1 + "/" + data);
                } else {
                    $("#coverage").load("<?php echo base_url('charts/summaries/vl_coverage'); ?>");
                }

                $("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/" + null + "/" + data + "/" + null + "/" + all);
                $("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/" + null + "/" + null + "/" + data);
                $("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/" + null + "/" + null + "/" + data);
                $("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/" + null + "/" + null + "/" + data);
                $("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/" + null + "/" + null + "/" + data);
                $("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/" + null + "/" + null + "/" + null + "/" + null + "/" + data);
            });
        });

        $("button").click(function () {
            var first, second;
            first = $(".date-picker[name=startDate]").val();
            second = $(".date-picker[name=endDate]").val();
            var all = localStorage.getItem("my_var");

            var new_title = set_multiple_date(first, second);

            $(".display_date").html(new_title);

            from = format_date(first);
            /* from is an array
             [0] => month
             [1] => year*/
            to = format_date(second);
            var error_check = check_error_date_range(from, to);

            if (!error_check) {
                $("#nattat").html("<div><?= lang('label.loading') ?></div>");
                $("#samples").html("<center><div class='loader'></div></center>");
                $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                $("#justification").html("<center><div class='loader'></div></center>");
                $("#ageGroups").html("<center><div class='loader'></div></center>");
                $("#gender").html("<center><div class='loader'></div></center>");
                $("#county").html("<center><div class='loader'></div></center>");

                $("#nattat").load("<?php echo base_url('charts/summaries/turnaroundtime'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + to[1] + "/" + to[0]);
                $("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/" + from[1] + "/" + null + "/" + null + "/" + all);
                $("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                $("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                $("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                $("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                $("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
            }

        });
        $('#agemodal').on('hidden.bs.modal', function () {
            location.reload();
        });
    });

    function date_filter(criteria, id)
    {
        if (criteria === "monthly") {
            year = null;
            month = id;
        } else {
            year = id;
            month = null;
        }

        var all = localStorage.getItem("my_var");
        var posting = $.post('<?php echo base_url(); ?>template/filter_date_data', {'year': year, 'month': month});

        // Put the results in a div
        posting.done(function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");

        });

        $("#nattat").html("<div><?= lang('label.loading') ?></div>");
        $("#samples").html("<center><div class='loader'></div></center>");
        $("#vlOutcomes").html("<center><div class='loader'></div></center>");
        $("#justification").html("<center><div class='loader'></div></center>");
        $("#ageGroups").html("<center><div class='loader'></div></center>");
        $("#gender").html("<center><div class='loader'></div></center>");
        $("#county").html("<center><div class='loader'></div></center>");

        $("#nattat").load("<?php echo base_url('charts/summaries/turnaroundtime'); ?>/" + year + "/" + month);
        $("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/" + year + "/" + null + "/" + null + "/" + all);
        $("#vlOutcomes").load("<?php echo base_url('charts/summaries/vl_outcomes'); ?>/" + year + "/" + month);
        $("#justification").load("<?php echo base_url('charts/summaries/justification'); ?>/" + year + "/" + month);
        $("#ageGroups").load("<?php echo base_url('charts/summaries/age'); ?>/" + year + "/" + month);
        $("#gender").load("<?php echo base_url('charts/summaries/gender'); ?>/" + year + "/" + month);
        $("#county").load("<?php echo base_url('charts/summaries/county_outcomes'); ?>/" + year + "/" + month);
    }

    function switch_source() {
        var all = localStorage.getItem("my_var");

        if (all == 0) {
            localStorage.setItem("my_var", 1);
            all = 1;
            $("#samples_heading").html('<?= lang('label.testing_trends_for_all_tests') ?>');
            $("#switchButton").val('<?= lang('label.switch_routine_tests_trends') ?>');

        } else {
            localStorage.setItem("my_var", 0);
            all = 0;
            $("#samples_heading").html('<?= lang('label.testing_trends_for_routine_VL') ?> ');
            $("#switchButton").val('<?= lang('label.switch_all_tests') ?> ');
        }
        $("#samples").load("<?php echo base_url('charts/summaries/sample_types'); ?>/" + null + "/" + null + "/" + null + "/" + all);

    }

    function ageModal()
    {
        $('#agemodal').modal('show');
        $('#CatAge').load('<?php echo base_url(); ?>charts/summaries/agebreakdown');
        // window.location.reload();
    }

    function justificationModal()
    {
        // alert('Why are you not working!');
        $('#justificationmodal').modal('show');
        $('#CatJust').load('<?php echo base_url(); ?>charts/summaries/justificationbreakdown');
    }

</script>