<?php
$statuses = $this->config->item('lead_status');
$statuses = array_values($statuses);


$lead_status = $this->config->item('lead_status');
$data = array();
if ($lead_data) {
    foreach ($lead_data as $k => $value) {
        $fullname = array_map('trim', explode('.', $value['employee_name']));
        if ($fullname[0] == '') {
            $fullname1 = trim($fullname[1]);
        } else {
            $fullname1 = trim($fullname[0]);
        }
        $created_by_name = array_map('trim', explode('.', $value['created_by_name']));
        if ($created_by_name[0] == '') {
            $created_by_name1 = trim($created_by_name[1]);
        } else {
            $created_by_name1 = trim($created_by_name[0]);
        }
        if ($k == 0) {
            $data[$k][] = $lead_status[$value['status']];
            $data[$k][] = date('d-m-Y', strtotime($value['generated_on']));
        } else {
            $data[$k][] = $lead_status[$value['status']];
            $data[$k][] = date('d-m-Y', strtotime($value['assigned_on']));
        }

        if (false !== $key = array_search($lead_status[$value['status']], $statuses)) {
            $keys[$key] = $key;
            $assigned_by[$key] = $created_by_name1;
        }
        $data[$k][] = $key;
    }
}
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">Lead Cycle</h3>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-form">
                <?php
                if (!empty($lead_data)) {
                    foreach ($lead_data as $key => $value) {
                        $fullname = array_map('trim', explode('.', $value['employee_name']));
                        if ($fullname[0] == '') {
                            $fullname1 = trim($fullname[1]);
                        } else {
                            $fullname1 = trim($fullname[0]);
                        }
                        $created_by_name = array_map('trim', explode('.', $value['created_by_name']));
                        if ($created_by_name[0] == '') {
                            $created_by_name1 = trim($created_by_name[1]);
                        } else {
                            $created_by_name1 = trim($created_by_name[0]);
                        }
                        if (($key + 1) % 2 != 0) {
                            ?>
                            <div class="lead-form-left">
                                <div class="form-control">
                                    <label>
                                        <?php echo "Lead Generated By " . $fullname1 . " On " . $value['generated_on']; ?>
                                    </label>
                                </div>
                                <?php if (!empty($value['assigned_on'])) { ?>
                                    <div class="form-control">
                                        <label><?php echo "Lead Assigned By " . $created_by_name1 . " On " . $value['assigned_on']; ?></label>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($value['reroute_from_branch_id'])) { ?>
                                    <div class="form-control">
                                        <label><?php echo "Lead Reroute From Branch " . $value['reroute_from_branch_id']; ?></label>
                                    </div>
                                    <div class="form-control">
                                        <label><?php echo "Lead Reroute To Branch " . $value['branch_id']; ?></label>
                                    </div>
                                <?php } ?>

                                <?php if (!empty($value['status'])) { ?>
                                    <div class="form-control">
                                        <label><?php echo "Lead Status : " . $lead_status[$value['status']]; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else {
                            ?>

                            <div class="lead-form-right">
                            <div class="form-control">
                                <label>
                                    <?php echo "Lead Generated By " . $fullname1 . " On " . $value['generated_on']; ?>
                                </label>
                            </div>
                            <?php if (!empty($value['assigned_on'])) { ?>
                                <div class="form-control">
                                    <label><?php echo "Lead Assigned By " . $created_by_name1 . " On " . $value['assigned_on']; ?></label>
                                </div>
                            <?php } ?>

                            <?php if (!empty($value['reroute_from_branch_id'])) { ?>
                                <div class="form-control">
                                    <label><?php echo "Lead Reroute From Branch " . $value['reroute_from_branch_id']; ?></label>
                                </div>
                                <div class="form-control">
                                    <label><?php echo "Lead Reroute To Branch " . $value['branch_id']; ?></label>
                                </div>
                            <?php } ?>

                            <?php if (!empty($value['status'])) { ?>
                                <div class="form-control">
                                    <label><?php echo "Lead Status : " . $lead_status[$value['status']]; ?></label>
                                </div>
                                </div>
                            <?php }
                        }
                    }
                } ?>
            </div>
        </div>
        <span class="bg-bottom"></span>
    </div>
    <span class="bg-top"></span>
    <div class="inner-content">
        <div id="container" style="min-width: 310px; height: auto; margin: 0 auto"></div>
    </div>
    <script src="<?php echo base_url() . PLUGINS; ?>highcharts/js/highcharts.js"></script>
    <script src="<?php echo base_url() . PLUGINS; ?>highcharts/js/exporting.js"></script>
    <script src="<?php echo base_url() . PLUGINS; ?>highcharts/js/chart.js"></script>
    <script>
        Highcharts.chart('container', {
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%e of %b'
                }
            },
            tooltip: {
                formatter: function () {
                    var todaydate = new Date(this.point.x);  //pass val varible in Date(val)
                    var dd = todaydate.getDate();
                    var mm = todaydate.getMonth() + 1; //January is 0!
                    var yyyy = todaydate.getFullYear();
                    if (dd < 10) {
                        dd = '0' + dd
                    }
                    if (mm < 10) {
                        mm = '0' + mm
                    }
                    var datenew = dd + '-' + mm + '-' + yyyy;
                    var assigned_by = <?php echo json_encode($assigned_by);?>;
                    var status = <?php echo json_encode($statuses);?>;
                    return 'Status: <b>' + status[this.point.y] + '</b><br> Date : <b>' + datenew+'</b><br> Assigned By : <b>'+assigned_by[this.point.y]+'</b>';
                }
            },
            series: [
                {
                    showInLegend: false,
                    data: [
                        <?php foreach ($data as $_k => $_val) {
                        $month = date('m', strtotime($_val[1])) - 1;
                        echo '[Date.UTC(' . date('Y', strtotime($_val[1])) . ',' . $month . ',' . date('d', strtotime($_val[1])) . '),' . $_val[2] . '],';
                    }?>
                    ]
                }]
        });
    </script>