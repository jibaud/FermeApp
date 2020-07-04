$(document).ready(function() {
  // Pour griser les champs de formulaire
  noPregnant();

  // Options du tableau
  $('#cowListTable').DataTable( {
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    "columnDefs": [
      { "orderable": false, "targets": 7 }
    ],
    fixedHeader: true
  } );

  // Pour éditer la ligne cliquée dans le tableau
  $("button.deleteButton").click(function(e) 
   { 
    document.getElementById("selectedId").value = this.id;
   });

   $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  $('#tooltipFor3929').tooltip({ 
    title: 'coucou'
   })

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


  if ((gender.options[gender.selectedIndex].value != "femelle") | (type.options[type.selectedIndex].value != "vache")) {
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