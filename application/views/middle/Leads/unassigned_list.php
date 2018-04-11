<?php
$param1 = isset($type) ? $type.'/' : '';
$param2 = isset($lead_source) ? encode_id($lead_source).'/' : '';
$source = $this->config->item('lead_source');
?>

<div class="page-content">
    <div class="container">
        <div class="unassigned-content">
        <div class="page-title">
                    <div class="container clearfix">
                        <h3 class="text-center">
                            <?php 
                                echo ucwords($source[$lead_source]);
                            ?>
                        </h3>
                    </div>
                </div>
        <?php if ($unassigned_leads) { ?>
        
        <?php }?>
        <span class="bg-top"></span>
        <div class="inner-content">
            <div class="container">
            <div class="float-left">
            <span class="total-lead" style="color: red">To assign the lead, select the checkbox and select employee from drop down<span style="color:red;">*</span></span>
        </div>
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
                                    <label><strong>Assign To :</strong> </label>&nbsp;&nbsp;
                                    <select name="assign_to">
                                        <option value="">Select Employee</option>
                                    <?php $result = get_details($this->session->userdata('admin_id'));?>
                                        <option value="<?php echo $this->session->userdata('admin_id').'-'.ucwords(strtolower($this->session->userdata('admin_name'))); ?>"><?php echo ucwords(strtolower($this->session->userdata('admin_name')));?></option>
                                        <?php foreach ($result['list'] as $key =>$value){?>
                                        <option value="<?php echo $value->DESCR10.'-'.$value->DESCR30;?>"><?php echo ucwords($value->DESCR30);?></option>
                                        <?php }?>
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
                                <div class="form-control form-submit clearfix" id="btn-r">
                                    <button type="submit" name="Submit" value="Submit" class="full-btn float-right">
<img src="<?php echo base_url().ASSETS;?>images/left-nav.png" alt="left-nav" class="left-btn-img">
<span class="btn-txt">Submit</span>
<img src="<?php echo base_url().ASSETS;?>images/right-nav.png" alt="left-nav" class="right-btn-img">
</button>

                                </div>
                            <?php
                                    }   
                                //}
                            ?>
<!--                             <a href="--><?php //echo base_url('leads/export_excel_listing/'.$param1.$param2);?><!--">-->
<!--                                <img src="--><?php //echo base_url().ASSETS;?><!--images/excel-btn.png" alt="btn">-->
<!--                            </a>-->
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
                        <?php if($lead_source == 'walkin'){?>
                            <th><input type="text" name="customername" placeholder="Ticket Size"></th>
                        <?php }?>

                        <th><input type="text" name="customername" placeholder="Search Days"></th>
                        <th></th>
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
                        <th style="text-align:left">Customer Name</th>
                        <th style="text-align:left">Product Name</th>
                        <?php if($lead_source == 'walkin'){?>
                        <th style="text-align:left">Ticket Size (In Lacs)</th>
                        <?php }?>
                        <th style="text-align:center">Elapsed Days</th>
                        <th style="text-align:center">Generated BY</th>
                        <th style="text-align:left">Details</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if ($unassigned_leads) {
                            $i = 0;
                            foreach ($unassigned_leads as $key => $value) {
                                $branch_mapp = get_branch_map($value['mapping'],$this->session->userdata('branch_id'));
                                $branch_map = $branch_mapp[0]['processing_center'];
                    ?>
                            <tr>
                                <td  style="text-align:center">
                                <?php if($value['lead_source'] != 'walkin' || ($value['lead_source'] == 'walkin' && $value['mapping'] == 'BRANCH' || ($value['reroute_from_branch_id'] != '' || $value['reroute_from_branch_id'] != NULL))) {

                                    $data = array(
                                        'name' => 'lead_ids[]',
                                        'id' => 'check_all',
                                        'value' => $value['id'] . '-' . $value['customer_name'] . '-' . $value['product_title'],
                                        'class' => 'multi_check'
                                    );
                                    echo form_checkbox($data);
                                    // Would produce: <input type="checkbox" name="newsletter" id="newsletter" value="1" style="margin:10px" />
                                }
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
                                <?php if($lead_source == 'walkin'){?>
                                <td>
                                    <?php echo convertCurrency($value['lead_ticket_range']); ?>
                                </td>
                                    <?php }?>
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
                                    <?php
                                    if(!empty($value['created_by_branch_id'])){
                                        $generatedb = branchname($value['created_by_branch_id']);
                                        echo ucwords($generatedb[0]['name']);
                                    }else{
                                        echo ucwords($source[$lead_source]);
                                    }

                                    ?>
                                </td>
                                <td>
                                    <a href="<?php echo site_url('leads/lead_life_cycle/'.encode_id($value['id']))?>">Life Cycle</a>
                                    <?php if($value['lead_source'] == 'walkin' && ($value['mapping'] != 'BRANCH' && $value['mapping'] == $branch_map) && ($value['reroute_from_branch_id'] == '' || $value['reroute_from_branch_id'] == NULL)){?>
                                    <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="javascript:void(0);" class="send_rapc" data="<?php echo encode_id($value['id']);?>">Send To <?php echo $branch_map;?></a>
                                    <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="javascript:void(0);" class="drop_lead" data="<?php echo encode_id($value['id']);?>">Drop </a>
                                    <?php }?>
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
        var chkbox = 1;


        //Initialize datatable configuration
        initTable(table, columns,chkbox);

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

        $(document).on('click', '.send_rapc', function(){
            if (window.confirm('CIR / CIBIL report generated and Lead is Qualified?'))
            {
                $.ajax({
                    method:'POST',
                    url: baseUrl + 'leads/move_rapc',
                    data:{
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        id:$(this).attr('data')
                    }
                }).success(function (resp) {
                    location.reload();
                });
            }
        });

         $(document).on('click', '.drop_lead', function(){
            if (window.confirm('Are you sure want to drop this lead?'))
            {
                $.ajax({
                    method:'POST',
                    url: baseUrl + 'leads/drop_lead',
                    data:{
                        '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                        id:$(this).attr('data')
                    }
                }).success(function (resp) {
                    location.reload();
                });
            }
        });

    });
</script>
