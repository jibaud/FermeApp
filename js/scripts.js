$(document).ready(function() {
  noPregnant();

  $('#cowListTable').DataTable( {
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    fixedHeader: true
  } );

  $("td button").click(function(e) 
   { 
    document.getElementById("selectedId").value = this.id;
   });

});

// PREGNANT DISABLER //

function isPregnantChecked() {
  var checkBox = document.getElementById("ispregnant");
  var hidden = document.getElementById("pregnantsince");

  if (checkBox.checked == true){
    hidden.disabled = false;
  } else {
    hidden.disabled = true;
  }
}

function noPregnant() {
  var gender = document.getElementById("gender");
  var type = document.getElementById("type");
  var checkBox = document.getElementById("ispregnant");
  var hidden = document.querySelectorAll('.hiddenifmale');


  if ((gender.options[gender.selectedIndex].value == "male") | (type.options[type.selectedIndex].value != "vache")) {
    hidden.forEach(element => {
      element.disabled = true;
    });
  } else {
    hidden.forEach(element => {
      element.disabled = false;
      isPregnantChecked();
    });
  }
}