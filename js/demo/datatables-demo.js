// Call the dataTables jQuery plugin
$(document).ready(function() {
  $('#dataTable').dataTable( {
    "columnDefs": [
      { "orderable": false, "targets": 1 }
    ]
  } );
});
