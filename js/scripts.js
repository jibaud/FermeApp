$(document).ready(function () {

  // Options du tableau
  $('#cowListTable').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    searching: true,
    ordering: true,
    "columnDefs": [
      { "orderable": false, "targets": 7 }
    ],
    fixedHeader: true
  });

  $('#archiveListTable').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    searching: true,
    ordering: true,
    "columnDefs": [
      { "orderable": false, "targets": 5 }
    ]
  });

  $('#gestTable').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    },
    info: false,
    paging: false,
    searching: false,
    ordering: false
  });

  // Pour éditer la ligne cliquée dans le tableau
  $(".selectIdButtonArchive").click(function (e) {
    document.getElementById("selectedIdToArchive").value = this.id;
  });
  $(".selectIdButtonRestaure").click(function (e) {
    document.getElementById("selectedIdToRestaure").value = this.id;
  });
  $(".selectIndexButtonDelete").click(function (e) {
    console.log('ok');
    document.getElementById("selectedIndexToDelete").value = this.id;
  });

  // Tooltip initializer
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  // Ouvre automatiquement la modal addCowModal depuis l'URL
  if (window.location.href.indexOf('#addCowModal') != -1) {
    $('#addCowModal').modal('show');
  }

  // Change URL quand on ouvre la modal addCowModal
  $("#addNewButton").click(function (e) {
    history.pushState({ key: 'milkow' }, 'Nouvel élément', '#addCowModal');
  });

  // Change URL quand on ferme la modal addCowModal
  $('#addCowModal').on('hide.bs.modal', function (e) {
    history.pushState({ key: 'milkow' }, '', 'cows-manager');
  })

  // Desactive la touche entrer pr valider le formulaire
  $('.noEnterKey').on('keyup keypress', function (e) {
    var keyCode = e.keyCode || e.which;
    if (keyCode === 13) {
      e.preventDefault();
      return false;
    }
  });

  // Active submit button si changements
  $('.enableSubmitOnChange')
    .each(function () {
      $(this).data('serialized', $(this).serialize())
    })
    .on('change input', function () {
      $(this)
        .find('input:submit, button:submit')
        .prop('disabled', $(this).serialize() == $(this).data('serialized'))
        ;
    })
    .find('input:submit, button:submit')
    .prop('disabled', true)
    ;


  var open = 0
  $('#displayGestForm').click(function (e) {
    e.preventDefault();
    if (open == 0) {
      $('#addGest').slideDown(300);
      $('#displayGestFormIcon').animate(
        { deg: -225 },
        {
          duration: 300,
          step: function (now) {
            $(this).css({ transform: 'rotate(' + now + 'deg)' });
            $(this).removeClass('text-success').addClass('text-danger');
          }
        }
      );
      open = 1;
    } else {
      $('#addGest').slideUp(300);
      $('#displayGestFormIcon').animate(
        { deg: 180 },
        {
          duration: 300,
          step: function (now) {
            $(this).css({ transform: 'rotate(' + now + 'deg)' });
            $(this).removeClass('text-danger').addClass('text-success');
          }
        }
      );
      open = 0;
    }

  });

  var open = 0
  $('#displayTreatForm').click(function (e) {
    e.preventDefault();
    if (open == 0) {
      $('#addTreat').slideDown(300);
      $('#displayTreatFormIcon').animate(
        { deg: -225 },
        {
          duration: 300,
          step: function (now) {
            $(this).css({ transform: 'rotate(' + now + 'deg)' });
            $(this).removeClass('text-success').addClass('text-danger');
          }
        }
      );
      open = 1;
    } else {
      $('#addTreat').slideUp(300);
      $('#displayTreatFormIcon').animate(
        { deg: 180 },
        {
          duration: 300,
          step: function (now) {
            $(this).css({ transform: 'rotate(' + now + 'deg)' });
            $(this).removeClass('text-danger').addClass('text-success');
          }
        }
      );
      open = 0;
    }

  });


  // TOPBAR
  // Change la couleur du badge de notification des gestations
  if ($("#gestNotification").find(".bg-danger").length > 0){ 
    $('#gestNotifBadge').toggleClass(['bg-success', 'bg-danger']);
  } else if ($("#gestNotification").find(".bg-warning").length > 0){
    $('#gestNotifBadge').toggleClass(['bg-success', 'bg-warning']);
  }

  // Change la couleur du badge de notification des traitement
  if ($("#treatNotification").find(".bg-danger").length > 0){ 
    $('#treatNotifBadge').toggleClass(['bg-success', 'bg-danger']);
  } else if ($("#treatNotification").find(".bg-warning").length > 0){
    $('#treatNotifBadge').toggleClass(['bg-success', 'bg-warning']);
  }

  var $numberOfTreat = $('#treatNotification').find('.treatNotifElement').length;
  if($numberOfTreat > 0){
  $('#treatNotifBadge').html($numberOfTreat);
  } else {
    $('#treatNotifBadge').hide();
  }
  $('.numberOfTreat').html($numberOfTreat);

  var $howManyToday = $('#treatNotification').find('.treatNotifElement').find('.bg-danger').length;
  if ($howManyToday > 0) {
    $('#howManyToday').html('('+$howManyToday+' aujourd\'hui)');
  }


  // Dashboard Treat Card - Change la couleur et le texte
  var $numberOfTreatDanger = $('#treatNotification').find('.treatNotifElement').find('.bg-danger').length;
  var $numberOfTreatWarning = $('#treatNotification').find('.treatNotifElement').find('.bg-warning').length;

  if ($numberOfTreatDanger > 0) {
    $('#treatDashboardText').html($numberOfTreatDanger+' aujourd\'hui');
    $('#treatDashboardCard').addClass('border-left-danger');
    $('#treatDashboardTitle').addClass('text-danger');
  } else if ($numberOfTreatWarning > 0) {
    $('#treatDashboardText').html($numberOfTreatWarning+' à venir');
    $('#treatDashboardCard').addClass('border-left-warning');
    $('#treatDashboardTitle').addClass('text-warning');
  } else {
    $('#treatDashboardText').html('Aucun prévu');
    $('#treatDashboardCard').addClass('border-left-success');
    $('#treatDashboardTitle').addClass('text-success');
  }
  



  // GESTATION LIST EDIT 
  //Bouton modifier dans gestation row edit
  $('.editGestRow').click(function (e) {
    e.preventDefault();

    resetGestRow();
    $(this).parents('.rowGestList').addClass('activeGest');

    // Affiche/Cache edition mode
    $(this).parents('.rowGestList').find('.displayRead').hide();
    $(this).parents('.rowGestList').find('.displayEdit').show();

    $(this).parent().tooltip('hide');

    // Rénitialise la valeur à partir du champs origin
    $(this).parents('.rowGestList').find('.g_start_edit').val($(this).parents('.rowGestList').find('.g_start_origin').val());
    $(this).parents('.rowGestList').find('.g_state_edit').val($(this).parents('.rowGestList').find('.g_state_origin').val());
    $(this).parents('.rowGestList').find('.g_end_edit').val($(this).parents('.rowGestList').find('.g_end_origin').val());
    $(this).parents('.rowGestList').find('.g_note_edit').val($(this).parents('.rowGestList').find('.g_note_origin').val());

    // Rempli les inputs cachés
    document.getElementById("inputGestId").value = this.id;
    document.getElementById("deletedNumber").value = this.id;
    document.getElementById("inputGestStart").value = $(this).parents('.rowGestList').find('.g_start_edit').val();
    document.getElementById("inputGestState").value = $(this).parents('.rowGestList').find('.g_state_edit').val();
    document.getElementById("inputGestOriginState").value = $(this).parents('.rowGestList').find('.g_state_origin').val();
    document.getElementById("deletedRowState").value = $(this).parents('.rowGestList').find('.g_state_origin').val(); // Pour la suppression
    document.getElementById("inputGestEnd").value = $(this).parents('.rowGestList').find('.g_end_edit').val();
    document.getElementById("inputGestNote").value = $(this).parents('.rowGestList').find('.g_note_edit').val();


    // Desactive le champs date de fin si en cours est selectionné
    gState = $(this).parents('.rowGestList').find('.g_state_edit');
    gEnd = $(this).parents('.rowGestList').find('.g_end_edit');
    if (gState.val() != 0) {
      gEnd.prop('disabled', false);
    } else {
      gEnd.prop('disabled', true);
      gEnd.val('');
      $('#inputGestEnd').val('');
    }

  });

  // Bouton annuler dans gestation row edit
  $('.cancelGestEdit').click(function (e) {
    e.preventDefault();
    $(this).parent().tooltip('hide');
    $(this).parents('.rowGestList').removeClass('activeGest');

    resetGestRow();
  });

  // Change les valeurs des inputs caché quand on modifie un input dans un row
  $('.g_start_edit').on('change input', function () {
    document.getElementById("inputGestStart").value = $(this).val();
  });
  $('.g_state_edit').on('change input', function () {
    document.getElementById("inputGestState").value = $(this).val();
  });
  $('.g_end_edit').on('change input', function () {
    document.getElementById("inputGestEnd").value = $(this).val();
  });
  $('.g_note_edit').on('change input', function () {
    document.getElementById("inputGestNote").value = $(this).val();
  });

  // Desactive le champs date de fin si en cours est selectionné
  $('.g_state_edit').change(function (e) {
    e.preventDefault();

    gState = $(this).parents('.rowGestList').find('.g_state_edit');
    gEnd = $(this).parents('.rowGestList').find('.g_end_edit');

    if (gState.val() != 0) {
      gEnd.prop('disabled', false);
    } else {
      gEnd.prop('disabled', true);
      gEnd.val('');
      $('#inputGestEnd').val('');
    }
  });


  // TREATS LIST EDIT 
  //Bouton modifier dans treat row edit
  $('.editTreatRow').click(function (e) {
    e.preventDefault();

    resetTreatRow();
    $(this).parents('.rowTreatList').addClass('activeTreat');

    // Affiche/Cache edition mode
    $(this).parents('.rowTreatList').find('.displayRead').hide();
    $(this).parents('.rowTreatList').find('.displayEdit').show();

    $(this).parent().tooltip('hide');

    // Rénitialise la valeur à partir du champs origin
    $(this).parents('.rowTreatList').find('.t_date_edit').val($(this).parents('.rowTreatList').find('.t_date_origin').val());
    $(this).parents('.rowTreatList').find('.t_name_edit').val($(this).parents('.rowTreatList').find('.t_name_origin').val());
    $(this).parents('.rowTreatList').find('.t_repeat_edit').val($(this).parents('.rowTreatList').find('.t_repeat_origin').val());
    $(this).parents('.rowTreatList').find('.t_days_edit').val($(this).parents('.rowTreatList').find('.t_days_origin').val());
    $(this).parents('.rowTreatList').find('.t_dose_edit').val($(this).parents('.rowTreatList').find('.t_dose_origin').val());
    $(this).parents('.rowTreatList').find('.t_note_edit').val($(this).parents('.rowTreatList').find('.t_note_origin').val());

    // Rempli les inputs cachés
    document.getElementById("inputTreatId").value = this.id;
    document.getElementById("deletedTreatNumber").value = this.id;
    document.getElementById("inputTreatDate").value = $(this).parents('.rowTreatList').find('.t_date_edit').val();
    document.getElementById("inputTreatName").value = $(this).parents('.rowTreatList').find('.t_name_edit').val();
    document.getElementById("inputTreatRepeat").value = $(this).parents('.rowTreatList').find('.t_repeat_edit').val();
    document.getElementById("inputTreatDays").value = $(this).parents('.rowTreatList').find('.t_days_edit').val();
    document.getElementById("inputTreatDose").value = $(this).parents('.rowTreatList').find('.t_dose_edit').val();
    document.getElementById("inputTreatNote").value = $(this).parents('.rowTreatList').find('.t_note_edit').val();


    // Desactive le champs date de fin si en cours est selectionné
    gState = $(this).parents('.rowTreatList').find('.g_state_edit');
    gEnd = $(this).parents('.rowTreatList').find('.g_end_edit');
    if (gState.val() != 0) {
      gEnd.prop('disabled', false);
    } else {
      gEnd.prop('disabled', true);
      gEnd.val('');
      $('#inputTreatDays').val('');
    }

  });

  // Bouton annuler dans traitement row edit
  $('.cancelTreatEdit').click(function (e) {
    e.preventDefault();
    $(this).parent().tooltip('hide');
    $(this).parents('.rowTreatList').removeClass('activeTreat');

    resetTreatRow();
  });

  // Change les valeurs des inputs caché quand on modifie un input dans un row
  $('.t_date_edit').on('change input', function () {
    document.getElementById("inputTreatDate").value = $(this).val();
  });
  $('.t_name_edit').on('change input', function () {
    document.getElementById("inputTreatName").value = $(this).val();
  });
  $('.t_repeat_edit').on('change input', function () {
    document.getElementById("inputTreatRepeat").value = $(this).val();
  });
  $('.t_days_edit').on('change input', function () {
    document.getElementById("inputTreatDays").value = $(this).val();
  });
  $('.t_dose_edit').on('change input', function () {
    document.getElementById("inputTreatDose").value = $(this).val();
  });
  $('.t_note_edit').on('change input', function () {
    document.getElementById("inputTreatNote").value = $(this).val();
  });
 

  // Desactive le champs date de fin si en cours est selectionné
  $('.t_repeat_edit').change(function (e) {
    e.preventDefault();

    tRepeat = $(this).parents('.rowTreatList').find('.t_repeat_edit');
    tDays = $(this).parents('.rowTreatList').find('.t_days_edit');

    if (tRepeat.val() != 0) {
      tDays.prop('disabled', false);
    } else {
      tDays.prop('disabled', true);
      tDays.val('');
      $('#inputTreatDays').val('');
    }
  });



  // PROFIL
  // Bouton pour changer la photo de profil
  // Affiche ou non le bouton d'uploard
  $('#modifyImgButton').click(function(e) {
    e.preventDefault();

    $('#profilePicture').toggleClass('d-none');
    $('#imgUploadInput').toggleClass('d-none');
    $('#deleteImgButton').toggleClass('d-none');

    if ($(this).html() == "Modifier") {
      $(this).html("Annuler");
   }
   else {
      $(this).html("Modifier");
   }
  });


}); // End of document ready function






