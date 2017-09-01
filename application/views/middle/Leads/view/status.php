
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url().ASSETS;?>css/jquery.dataTables.min.css" rel="stylesheet">
<!-- END PAGE LEVEL STYLES -->
<?php $status = $this->config->item('lead_status');
      $status_ytd = array(
        'NC' => 'not_contacted',
        'DC' => 'documents_collected',
        'AO' => 'account_opened',
        'NI' =>'drop_not_interested',
        'CBC' =>'can_not_be_contacted',
        'FU' => 'follow_up',
        'Converted' =>'converted',
        'Closed' => 'closed'
        );
      $status_mtd = array(
        'NC' =>'month_not_contacted',
        'DC' =>'month_documents_collected',
        'AO' =>'month_account_opened',
        'NI' =>'month_drop_not_interested',
        'CBC' =>'month_can_not_be_contacted',
        'FU' =>'month_follow_up',
        'Converted' => 'month_converted',
        'Closed' =>'month_closed'
      );


      $title = 'MY GENERATED LEAD';
      if($this->session->userdata('admin_type') == 'BM'){
          $title = $employee_name.' GENERATED LEAD';
          $emp_id = 'Employee Id: '.$employee_id;
      }
      if($this->session->userdata('admin_type') == 'ZM'){
          $title = 'Branch Id: '.$branch_id.' GENERATED LEAD';
      }
?>
<div class="page-title">
    <div class="container clearfix">
        <h3 class="text-center"><?php echo $title;?></h3>
        <?php echo isset($emp_id) ? '<h4 class="text-center">'. $emp_id .'</h4>' : "";?>
    </div>
</div>
<div class="page-content">
    <div class="container">
        <table class="upload-table" id="sample_3">
            <thead>
            <tr class="top-header">
                <th></th>
                <th>
                    <?php
                        $options['']='Select Status';
                        foreach ($status as $key => $value) {
                            $options[$value] = $value;
                        }
                        echo form_dropdown('status', $options ,'',array());
                    ?>
                </th>
                <th><input type="text" name="customername" placeholder="Search MTD"></th>
                <th><input type="text" name="customername" placeholder="Search YTD"></th>
            </tr>
            <tr>
                <th>Sr No</th>
                <th>Status</th>
                <th>MTD</th>
                <th>YTD</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if(!empty($status)){
                $i = 0;
                foreach ($status as $key => $value){ 
                    $mtd = $status_mtd[$key];
                    $ytd = $status_ytd[$key];
                ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td><?php echo $value; ?></td>
                    <td><a href="<?php echo site_url('leads/leads_list/generated/mtd/'.$key);?>" ><?php echo $$mtd; ?></a></td>
                    <td><a href="<?php echo site_url('leads/leads_list/generated/ytd/'.$key);?>" ><?php echo $$ytd; ?></a></td>
                </tr>
            <?php
            $i++; 
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="<?php echo base_url().ASSETS;?>js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url().ASSETS;?>js/config.datatable.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var table = $('#sample_3');
        var columns = [];

        //Initialize datatable configuration
        initTable(table,columns);

    });

</script>
