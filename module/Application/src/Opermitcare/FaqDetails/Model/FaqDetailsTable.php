<?php

namespace Application\Opermitcare\FaqDetails\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class FaqDetailsTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $rowSet = $this->tableGateway->select();

        return $this->parseRow($rowSet);
    }

    public function getByColumns($columns)
    {
        $rowSet = $this->tableGateway->select($columns);

        return $this->parseRow($rowSet);
    }

    public function save(FaqDetails $faqDetails)
    {
        $data = [
            'faqId' => $faqDetails->faqId,
            'title' => $faqDetails->title,
            'description' => $faqDetails->description,
            'active' => $faqDetails->active,
        ];

        $faqDetailsId = (int) $faqDetails->faqDetailsId;

        if ($faqDetailsId === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getByColumns(['faqDetailsId' => $faqDetailsId]);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                                           'Cannot update user with identifier %d; does not exist',
                                           $faqDetailsId
                                       ));
        }

        $this->tableGateway->update($data, ['faqDetailsId' => $faqDetailsId]);
    }

    public function delete($faqDetailsId)
    {
        $this->tableGateway->delete(['faqDetailsId' => (int) $faqDetailsId]);
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
