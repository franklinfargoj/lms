<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/Scroller/css/dataTables.scroller.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/extensions/ColReorder/css/dataTables.colReorder.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->

<!-- BEGIN PRODUCT -->
<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-green-sharp bold"><?php echo $title;?></span>
		</div>
		<div class="tools">
			<a href="<?php echo site_url('dashboard');?>" class="btn btn-sm green"><i class="fa fa-plus"></i>Back
			</a>
		</div>
	</div>
	<div class="portlet-body">
		<table class="table table-striped table-bordered table-hover" id="sample_3">
		<thead>
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
                Lead as (H/W/C)
            </th>
            <th>
                Lead Source
            </th>
             <th>
                Elapsed Days
            </th>
            <?php if($type == 'assigned'){?>
            <th>
                Interested in product
            </th>
            <?php }?>
           <th>
				Details
			</th>
		</tr>
		</thead>
		<tbody>
			<?php if($leads){
				$i = 0;
				foreach ($leads as $key => $value) {
			?>	
				<tr>
					<td>
						 <?php echo ++$i;?>
					</td>
					<td>
						 <?php echo $value['customer_name'];?>
					</td>
					<td>
						 <?php echo $value['title'];?>
					</td>
                    <td>
                         <?php echo $value['lead_identification'];?>
                    </td>
                    <td>
                         <?php echo $value['lead_source'];?>
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
                    <?php if($type == 'assigned'){?>
                    <td>
                         <?php echo isset($value['interested_product_title']) ? $value['interested_product_title'] : 'NA';?>
                    </td>
                    <?php }?>
                    <td>
                        <a class="btn btn-sm grey-cascade" href="<?php echo site_url('leads/details/'.$type.'/'.$till.'/'.encode_id($value['id']))?>">
                            <i class="fa fa-link"></i> View
                        </a> 
                    </td>
				</tr>	
			<?php	
				}
			}?>
		</tbody>
		</table>
	</div>
</div>
<!-- END PRODUCT CATEGOEY-->

<script src="<?php echo base_url();?>assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

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
            //this.insertBefore(nCloneTh, this.childNodes[0]);
        });

        table.find('tbody tr').each(function () {
            //this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
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
                "targets": [3,4]
            }],
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
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
                $(this).addClass("row-details-open").removeClass("row-details-close");
                oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
            }
        });
    }
</script>
<script>
	jQuery(document).ready(function() { 
		initTable3();

	   $('.delete').click(function(){
	   		var url = $(this).data('url');
	   		bootbox.confirm("Are you sure want to delete?", function(result) {
               if(result == true){
               	window.location.href = url;
               }
            }); 
        });
	});
</script>