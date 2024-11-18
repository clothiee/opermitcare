<?php

namespace Application\Permit\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class PermitTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function getByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }

    public function save(Permit $permit)
    {
        $data = [
            'permitId' => $permit->permitId,
            'residentId' => $permit->residentId,
            'agentId' => $permit->agentId,
            'businessEntityType' => $permit->businessEntityType,
            'lastName' => $permit->lastName,
            'firstName' => $permit->firstName,
            'middleName' => $permit->middleName,
            'lessorName' => $permit->lessorName,
            'corporateName' => $permit->corporateName,
            'tradeName' => $permit->tradeName,
            'businessAddress' => $permit->businessAddress,
            'natureOfBusiness' => $permit->natureOfBusiness,
            'area' => $permit->area,
            'capital' => $permit->capital,
            'monthlyRent' => $permit->monthlyRent,
            'permitStatusId' => $permit->permitStatusId,
            'mayorsFee' => $permit->mayorsFee,
            'licenseFee' => $permit->licenseFee,
            'garbageFee' => $permit->garbageFee,
            'zoningFee' => $permit->zoningFee,
            'processingFee' => $permit->processingFee,
            'dateCreated' => $permit->dateCreated,
            'remarks' => $permit->remarks,
        ];

        $permitId = (int) $permit->permitId;

        if ($permitId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['permitId' => $permitId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $permitId
                                       ));
        }

        $this->tableGateway->update($data, ['permitId' => $permitId]);
    }

    public function delete($permitId)
    {
        $this->tableGateway->delete(['permitId' => (int) $permitId]);
    }
}
