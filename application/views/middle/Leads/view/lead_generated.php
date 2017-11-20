<?php
$param1 = isset($type) ? $type.'/' : '';
$param2 = isset($lead_source) ? encode_id($lead_source).'/' : '';
$source = $this->config->item('lead_source');
?>

<div class="page-content">
    <div class="">
        <div class="unassigned-content">
            <div class="page-title">
                <div class="container clearfix">
                    <h3 class="text-center">
                        Leads Generated By Me
                    </h3>
                </div>
            </div>
            <span class="bg-top"></span>
            <div class="inner-content">
                <div class="container">
                    <!-- BEGIN PAGE LEVEL STYLES -->
                    <link href="<?php echo base_url() . ASSETS; ?>css/jquery.dataTables.min.css" rel="stylesheet">
                    <!-- END PAGE LEVEL STYLES -->
                    <!-- BEGIN PRODUCT CATEGOEY-->
                    <table class="upload-table lead-table" id="sample_3">
                        <thead>
                        <tr class="top-header">
                            <th></th>
                            <th><input type="text" name="customername" placeholder="Search Customer Name"></th>
                            <th><input type="text" name="customername" placeholder="Search Product Name"></th>
                            <th></th>
                        </tr>
                        <tr>
                            <th style="text-align:center">Sr. No</th>
                            <th style="text-align:left">Customer Name</th>
                            <th style="text-align:left">Product Name</th>
                            <th style="text-align:left">Status</th>
                            <th style="text-align:left">Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($generated_leads) && !empty($generated_leads)) {
                            $i = 0;
                            foreach ($generated_leads as $key => $value) {
                                ?>
                                <tr>
                                    <td style="text-align:center">
                                        <?php
                                        echo ++$i;
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo ucwords($value['lead_name']); ?>
                                    </td>
                                    <td>
                                        <?php echo ucwords($value['title']); ?>
                                    </td>
                                    <td>
                                        <?php $st = get_status($value['id']);
                                        pe($st);
                                         if(empty($st)){
                                            echo " --- ";
                                         }else{
 echo $st['status'];
                                         }

                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo site_url('leads/lead_life_cycle/'.encode_id($value['id']))?>">Life Cycle</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <span class="bg-bottom"></span>
        </div>
    </div>
</div>
<script src="<?php echo base_url() . ASSETS; ?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() . ASSETS; ?>js/config.datatable.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $("#assign_multiple").validate({
            rules: {
                assign_to: {
                    required: true
                }
            },
            messages: {
                assign_to: {
                    required: "Please select employee"
                }
            }
        });

        var table = $('#sample_3');
        var columns = [0,3];


        //Initialize datatable configuration
        initTable(table, columns);

        $(".grp_check").change(function () {
            $(".multi_check").prop('checked', $(this).prop("checked"));
        });
        $(".multi_check").change(function () {
            if ($(".multi_check:not(:checked)").length == 0) {
                $(".grp_check").prop('checked', true);
            } else {
                $(".grp_check").prop('checked', '');
            }
        });

    });
</script>
