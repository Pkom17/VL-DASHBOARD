<script type="text/javascript">
    localStorage.setItem("view_age2", 0);
    $().ready(function () {
        $.get("<?php echo base_url(); ?>template/dates", function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
        });
        $("#second").hide();

        $("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes_sup'); ?>");


        $("select").change(function () {
            em = $(this).val();
            // console.log(em);
            // Send the data using post
            var posting = $.post("<?php echo base_url(); ?>template/filter_regimen_data", {regimen: em});

            //      // Put the results in a div
            posting.done(function (data) {
                $.get("<?php echo base_url(); ?>template/dates", function (data) {
                    obj = $.parseJSON(data);

                    if (obj['month'] == "null" || obj['month'] == null) {
                        obj['month'] = "";
                    }
                    $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
                    $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
                });
                if (data == "") {
                    $("#second").hide();
                    $("#first").show();

                    $("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes_sup'); ?>");
                } else {
                    data = $.parseJSON(data);
                    $("#first").hide();
                    $("#second").show();

                    $("#samples").html("<center><div class='loader'></div></center>");
                    $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                    $("#regimen_gender").html("<center><div class='loader'></div></center>");
//                    $("#gender").html("<center><div class='loader'></div></center>");
//                    $("#age").html("<center><div class='loader'></div></center>");
                    $("#countiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#partnersRegimen").html("<center><div class='loader'></div></center>");
                    $("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#county").html("<center><div class='loader'></div></center>");

                    $("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/" + null + "/" + data);
                    $("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>");
                    $("#regimen_gender").load("<?php echo base_url('charts/ages/p_age_gender_regimen'); ?>/" + null + "/" + null + "/" + null + "/" + null + "/" + data + "/" + null + "/" + null);
//                    $("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/" + null + "/" + null + "/" + data);
//                    $("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/" + null + "/" + null + "/" + data);
                    $("#countiesRegimen").load("<?= base_url('charts/regimen/regimen_breakdowns'); ?>/" + null + "/" + null + "/" + data + "/" + null + "/" + null + "/" + 1);
                    $("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + null + "/" + null + "/" + data + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + null + "/" + null + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + null + "/" + null + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/" + null + "/" + null + "/" + data);
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
            to = format_date(second);
            var error_check = check_error_date_range(from, to);

            if (!error_check) {
                $.get("<?php echo base_url('regimen/check_regimen_select'); ?>", function (data) {
                    data = JSON.parse(data);
                    // console.log(data);
                    if (data == 0) {
                        $("#second").hide();
                        $("#first").show();

                        $("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes_sup'); ?>/" + from[1] + "/" + from[0] + "/" + to[1] + "/" + to[0]);
                    } else {
                        $("#first").hide();
                        $("#second").show();

                        $("#samples").html("<center><div class='loader'></div></center>");
                        $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                        $("#regimen_gender").html("<center><div class='loader'></div></center>");
//                        $("#gender").html("<center><div class='loader'></div></center>");
//                        $("#age").html("<center><div class='loader'></div></center>");
                        $("#countiesRegimen").html("<center><div class='loader'></div></center>");
                        $("#partnersRegimen").html("<center><div class='loader'></div></center>");
                        $("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
                        $("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
                        $("#county").html("<center><div class='loader'></div></center>");

                        $("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/" + from[1] + "/" + data);
                        $("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
                        $("#regimen_gender").load("<?php echo base_url('charts/ages/p_age_gender_regimen'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + data + "/" + to[1] + "/" + to[0]);
//                        $("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
//                        $("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
                        $("#countiesRegimen").load("<?= @base_url('charts/regimen/regimen_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + 1);
                        $("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + 1);
                        $("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + null + "/" + 1);
                        $("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0] + "/" + null + "/" + null + "/" + null + "/" + 1);
                        $("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + data + "/" + to[1] + "/" + to[0]);
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
        } else {
            year = id;
            month = null;
        }
        // console.log(year+"<___>"+month);
        var posting = $.post('<?php echo base_url(); ?>template/filter_date_data', {'year': year, 'month': month});

        // Put the results in a div
        posting.done(function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");

            $.get("<?php echo base_url('regimen/check_regimen_select'); ?>", function (data) {
                data = $.parseJSON(data);
                if (data == 0) {
                    $("#second").hide();
                    $("#first").show();

                    $("#regimen_outcomes").load("<?php echo base_url('charts/regimen/regimen_outcomes_sup'); ?>/" + year + "/" + month);
                } else {
                    $("#first").hide();
                    $("#second").show();

                    $("#samples").html("<center><div class='loader'></div></center>");
                    $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                    $("#regimen_gender").html("<center><div class='loader'></div></center>");
//                    $("#gender").html("<center><div class='loader'></div></center>");
//                    $("#age").html("<center><div class='loader'></div></center>");
                    $("#countiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#partnersRegimen").html("<center><div class='loader'></div></center>");
                    $("#subcountiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#FacilitiesRegimen").html("<center><div class='loader'></div></center>");
                    $("#county").html("<center><div class='loader'></div></center>");

                    $("#samples").load("<?php echo base_url('charts/regimen/sample_types'); ?>/" + year + "/" + data);
                    $("#vlOutcomes").load("<?php echo base_url('charts/regimen/regimen_vl_outcome'); ?>/" + year + "/" + month + "/" + data);
                    $("#regimen_gender").load("<?php echo base_url('charts/ages/p_age_gender_regimen'); ?>/" + year + "/" + month + "/" + null + "/" + null + "/" + data + "/" + null + "/" + null);
//                    $("#gender").load("<?php echo base_url('charts/regimen/regimen_gender'); ?>/" + year + "/" + month + "/" + data);
//                    $("#age").load("<?php echo base_url('charts/regimen/regimen_age'); ?>/" + year + "/" + month + "/" + data);
                    $("#countiesRegimen").load("<?= base_url('charts/regimen/regimen_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + to[1] + "/" + to[0] + "/" + 1);
                    $("#partnersRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#subcountiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#FacilitiesRegimen").load("<?php echo base_url('charts/regimen/regimen_breakdowns'); ?>/" + year + "/" + month + "/" + data + "/" + null + "/" + null + "/" + null + "/" + null + "/" + null + "/" + 1);
                    $("#county").load("<?php echo base_url('charts/regimen/regimen_county_outcomes'); ?>/" + year + "/" + month + "/" + data);
                }
            });

        });
    }

    function switch_source_vl_p_regimen_gender() {
        var view = localStorage.getItem("view_age2");
        if (view == 0) {
            localStorage.setItem("view_age2", 1);
            $("#p_age_gender_tests").hide();
            $("#p_age_gender_pat").show();
            $("#switchButton_vl_age").val('<?= lang('label.switch_all_tests') ?>');
            $(".vl_subcounty_heading").html('<?= lang('title_tested_patients_by_age') ?> ');
            $('#p_age_gender_pat_male').highcharts().reflow();
            $('#p_age_gender_pat_female').highcharts().reflow();
        } else {
            localStorage.setItem("view_age2", 0);
            $("#p_age_gender_tests").show();
            $("#p_age_gender_pat").hide();
            $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?> ');
            $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');
            $('#p_age_gender_tests_female').highcharts().reflow();
            $('#p_age_gender_tests_male').highcharts().reflow();
        }
    }
</script>