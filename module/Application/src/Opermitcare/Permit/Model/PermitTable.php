<?php

namespace Application\Opermitcare\Permit\Model;

use Laminas\Db\Sql\Select;
use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class PermitTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($from = '', $to  = '')
    {
        $rowSet = $this->filter([], $from, $to);

        return $this->parseRow($rowSet);
    }

    public function getByColumns($columns, $from = '', $to = '')
    {
        try {
            $rowSet = $this->filter($columns, $from, $to);
        } catch (\Exception $exception) {
            $rowSet = $this->tableGateway->select($columns);
        }

        return $this->parseRow($rowSet);
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
            return $this->tableGateway->getLastInsertValue();
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

    private function filter($columns, $from = '', $to = '')
    {
        $table = $this->tableGateway->getTable();
        $select = new Select($table);

        if (!empty($columns)) {
            $select->columns($columns);
        }

        if (!empty($from) && !empty($to)) {
            $select->where->greaterThanOrEqualTo('dateCreated', date('Y-m-d H:i:s', strtotime($from)));
            $select->where->lessThanOrEqualTo('dateCreated', date('Y-m-d H:i:s', strtotime($to . ' 23:59:59')));
        }

        $rowSet = $this->tableGateway->selectWith($select);

        return $this->parseRow($rowSet);
    }

    private function parseRow($rowSet)
    {
        $data = [];

        foreach ($rowSet as $row) {
            $data[] = $row;
        }

        return $data;
    }
}