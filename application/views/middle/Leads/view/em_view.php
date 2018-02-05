<?php
$lead_type = $this->config->item('lead_type');
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN LEADS -->
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center">
            <?php 
                if(isset($status)){
                $lead_status = $this->config->item('lead_status');
                        echo $lead_status[$status];    
                }else{
                    echo ucwords($type);
                }

            ?>
            Leads
        </h3>
    </div>
</div>
<div class="lead-top">
    <div class="container clearfix">
        <div class="float-left">
            <span class="total-lead">
                Total
            </span>
            <span class="lead-num"> : <?php echo count($leads);?></span>
        </div>
        <div class="float-right">
            <a href="">
                <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
            </a>
        </div>
    </div>
</div>
<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <div class="lead-top clearfix">
                <div class="float-left">
                    <span class="total-lead">Total <?php echo ucwords($type);?> Leads</span>
                    <span class="lead-num"> : <?php echo count($leads);?></span>
                </div>
                <div class="float-right">
                    <a href="">
                        <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
                    </a>
                </div>
            </div>
            <table id="sample_3" class="display lead-table">
                <thead>
                    <tr class="top-header">
                        <th></th>
                        <th><input type="text" name="customername" value=""></th>
                        <th><input type="text" name="productname" value=""></th>
                        <th><input type="text" name="finaccno" value=""></th>
                        <?php if(!isset($status)){?>
                         <th>
                            <?php
                                $lead_status = $this->config->item('lead_status');
                                $options1['']='Select Status';
                                foreach ($lead_status as $key => $value) {
                                    $options1[$value] = $value;
                                }
                                echo form_dropdown('status', $options1 ,'',array());
                            ?>
                        </th>
                        <?php }?>
                        <?php if($type == 'assigned'){?>
                        <th><input type="text" name="conversiondate" value=""></th>
                        <?php }?>
                        <th>
                            <?php
                                $lead_type = $this->config->item('lead_type');
                                $options2['']='Select Type';
                                foreach ($lead_type as $key => $value) {
                                    $options2[$value] = $value;
                                }
                                echo form_dropdown('status', $options2 ,'',array());
                            ?>
                        </th>
                        <?php if($type == 'assigned'){?>
                        <!-- <th><input type="text" name="conversiondate" value=""></th> -->
                        <?php }?>
                        <th>
                            <?php
                                if($lead_source){
                                    $options3['']='Select Source';
                                    foreach ($lead_source as $key => $value) {
                                        $options3[$value] = $value;
                                    }
                                    echo form_dropdown('status', $options3 ,'',array());
                                }
                            ?>
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>
                            Sr. No.
                        </th>
                        <th>
                            Customer Name
                        </th>
                        <th>
                            Product Name
                        </th>
                        <th>
                            Elapsed Days
                        </th>
                        <?php if(!isset($status)){?>
                        <th>
                            Status
                        </th>
                        <?php }?>
                        <?php if($type == 'assigned'){?>
                        <th>
                            Followup date
                        </th>
                        <?php }?>
                        <th>
                            Lead Identified As
                        </th>
                        <?php if($type == 'assigned'){?>
                        <!-- <th>
                            Intrested Other Product
                        </th> -->
                        <?php }?>
                         <th>
                            Lead Source
                        </th>
                       <th>
                            Details
                        </th>
                    </tr>
                </thead>
                    <tbody>
                    <?php 
                        if($leads){
                        $i = 0;
                        foreach ($leads as $key => $value) {
                    ?>  
                        <tr>
                            <td>
                                 <?php echo ++$i;?>
                            </td>
                            <td>
                                 <?php echo ucwords(strtolower($value['customer_name']));?>
                            </td>
                            <td>
                                 <?php echo ucwords($value['title']);?>
                            </td>
                            <td>
                                 <?php 
                                    $created_date = explode(' ',$value['created_on']);
                                    $now = date_create(date('Y-m-d')); // or your date as well
                                    $generated_date = date_create($created_date[0]);
                                    $datediff = date_diff($now,$generated_date);
                                    echo $datediff->format("%a days");
                                ?>
                            </td>
                            <?php if(!isset($status)){?>
                            <td>
                                 <?php echo ucwords($lead_status[$value['status']]);?>
                            </td>
                            <?php }?>
                            <?php if($type == 'assigned'){?>
                            <td>
                                 <?php echo isset($value['remind_on']) ? date('d-m-Y',strtotime($value['remind_on'])) : '';?>
                            </td>
                            <?php }?>
                            <td>
                                 <?php echo !empty($value['lead_identification']) ? ucwords($lead_type[$value['lead_identification']]) : '';?>
                            </td>
                            <?php if($type == 'assigned'){?>
                            <!-- <td>
                                 <?php echo isset($value['interested_product_title']) ? ucwords($value['interested_product_title']) : 'NA';?>
                            </td> -->
                            <?php }?>
                            <td>
                                 <?php echo ucwords($value['lead_source']);?>
                            </td>
                            <td>
                                <?php if(isset($status)){?>
                                    <a href="<?php echo site_url('leads/details/'.$type.'/'.$till.'/'.encode_id($value['id']).'/'.$status)?>">View</a>
                                <?php }else{
                                ?>
                                    <a href="<?php echo site_url('leads/details/'.$type.'/'.$till.'/'.encode_id($value['id']))?>">View</a>
                                <?php }?>
                            </td>
                        </tr>   
                    <?php   
                        }
                    }?>
                </tbody>
            </table>
        </div>
    </div>
    <span class="bg-bottom"></span>
</div>
<!-- END LEADS-->
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function() { 
        var table = $('#sample_3');
        var columns = [];

        var type = "<?php echo $type ?>";
        switch(type) {
            case 'generated':
                columns = [6];
                break;
            case 'converted':
                columns = [6];
                break;
            case 'assigned':
                columns = [8];
                break;
        }
        
        //Initialize datatable configuration
        initTable(table,columns);
    });
</script>
