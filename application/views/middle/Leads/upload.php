<?php
/**
 * Created by PhpStorm.
 * User: webwerks1
 * Date: 15/8/17
 * Time: 6:32 PM
 */
$form_attributes = array('id' => 'upload_lead', 'method' => 'POST');
$data_input = array('id' => 'file', 'name'=>'filename', 'method' => 'POST' ,'type' => 'file');

$source_options[''] = 'Select Lead Source';
$source_options['Tie Ups'] = 'Tie Ups';
$source_options['Enquiry'] = 'Enquiry';
$source_options['Analytics'] = 'Analytics';
$class = 'class="form-control"';
$data_submit = array(
    'name' => 'Submit',
    'id' => 'Submit',
    'type' => 'Submit',
    'content' => 'Submit',
    'class' => 'btn green',
    'value' => 'Submit'
);
?>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!-- END PAGE LEVEL PLUGINS -->

<div class="row">
    <div class="col-md-6 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light">
            <div class="portlet-body form">
                <?php
                $url = base_url('leads/upload');
                echo form_open_multipart($url, $form_attributes);
                ?>
                <?php echo $this->session->flashdata('message'); ?>
                <div class="form-body">
                    <div class="form-group">
                        <label>Lead Source</label>
                        <?php echo form_dropdown('lead_source', $source_options,'', $class) ?>
                    </div>
                    <div class="form-group">
                        <label>Upload</label>
                        <div class="input-group">
						<span class="input-group-addon">
                            <?php echo form_input($data_input);?>
						</span>
                        </div>
                        <label id="file-error" class="error" style="color: #A94442" for="file"></label>
                    </div>
                    <div class="form-group">
                        <span>*Please Upload a file with extension xls or xlsx</span>
                    </div>
                </div>
                <div class="form-actions">
                    <?php echo form_button($data_submit) ?>
                </div>
                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>

<div>
    <table class="table table-striped table-bordered table-hover" id="sample_3">
        <thead>
        <tr>
            <th>
                Sr. No.
            </th>
            <th>
                File Name
            </th>
            <th>
                Uploaded On
            </th>
            <th>
                Status
            </th>
            <th>
                Download
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($uploaded_logs)){
            $i = 0;
            foreach ($uploaded_logs as $key => $value) {
                ?>
                <tr>
                    <td>
                        <?php echo ++$i;?>
                    </td>
                    <td>
                        <?php echo $value['file_name'];?>
                    </td>
                    <td>
                        <?php echo $value['created_time'];?>
                    </td>
                    <td>
                        <?php echo $value['status'];?>
                    </td>
                    <td>
                        <?php if($value['status'] == 'failed'){ ?>
                        <a href="<?php echo base_url('uploads/errorlog/'.$value['file_name']); ?>">Download</a>
                        <?php }else{?>
                            <a href="<?php echo base_url('uploads/'.$value['file_name']); ?>">Download</a>
                        <?php }?>
                    </td>
                </tr>
                <?php
            }
        }?>
        </tbody>
    </table>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script>
    $('#upload_lead').validate({

        rules:{
            lead_source:{
                required:true
            },
            filename:{
                required:true
            }
        },messages:{
            lead_source:{
                required:'Please select lead source.'
            },
            filename:{
                required:'Please upload a file.'
            }
        }
    });
</script>

<script type="text/javascript">
    var initTable3 = function () {
        var table = $('#sample_3');

        /* Formatting function for row details */
        function fnFormatDetails(oTable, nTr) {
            var aData = oTable.fnGetData(nTr);
            var sOut = '<table>';
            sOut += '<tr><td>Platform(s):</td><td>' + aData[2] + '</td></tr>';
            sOut += '<tr><td>Engine version:</td><td>' + aData[3] + '</td></tr>';
            sOut += '<tr><td>CSS grade:</td><td>' + aData[4] + '</td></tr>';
            sOut += '<tr><td>Others:</td><td>Could provide a link here</td></tr>';
            sOut += '</table>';

            return sOut;
        }

        /*
         * Insert a 'details' column to the table
         */
        var nCloneTh = document.createElement('th');
        nCloneTh.className = "table-checkbox";

        var nCloneTd = document.createElement('td');
        nCloneTd.innerHTML = '<span class="row-details row-details-close"></span>';

        table.find('thead tr').each(function () {
//            this.insertBefore(nCloneTh, this.childNodes[0]);
        });

        table.find('tbody tr').each(function () {
//            this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
        });

        /*
         * Initialize DataTables, with no sorting on the 'details' column
         */
        var oTable = table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                "lengthMenu": "Show _MENU_ entries",
                "search": "Search:",
                "zeroRecords": "No matching records found"
            },

            "columnDefs": [{
                "orderable": false,
                "targets": [4]
            }],
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [
                [5, 10, 15, -1],
                [5, 10, 15, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,
        });
        var tableWrapper = $('#sample_3_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2(); // initialize select2 dropdown

        /* Add event listener for opening and closing details
         * Note that the indicator for showing which row is open is not controlled by DataTables,
         * rather it is done here
         */
        table.on('click', ' tbody td .row-details', function () {
            var nTr = $(this).parents('tr')[0];
            if (oTable.fnIsOpen(nTr)) {
                /* This row is already open - close it */
                $(this).addClass("row-details-close").removeClass("row-details-open");
                oTable.fnClose(nTr);
            } else {
                /* Open this row */
//                $(this).addClass("row-details-open").removeClass("row-details-close");
//                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }
        });
    }
</script>
<script>
    jQuery(document).ready(function() {
        initTable3();
    });
</script>