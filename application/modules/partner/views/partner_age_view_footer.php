<script type="text/javascript">
    $().ready(function () {
        localStorage.setItem("view_age", 0);
        localStorage.setItem("view_age2", 0);
        $.get("<?php echo base_url(); ?>template/dates", function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
        });
        $("#second").hide();
        $("#first").show();
        $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>");

        $("#partner").change(function () {
            part = $(this).val();
            // Send the data using post
            var posting = $.post("<?php echo base_url(); ?>template/filter_partner_data", {partner: part});

            // Put the results in a div
            posting.done(function (data) {
                $("#age_category").val(0).trigger('change');
                $.get("<?php echo base_url(); ?>template/dates", function (data) {
                    obj = $.parseJSON(data);

                    if (obj['month'] == "null" || obj['month'] == null) {
                        obj['month'] = "";
                    }
                    $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
                    $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");
                });

                //reset 
                localStorage.setItem("view_age2", 0);
                $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?>');
                $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');



                // Condition to dispay the proper divs based on whether a partner is selected or not
                if (data == 'null') {
                    $("#second").hide();
                    $("#first").show();
                    // fetching the partner outcomes
                    $("#age_outcomes").html("<center><div class='loader'></div></center>");
                    $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + null + "/" + null + "/" + null + "/" + null + "/" + null + "/" + null);
                } else {
                    data = JSON.parse(data);
                    // alert(data);
                    $("#second").hide();
                    $("#first").show();
                    // fetching the partner outcomes
                    $("#age_outcomes").html("<center><div class='loader'></div></center>");
                    $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + null + "/" + null + "/" + null + "/" + data + "/" + null + "/" + null);
                }
            });
        });

        $("#age_category").change(function () {
            age = $(this).val();

            var posting = $.post("<?php echo base_url(); ?>template/filter_partner_age_data", {ageCat: age});
            posting.done(function (adata) {
                $.get("<?php echo base_url(); ?>partner/check_partner_select", function (data) {
                    partner = JSON.parse(data);
                    if (partner == 0) {
                        partner = null;
                    }
                    if (adata == null || adata == "" || adata == undefined) {
                        $("#second").hide();
                        $("#first").show();
                        //reset 
                        localStorage.setItem("view_age2", 0);
                        $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?>');
                        $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');
                        $("#age_outcomes").html("<center><div class='loader'></div></center>");
                        $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + null + "/" + null + "/" + null + "/" + partner + "/" + null + "/" + null);
                    } else {
                        $("#first").hide();
                        $("#second").show();
                        adata = parseInt(adata);

                        $("#samples").html("<center><div class='loader'></div></center>");
                        $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                        $("#gender").html("<center><div class='loader'></div></center>");
                        // $("#county").html("<center><div class='loader'></div></center>");

                        $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + null + "/" + adata + "/" + partner);
                        $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>/" + null + "/" + null + "/" + adata + "/" + null + "/" + null + "/" + partner);
                        $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + null + "/" + null + "/" + adata + "/" + null + "/" + null + "/" + partner);
                        // $("#county").load("<?php echo base_url('charts/ages/age_county_outcomes'); ?>/" + null + "/" + null + "/" + adata + "/" + null + "/" + null + "/" + partner);
                    }
                });
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
            var new_range = to[1] - 1 + " - " + to[1];
            $(".display_range").html(new_range);
            var error_check = check_error_date_range(from, to);

            if (!error_check) {
                $.get("<?php echo base_url(); ?>partner/check_partner_select", function (data) {
                    partner = JSON.parse(data);

                    if (partner == 0) {
                        $("#second").hide();
                        $("#first").show();
                        //reset 
                        localStorage.setItem("view_age2", 0);
                        $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?>');
                        $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');
                        $("#age_outcomes").html("<center><div class='loader'></div></center>");
                        $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + from[1] + "/" + from[0] + "/" + null + "/" + null + "/" + to[1] + "/" + to[0]);
                    } else {
                        partner = $.parseJSON(partner);
                        $.get("<?php echo base_url(); ?>partner/check_partner_age_select", function (data) {
                            adata = JSON.parse(data);
                            if (adata == 0) {
                                $("#second").hide();
                                $("#first").show();

                                $("#age_outcomes").html("<center><div class='loader'></div></center>");
                                $("#age_outcomes").load("<?php echo base_url('charts/ages/age_outcomes'); ?>/" + from[1] + "/" + from[0] + "/" + to[1] + "/" + to[0] + "/" + partner);
                            } else {
                                $("#second").show();
                                $("#first").hide();

                                $("#samples").html("<center><div class='loader'></div></center>");
                                $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                                $("#justification").html("<center><div class='loader'></div></center>");
                                $("#gender").html("<center><div class='loader'></div></center>");

                                $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + to[1] + "/" + adata + "/" + partner);
                                $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>/" + from[1] + "/" + from[0] + "/" + adata + "/" + to[1] + "/" + to[0] + "/" + partner);
                                $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + from[1] + "/" + from[0] + "/" + adata + "/" + to[1] + "/" + to[0] + "/" + partner);
                            }
                        });
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

        var posting = $.post('<?php echo base_url(); ?>template/filter_date_data', {'year': year, 'month': month});

        // Put the results in a div
        posting.done(function (data) {
            obj = $.parseJSON(data);

            if (obj['month'] == "null" || obj['month'] == null) {
                obj['month'] = "";
            }
            $(".display_date").html("( " + obj['year'] + " " + obj['month'] + " )");
            $(".display_range").html("( " + obj['prev_year'] + " - " + obj['year'] + " )");

            $.get("<?php echo base_url(); ?>partner/check_partner_select", function (data) {
                partner = data;

                if (partner == 0) {
                    $("#second").hide();
                    $("#first").show();
                    //reset 
                    localStorage.setItem("view_age2", 0);
                    $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?>');
                    $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');
                    $("#age_outcomes").html("<center><div class='loader'></div></center>");
                    $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + year + "/" + month + "/" + null + "/" + null + "/" + null + "/" + null);
                } else {
                    partner = $.parseJSON(partner);
                    $.get("<?php echo base_url(); ?>partner/check_partner_age_select", function (data) {
                        adata = JSON.parse(data);
                        if (adata == 0) {
                            $("#second").hide();
                            $("#first").show();
                            //reset 
                            localStorage.setItem("view_age2", 0);
                            $("#switchButton_vl_age").val('<?= lang('label.switch_routine_tests_trends') ?>');
                            $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> '); 
                            
                            $("#age_outcomes").html("<center><div class='loader'></div></center>");
                            $("#age_outcomes").load("<?php echo base_url('charts/ages/p_age_gender2'); ?>/" + year + "/" + month + "/" + null + "/" + partner + "/" + null + "/" + null);
                        } else {
                            $("#second").show();
                            $("#first").hide();

                            $("#samples").html("<center><div class='loader'></div></center>");
                            $("#vlOutcomes").html("<center><div class='loader'></div></center>");
                            $("#justification").html("<center><div class='loader'></div></center>");
                            $("#ageGroups").html("<center><div class='loader'></div></center>");
                            $("#gender").html("<center><div class='loader'></div></center>");
                            $("#partner").html("<center><div class='loader'></div></center>");

                            $("#samples").load("<?php echo base_url('charts/ages/sample_types'); ?>/" + year + "/" + adata + "/" + partner);
                            $("#vlOutcomes").load("<?php echo base_url('charts/ages/age_vl_outcome'); ?>/" + year + "/" + month + "/" + adata + "/" + null + "/" + null + "/" + partner);
                            $("#gender").load("<?php echo base_url('charts/ages/age_gender'); ?>/" + year + "/" + month + "/" + adata + "/" + null + "/" + null + "/" + partner);
                            $("#county").load("<?php echo base_url('charts/ages/age_county_outcomes'); ?>/" + year + "/" + month + "/" + adata + "/" + null + "/" + null + "/" + partner);
                        }
                    });
                }
            });
        });
    }

    function switch_source_vl_age1() {
        var view = localStorage.getItem("view_age");
        if (view == 0) {
            localStorage.setItem("view_age", 1);
            $("#vl_county_pie_tests").hide();
            $("#vl_county_pie_pat").show();
            $("#switchButton_subcounty").val('<?= lang('label.switch_all_tests') ?>');
            $(".vl_subcounty_heading").html('<?= lang('title_tested_patients_by_age') ?> ');
            $('#vl_county_pie_pat').highcharts().reflow();
        } else {
            localStorage.setItem("view_age", 0);
            $("#vl_county_pie_tests").show();
            $("#vl_county_pie_pat").hide();
            $("#switchButton_subcounty").val('<?= lang('label.switch_routine_tests_trends') ?> ');
            $(".vl_subcounty_heading").html('<?= lang('title_tested_done_by_age') ?> ');
            $('#vl_county_pie_tests').highcharts().reflow();
        }
    }
    function switch_source_vl_p_age1() {
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