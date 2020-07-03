<?php

class Cow {
  // Properties
  public $id;
  public $name;
  public $owner_id;
  public $gender;
  public $type;
  public $race;

  // Methods
  function setId($id) {
    $this->id = $id;
  }
  function getId() {
    return $this->id;
  }

  function setName($name) {
    $this->name = $name;
  }
  function getName() {
    return $this->name;
  }

  function setOwner($owner_id) {
    $this->owner_id = $owner_id;
  }
  function getOwner() {
    return $this->owner_id;
  }

  function setGender($gender) {
    $this->gender = $gender;
  }
  function getGender() {
    return $this->gender;
  }
  
  function setType($type) {
    $this->type = $type;
  }
  function getType() {
    return $this->type;
  }

  function setRace($race) {
    $this->race = $race;
  }
  function getRace() {
    return $this->race;
  }
}

?>