function resetGestRow() {
  $('.displayEdit').hide();
  $('.displayRead').show();
  $('.rowGestList').removeClass('activeGest');

  document.getElementById("inputGestId").value = '';
  document.getElementById("deletedNumber").value = '';
  document.getElementById("inputGestStart").value = '';
  document.getElementById("inputGestState").value = '';
  document.getElementById("inputGestEnd").value = '';
  document.getElementById("inputGestNote").value = '';
};

function resetTreatRow() {
  $('.displayEdit').hide();
  $('.displayRead').show();
  $('.rowTreatList').removeClass('activeTreat');

  document.getElementById("inputTreatId").value = '';
  document.getElementById("deletedTreatNumber").value = '';
  document.getElementById("inputTreatDate").value = '';
  document.getElementById("inputTreatName").value = '';
  document.getElementById("inputTreatRepeat").value = '';
  document.getElementById("inputTreatDays").value = '';
  document.getElementById("inputTreatDose").value = '';
  document.getElementById("inputTreatNote").value = '';
};



// GESTATION ADD FORM - Désactive le champs date de fin si gestation toujours en cours
function gestationState() {
  var gState = document.getElementById("g_state");
  var gEnd = $('#g_end');
  if (gState.value != 0) {
    gEnd.prop('disabled', false);
  } else {
    gEnd.prop('disabled', true);
    gEnd.val('');
  }
};

// TREAT ADD FORM - Désactive le champs days si traitement une seule fois
function treatState() {
  var tRepeat = document.getElementById("t_repeat");
  var tdays = $('#t_days');
  if (tRepeat.value != 0) {
    tdays.prop('disabled', false);
  } else {
    tdays.prop('disabled', true);
    tdays.val('');
  }
};


function showSnackBar($message, $color = "primary") {
  let snackbar = new SnackBar;
  snackbar.make("message",
    [
      $message,
      null,
      "bottom",
      "center",
      $color
    ], 4000);
}