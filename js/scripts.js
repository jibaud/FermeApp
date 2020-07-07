$(document).ready(function() {
  // Pour griser les champs de formulaire en fonction de la possibilité d'être enceinte
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

  $('#archiveListTable').DataTable( {
    "language": {
        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    "columnDefs": [
      { "orderable": false, "targets": 5 }
    ]
  } );

  // Pour éditer la ligne cliquée dans le tableau
  $(".selectIdButtonArchive").click(function(e) { 
    document.getElementById("selectedIdToArchive").value = this.id;
  });
  $(".selectIdButtonRestaure").click(function(e) { 
    document.getElementById("selectedIdToRestaure").value = this.id;
  });
  $(".selectIdButtonDelete").click(function(e) { 
    document.getElementById("selectedIdToDelete").value = this.id;
  });

  // Tooltip initializer
   $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  // Ouvre automatiquement la modal addCowModal depuis l'URL
  if(window.location.href.indexOf('#addCowModal') != -1) {
    $('#addCowModal').modal('show');
  }

  // Change URL quand on ouvre la modal addCowModal
  $("#addNewButton").click(function(e) { 
    history.pushState({key: 'milkow'}, 'Nouvel élément', '#addCowModal');
  });

  // Change URL quand on ferme la modal addCowModal
  $('#addCowModal').on('hide.bs.modal', function (e) {
    history.pushState({key: 'milkow'}, '', 'cows-manager');
  })

  // Desactive le touche entrer pr valider le formulaire
  $('.noEnterKey').on('keyup keypress', function(e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) { 
      e.preventDefault();
      return false;
    }
  });
  
}); // End of document ready function


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

// Vérifie si l'animal à plus d'1 an
function checkAge(){
  var dob = $('#birthdate').val();
  if(dob != ''){
      var str=dob.split('/');    
      var firstdate=new Date(str[2],str[1],str[0]);
      var today = new Date();        
      var dayDiff = Math.ceil(today.getTime() - firstdate.getTime()) / (1000 * 60 * 60 * 24 * 365.25);
      var age = parseInt(dayDiff);
      $('#age').html(age+' years old');
      if (age >= 1) {
        return 'génisse';
      } else {
        return 'veau';
      }
  }
}

// Desactive les champs de formulaire en fonction de la possibilité d'être enceinte
function noPregnant() {
  var gender = document.getElementById("gender");
  var type = checkAge();
  var checkBox = document.getElementById("ispregnant");
  var hidden = document.querySelectorAll('.hiddenifmale');

  if ((gender.options[gender.selectedIndex].value != "femelle") | (type != "génisse")) {
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