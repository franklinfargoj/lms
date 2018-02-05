<?php
//pe($leads);die;
    $parameter = '';
    $source = 'empty';
    $lead_type = $this->config->item('lead_type');
    $all_source = $this->config->item('lead_source');
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
<?php
$param1 = isset($type) ? $type.'/' : '';
$param2 = isset($till) ? $till.'/' : '';
$param3 = isset($status) ? $status.'/' : '';
$param4 = isset($lead_source) ? $lead_source.'/' : '';
$param5 = isset($param) ? encode_id($param).'/' : '';
?>

<div class="page-content">
    <span class="bg-top"></span>
    <div class="inner-content">
        <div class="container">
            <?php if(in_array($this->session->userdata('admin_type'),array('BM'))){?>
            <p style="font-style:italic;">All leads assigned to your branch employees in last 1 year</p>
            <?php }else{?>
                <p style="font-style:italic;">All leads assigned to you in last 1 year</p>
            <?php }?>
            <div class="lead-top clearfix">
                <div class="float-left">
                    <span class="total-lead">
                        Total Leads
                    </span>
                    <span class="lead-num"> : <?php echo count($leads);?></span>
                </div>
                <?php if(count($leads) >0){?>
                <div class="float-right">
                <a href="<?php echo base_url('leads/export_excel_listing/'.$param1.$param2.$param3.$param4.$param5);?>">
                <img src="<?php echo base_url().ASSETS;?>images/excel-btn.png" alt="btn">
                </a>
                <?php } ?>
            </div>


            <table id="sample_3" class="display lead-table">
                <thead>
                    <tr class="top-header">
                        <th></th>
                        <th><input type="text" name="customername" placeholder="Customer Name" value=""></th>
                        <th><input type="text" name="productname" placeholder="Product Name" value=""></th>
                        <th><input type="text" name="finaccno" placeholder="Day" value="" size="2"></th>
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
                        <th><input type="text" name="conversiondate" placeholder="Followup Date" value=""></th>
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
                                if($lead_sources){
                                    $options3['']='Select Source';
                                    foreach ($lead_sources as $key => $value) {
                                        $options3[$all_source[$value]] = ucwords(strtolower($all_source[$value]));
                                    }
                                    echo form_dropdown('status', $options3 ,'',array());
                                }
                            ?>
                        </th>
                        <th></th>
                    </tr>
                    <tr>

                        <th style="text-align:left">
                            Sr.No
                        </th>
                        <th style="text-align:left">
                            Customer Name
                        </th>
                        <th style="text-align:left">
                            Product Name
                        </th>
                        <th style="text-align:center">
                            Elapsed Days
                        </th>
                        <?php if(!isset($status)){?>
                        <th style="text-align:left">
                            Status
                        </th>
                        <?php }?>
                        <?php if($type == 'assigned'){?>
                        <th style="text-align:center">
                            Followup Date
                        </th>
                        <?php }?>
                        <th style="text-align:left">
                            Lead Identified As
                        </th>
                        <?php if($type == 'assigned'){?>
                        <?php }?>
                         <th style="text-align:left">
                            Lead Source
                        </th>
                       <th style="text-align:left; width:140px!important; ">
                            Details
                        </th>
                    </tr>
                </thead>
                    <tbody>
                    <?php
                        if($leads){
                        $i = 1;
                        foreach ($leads as $key => $value) {
                    ?>
                        <tr>

                            <td>
                                 <?php echo $i;?>
                               
                            </td>
                            <td>
                              <?php $admin = $this->session->userdata('admin_type');
                                if($admin == 'BM' && in_array($value['status'],array('AO'))){?>
                                    <img src="<?php echo base_url().ASSETS;?>images/like.gif" alt="logo" class="like">
                                <p class="custname"><?php echo ucwords($value['customer_name']);?></p>

                                <?php }
                                elseif($admin == 'BM' && in_array($value['status'],array('NI'))){?>
                                <img src="<?php echo base_url().ASSETS;?>images/dislike.gif" alt="logo" class="like">
                                <p class="custname"><?php echo ucwords($value['customer_name']);?></p>
                               
                                <?php }
                                elseif($admin == 'BM' && $value['prod_cat']=='Fee Income' && in_array($value['status'],array('DC'))){?>
                                    <img src="<?php echo base_url().ASSETS;?>images/like.gif" alt="logo" class="like">
                                    <p class="custname"><?php echo ucwords($value['customer_name']);?></p>
                                
                                  <?php }else{?>
                                 <?php echo ucwords($value['customer_name']);
                                 }?>
                            </td>
                            <td>
                                 <?php echo ucwords($value['title']);?>
                            </td>
                            <td style="text-align:center">
                                 <?php
                                  echo $value['elapsed_day'];
                                ?>
                            </td>
                            <?php if(!isset($status)){?>
                            <td>
                                 <?php echo ucwords($lead_status[$value['status']]);?>
                            </td>
                            <?php }?>
                            <?php if($type == 'assigned'){?>
                            <td style="text-align:center">
                                 <?php echo isset($value['remind_on']) && $value['status'] == "FU" ? date('d-m-Y',strtotime($value['remind_on'])) : '';?>
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
                                 <?php echo ucwords(strtolower($all_source[$value['lead_source']]));?>
                            </td>
                            <td>
                                <?php
                                    if(isset($status) && !empty($status)){
                                        $parameter .= '/'.$status;
                                    }
                                    if(isset($param) && !empty($param)){
                                        $parameter .= '/'.encode_id($param);
                                    }
                                    if(isset($lead_source) && !empty($lead_source)){
                                        $parameter .= '/'.$lead_source;
                                        $source = $lead_source;
                                    }
                                ?>
                                <a href="<?php echo site_url('leads/details/'.$type.'/'.$till.'/'.encode_id($value['id']).$parameter)?>">View</a>

                                    <span>|</span> <a href="<?php echo site_url('leads/lead_life_cycle/'.encode_id($value['id']))?>">Life Cycle</a>

                            </td>
                        </tr>
                    <?php
                       $i++; }
                    }?>
                </tbody>
            </table>

        </div>
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
        var lead_source = "<?php echo $source ?>";
        switch(type) {
            case 'generated':
                columns = [0,6];
                break;
            case 'assigned':
            if(lead_source == 'empty'){
                columns = [0,8];
            }else{
                columns = [0,6];
            }
            break;
        }
        
        //Initialize datatable configuration
        initTable(table,columns);
    });
    

    
</script>
