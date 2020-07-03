<?php

function daysBetween($date1, $date2) {
  date_default_timezone_set('Europe/Paris');

  $dateTiret1 = str_replace('/', '-', $date1);
  $dateTiret2 = str_replace('/', '-', $date2);
  $dateTime1 = new DateTime($dateTiret1);
  $dateTime2 = new DateTime($dateTiret2);
  $diff = $dateTime2->diff($dateTime1)->format("%a");
  return $diff;
}

function daysSince($date) {
  date_default_timezone_set('Europe/Paris');

  $today = date('d-m-Y');
  $dateTiret = str_replace('/', '-', $date);
  $dateTime1 = new DateTime($today);
  $dateTime2 = new DateTime($dateTiret);
  $diff = $dateTime1->diff($dateTime2)->format("%a");
  return $diff;
}

function calculeAge($date, $option) {
  date_default_timezone_set('Europe/Paris');

  $today = date('d-m-Y');
  $dateTiret = str_replace('/', '-', $date);
  $dateTime1 = new DateTime($dateTiret);
  $dateTime2 = new DateTime($today);
  $diff = $dateTime1->diff($dateTime2);
  
  if ($option == 'full') {
    return $diff->y.' an(s), '.$diff->m.' mois, '.$diff->d.' jour(s)';
  } else if ($option == 'short') {
    if ($diff->y < 1) {
      if ($diff->m < 1) {
        if ($diff->d > 1) {
          return $diff->d.' jours';
        } else {
          return $diff->d.' jour';
        }
      } else {
      return $diff->m.' mois';
      }
    } else {
      if ($diff->y > 1) {
        return $diff->y.' ans';
      } else {
        return $diff->y.' an';
      }
    }
  } else {
      return 'Option \"full\" ou \"short\" manquante.';
  }

}

?>