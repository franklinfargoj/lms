<div class="page-content">
    <div class="container">
        <div class="unassigned-content">
        <div class="page-title">
                    <div class="container clearfix">
                        <h3 class="text-center">
                            <?php 
                                echo ucwords($lead_source);
                            ?>
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
                
                <?php 
                    //Form
                    $attributes = array(
                        'role' => 'form',
                        'id' => 'assign_multiple',
                        'class' => 'form',
                        'autocomplete' => 'off'
                    );
                    echo form_open(site_url().'leads/assign_multiple', $attributes);
                ?>
                <div class="lead-top">
                    <div class="container clearfix">
                        <div class="float-left">
                            <span class="total-lead">
                                Total Leads
                            </span>
                            <span class="lead-num"> : <?php echo count($unassigned_leads);?></span>
                        </div>
                        <div class="float-right">
                            <?php 
                                //if(ucwords($lead_source) != 'Walk-in'){
                                    if ($unassigned_leads) {
                            ?>
                                <div class="form-control" id="finline">
                                    <label>Assign To : </label>&nbsp;&nbsp;   
                                    <select name="assign_to">
                                        <option value="">Select Employee</option>
                                        <option value="2">Employee 1</option>
                                    </select>
                                    <?php 
                                        /*foreach ($unassigned_leads as $key => $value) {
                                            $data = array(
                                                'lead_ids[]' => $value['id'],
                                            );
                                            echo form_hidden($data);
                                        }*/
                                        $data1 = array(
                                            'lead_source' => $lead_source,
                                        );
                                        echo form_hidden($data1);
                                    ?>
                                </div>
                                <div class="form-control form-submit clearfix" id="btnravish">
                                    <a href="javascript:void(0);" class="float-right">
                                            <img src="<?php echo base_url().ASSETS;?>images/left-nav.png">
                                            <span><input type="submit" class="custom_button" value="Submit" /></span>
                                            <img src="<?php echo base_url().ASSETS;?>images/right-nav.png">
                                    </a>
                                </div>
                            <?php
                                    }   
                                //}
                            ?>
                            <!-- <a href="">
                                <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
                            </a> -->
                        </div>
                    </div>
                </div>
                <table class="upload-table lead-table" id="sample_3">
                    <thead>
                    <tr class="top-header">
                        <th></th>
                        <th></th>
                        <th><input type="text" name="customername" placeholder="Search Customer Name"></th>
                        <th><input type="text" name="customername" placeholder="Search Product Name"></th>
                        <th><input type="text" name="customername" placeholder="Search Days"></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>
                        <?php 
                            $data = array(
                                'name'          => 'check_all',
                                'id'            => 'check_all',
                                'class'         => 'grp_check'
                            );
                            echo form_checkbox($data);
                            // Would produce: <input type="checkbox" name="newsletter" id="newsletter" value="1" style="margin:10px" />
                        ?>
                        </th>
                        <th style="text-align:center">Sr. No</th>
                        <th>Customer Name</th>
                        <th>Product Name</th>
                        <th style="text-align:center">Elapsed Days</th>
                        <th>Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
                        if ($unassigned_leads) {
                            $i = 0;
                            foreach ($unassigned_leads as $key => $value) {
                    ?>
                            <tr>
                                <td >
                                <?php 
                                    $data = array(
                                        'name'          => 'lead_ids[]',
                                        'id'            => 'check_all',
                                        'value'         => $value['id'],
                                        'class'         => 'multi_check'
                                    );
                                    echo form_checkbox($data);
                                    // Would produce: <input type="checkbox" name="newsletter" id="newsletter" value="1" style="margin:10px" />
                                ?>
                                </td>
                                <td style="text-align:center">
                                    <?php 
                                        echo ++$i; 
                                    ?>

                                </td>
                                <td>
                                    <?php echo ucwords($value['lead_name']); ?>
                                </td>
                                <td>
                                    <?php echo ucwords($value['product_title']); ?>
                                </td>
                                <td  style="text-align:center">
                                    <?php $created_date = explode(' ', $value['created_on']);

                                    $now = date_create(date('Y-m-d')); // or your date as well
                                    //echo $created_date[0];
                                    $generated_date = date_create($created_date[0]);
                                    $datediff = date_diff($now, $generated_date);
                                    echo $datediff->format("%a ");
                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('leads/unassigned_leads_details/'.encode_id($lead_source).'/'. encode_id($value['id'])); ?>">View</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php 
                    echo form_close();
                ?>
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
        var columns = [0,5];


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
