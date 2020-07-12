<?php

function daysBetween($date1, $date2)
{
  date_default_timezone_set('Europe/Paris');

  $dateTiret1 = str_replace('/', '-', $date1);
  $dateTiret2 = str_replace('/', '-', $date2);
  $dateTime1 = new DateTime($dateTiret1);
  $dateTime2 = new DateTime($dateTiret2);
  $diff = $dateTime2->diff($dateTime1)->format("%a");
  return $diff;
}

function daysSince($date)
{
  date_default_timezone_set('Europe/Paris');

  $today = date('d-m-Y');
  $dateTiret = str_replace('/', '-', $date);
  $dateTime1 = new DateTime($today);
  $dateTime2 = new DateTime($dateTiret);
  $diff = $dateTime1->diff($dateTime2)->format("%a");
  return $diff;
}

function calculeAge($date, $option)
{
  date_default_timezone_set('Europe/Paris');

  $today = date('d-m-Y');
  $dateTiret = str_replace('/', '-', $date);
  $dateTime1 = new DateTime($dateTiret);
  $dateTime2 = new DateTime($today);
  $diff = $dateTime1->diff($dateTime2);

  if ($diff->y > 1) {
    $ys = 's'; // Rajoute un s à "an" si pluriel
  }
  if ($diff->d > 1) {
    $ds = 's'; // Rajoute un s à "jour" si pluriel
  }

  if ($option == 'full') {
    return $diff->y . ' an' . $ys . ', ' . $diff->m . ' mois et ' . $diff->d . ' jour' . $ds;
  } else if ($option == 'short') {
    if ($diff->y < 1) {
      if ($diff->m < 1) {
        return $diff->d . ' jour' . $ds;
      } else {
        return $diff->m . ' mois';
      }
    } else {
      return $diff->y . ' an' . $ys;
    }
  } else {
    return 'Option \"full\" ou \"short\" manquante.';
  }
}

function calculeAgeDead($birth, $death, $option)
{
  date_default_timezone_set('Europe/Paris');

  $dateTiret1 = str_replace('/', '-', $birth);
  $dateTiret2 = str_replace('/', '-', $death);
  $dateTime1 = new DateTime($dateTiret1);
  $dateTime2 = new DateTime($dateTiret2);
  $diff = $dateTime1->diff($dateTime2);

  if ($diff->y > 1) {
    $ys = 's'; // Rajoute un s à "an" si pluriel
  }
  if ($diff->d > 1) {
    $ds = 's'; // Rajoute un s à "jour" si pluriel
  }

  if ($option == 'full') {
    return $diff->y . ' an' . $ys . ', ' . $diff->m . ' mois et ' . $diff->d . ' jour' . $ds;
  } else if ($option == 'short') {
    if ($diff->y < 1) {
      if ($diff->m < 1) {
        return $diff->d . ' jour' . $ds;
      } else {
        return $diff->m . ' mois';
      }
    } else {
      return $diff->y . ' an' . $ys;
    }
  } else {
    return 'Option \"full\" ou \"short\" manquante.';
  }
}

// Convertit dd/mm/yyyy en yyyy-mm-dd
function FRtoSQLdate($date)
{
  $format_sql = implode('-', array_reverse(explode('/', $date)));
  return $format_sql;
}

// Convertit yyyy-mm-dd en dd/mm/yyyy
function SQLtoFRdate($date){
  $format_fr = implode('/', array_reverse(explode('-', $date)));
  return $format_fr;
}

function futureDate($date, $months)
{
  $dateDepart = str_replace('/', '-', $date);
  $dateDepartTimestamp = strtotime($dateDepart);
  $dateFin = date('d-m-Y', strtotime('+' . $months . ' month', $dateDepartTimestamp));
  return str_replace('-', '/', $dateFin);
}

function futureDateDay($date, $day)
{
  $dateDepart = str_replace('/', '-', $date);
  $dateDepartTimestamp = strtotime($dateDepart);
  $dateFin = date('d-m-Y', strtotime('+' . $day . ' day', $dateDepartTimestamp));
  return str_replace('-', '/', $dateFin);
}

function calculeType($date)
{
  date_default_timezone_set('Europe/Paris');

  $today = date('d-m-Y');
  $dateTiret = str_replace('/', '-', $date);
  $dateTime1 = new DateTime($dateTiret);
  $dateTime2 = new DateTime($today);
  $diff = $dateTime1->diff($dateTime2);

  if ($diff->m < 12 && $diff->y < 1) {
    return 'veau';
  } else {
    return 'génisse';
  }
}


// Compare si $date1 est plus grand que $date2
 function compareDate($date1, $date2){
  $date1numeric = explode("/", $date1); 
  $date2numeric = explode("/", $date2); 
          
  $date1reverse = $date1numeric[2].$date1numeric[1].$date1numeric[0]; 
  $date2reverse = $date2numeric[2].$date2numeric[1].$date2numeric[0]; 
    
  if ($date1reverse>$date2reverse)
  {
  return true;
  }
  else
  {
  return false;
  }
 }
?>