<?php

function setCowClass($viewId) {

    $reponseCowSingle = $database->prepare("SELECT * FROM cows WHERE id = $viewId");
    $reponseCowSingle->execute();
    $result = $reponseCowSingle->fetch();
  
    $_1 = new Cow();
    $_1->setId($result['id']);
    $_1->setName($result['name']);
    $_1->setOwner($result['owner_id']);
    $_1->setGender($result['gender']);
    $_1->setType($result['type']);
    $_1->setRace($result['race']);
    $_1->setBirthdate($result['birth_date']);
    $_1->setMotherId($result['mother_id']);
    
    echo "<script>console.log('Interieur fonction' );</script>";
  }

  echo "<script>console.log('Exterieur fonction' );</script>";


  ?>