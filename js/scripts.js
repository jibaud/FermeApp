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
  $(".selectIdButtonDelete").click(function (e) {
    document.getElementById("selectedIdToDelete").value = this.id;
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

  // GESTATION LIST EDIT 
  //Bouton modifier dans gestation row edit
  $('.editGestRow').click(function (e) {
    e.preventDefault();

    resetGestRow();

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
    document.getElementById("deletedRowState").value = $(this).parents('.rowGestList').find('.g_state_origin').val(); // Pour la suppression
    document.getElementById("inputGestEnd").value = $(this).parents('.rowGestList').find('.g_end_edit').val();
    document.getElementById("inputGestNote").value = $(this).parents('.rowGestList').find('.g_note_edit').val();

  });

  // Bouton annuler dans gestation row edit
  $('.cancelGestEdit').click(function (e) {
    e.preventDefault();
    $(this).parent().tooltip('hide');

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

}); // End of document ready function

function resetGestRow() {
  $('.displayEdit').hide();
  $('.displayRead').show();

  document.getElementById("inputGestId").value = '';
  document.getElementById("deletedNumber").value = '';
  document.getElementById("inputGestStart").value = '';
  document.getElementById("inputGestState").value = '';
  document.getElementById("inputGestEnd").value = '';
  document.getElementById("inputGestNote").value = '';
};


// GESTATION EDIT FORM 
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


function showSnackBar($message, $color = "primary"){
  let snackbar  = new SnackBar;
    snackbar.make("message",
  [
    $message,
    null,
    "bottom",
    "center",
    $color
  ], 400000);
}