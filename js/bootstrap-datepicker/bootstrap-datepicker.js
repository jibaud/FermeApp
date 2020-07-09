// Options for Bootstrap-Datepicker plugin
$(document).ready(function() {
    $('.input-group.date').datepicker({
        format: "dd/mm/yyyy",
        endDate: "0d",
        language: "fr",
        orientation: "auto top",
        autoclose: false, //To avoid to change URL of the modal
        todayHighlight: true
    });
  });
  