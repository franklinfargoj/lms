<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PAGE CONTENT INNER -->
    <div class="row">
        <div class="col-md-12">
             <!-- BEGIN PRODUCT CATEGOEY-->
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <!-- <i class="fa fa-cogs font-green-sharp"></i> -->
                            <span class="caption-subject font-green-sharp bold"><?php echo $title;?></span>
                        </div>
                        <div class="tools">
                            <a href="<?php echo site_url('dashboard');?>" class="btn btn-sm green"><i class="fa fa-plus"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                       <div class="row">
                            <div class="col-md-12">
                                <?php 
                                $attributes = array(
                                    'role' => 'form',
                                    'id' => 'add_form',
                                    'autocomplete' => 'off'
                                    );
                                echo form_open(site_url().'leads/update_lead_status', $attributes);
                                ?>
                                <div class="portlet yellow-crusta box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i> Details
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Customer Name
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php
                                                    $data = array(
                                                        'lead_id' => encode_id($leads[0]['id'])
                                                    );
                                                    echo form_hidden($data);
                                                ?>
                                                <?php echo $leads[0]['customer_name'];?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Customer Phone no.
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php echo $leads[0]['contact_no'];?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Product Name
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php echo $leads[0]['title'];?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Lead As
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php echo $leads[0]['lead_identification'];?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                 Lead Source
                                            </div>
                                            <div class="col-md-7 value">
                                                 <?php echo $leads[0]['lead_source'];?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-md-5 name">
                                                Lead Status
                                            </div>
                                            <div class="col-md-7 value">
                                                <?php
                                                    //$options = array_merge(array('' => 'Select'),$categorylist);
                                                    $options = $lead_status;
                                                    $js = array(
                                                            'id'       => 'lead_status',
                                                            'class'    => 'form-control'    
                                                            /*'onChange' => 'some_function();'*/
                                                    );

                                                    //$shirts_on_sale = array('small', 'large');
                                                    echo form_dropdown('lead_status', $options , $leads[0]['status'],$js);
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions right">
                                        <button type="reset" class="btn default">Reset</button>
                                        <button type="submit" class="btn green">Submit</button>
                                    </div>
                                </div>
                                <?php 
                                    echo form_close();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- END PRODUCT CATEGOEY-->
        </div>
    </div>
    <!-- END PAGE CONTENT INNER -->

    
    