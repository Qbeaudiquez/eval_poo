<?php

namespace Urssaf\Model;

class Contractor{
    public function __construct(
private int $id,
private string $fullName,
private string $siret,
private string $activity,
private string $taxSysteme,
private string $createdAt
    ){}

    public function getId() : int{
        return $this->id;
    }
    public function getFullName() : string{
        return $this->fullName;
    }
    public function getSiret() : string{
        return $this->siret;
    }
    public function getActivity() : string{
        return $this->activity;
    }
    public function getTaxSysteme() : string{
        return $this->taxSysteme;
    }
    public function getCreatedAt() : string{
        return $this->createdAt;
    }
}