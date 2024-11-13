<?php

namespace Application\Permit\Model;

class Permit
{
    /** @var int $permitId */
    public $permitId;
    /** @var int $residentId */
    public $residentId;
    /** @var int $agentId */
    public $agentId;
    /** @var string $businessEntityType */
    public $businessEntityType;
    /** @var string|null $lastName */
    public $lastName;
    /** @var string|null $firstName */
    public $firstName;
    /** @var string|null $middleName */
    public $middleName;
    /** @var string|null $principalAddress */
    public $principalAddress;
    /** @var string|null $corporateName */
    public $corporateName;
    /** @var string|null $tradeName */
    public $tradeName;
    /** @var string|null $businessAddress */
    public $businessAddress;
    /** @var string|null $natureOfBusiness */
    public $natureOfBusiness;
    /** @var float|null $area */
    public $area;
    /** @var float|null $capital */
    public $capital;
    /** @var float|null $monthlyRent */
    public $monthlyRent;
    /** @var int $permitStatusId */
    public $permitStatusId;
    /** @var float|null $mayorsFee */
    public $mayorsFee;
    /** @var float|null $licenseFee */
    public $licenseFee;
    /** @var float|null $garbageFee */
    public $garbageFee;
    /** @var float|null $zoningFee */
    public $zoningFee;
    /** @var float|null $processingFee */
    public $processingFee;
    /** @var string|null $dateCreated */
    public $dateCreated;

    public function exchangeArray($data)
    {
        $this->permitId = !empty($data['permitId']) ? $data['permitId'] : null;
        $this->residentId = !empty($data['residentId']) ? $data['residentId'] : null;
        $this->agentId = !empty($data['agentId']) ? $data['agentId'] : null;
        $this->businessEntityType = !empty($data['businessEntityType']) ? $data['businessEntityType'] : null;
        $this->lastName = !empty($data['lastName']) ? $data['lastName'] : null;
        $this->firstName = !empty($data['firstName']) ? $data['firstName'] : null;
        $this->middleName = !empty($data['middleName']) ? $data['middleName'] : null;
        $this->principalAddress = !empty($data['principalAddress']) ? $data['principalAddress'] : null;
        $this->corporateName = !empty($data['corporateName']) ? $data['corporateName'] : null;
        $this->tradeName = !empty($data['tradeName']) ? $data['tradeName'] : null;
        $this->businessAddress = !empty($data['businessAddress']) ? $data['businessAddress'] : null;
        $this->natureOfBusiness = !empty($data['natureOfBusiness']) ? $data['natureOfBusiness'] : null;
        $this->area = !empty($data['area']) ? $data['area'] : null;
        $this->capital = !empty($data['capital']) ? $data['capital'] : null;
        $this->monthlyRent = !empty($data['monthlyRent']) ? $data['monthlyRent'] : null;
        $this->permitStatusId = !empty($data['permitStatusId']) ? $data['permitStatusId'] : null;
        $this->mayorsFee = !empty($data['mayorsFee']) ? $data['mayorsFee'] : null;
        $this->licenseFee = !empty($data['licenseFee']) ? $data['licenseFee'] : null;
        $this->garbageFee = !empty($data['garbageFee']) ? $data['garbageFee'] : null;
        $this->zoningFee = !empty($data['zoningFee']) ? $data['zoningFee'] : null;
        $this->processingFee = !empty($data['processingFee']) ? $data['processingFee'] : null;
        $this->dateCreated = !empty($data['dateCreated']) ? $data['dateCreated'] : null;
    }
}
