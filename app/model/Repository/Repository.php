<?php

namespace Model\Repository;

use Nette\Application\BadRequestException;
use DateTime;

abstract class Repository extends \LeanMapper\Repository
{
    public function find($id)
    {
        $row = $this->connection->select('*')
            ->from($this->getTable())
            ->where('id = %i', $id)
            ->fetch();

        if ($row === false) {
            throw new BadRequestException('Entity does not exist.', 404);
        }

        return $this->createEntity($row);
    }

    public function findAll($orderBy = null)
    {
        $query = $this->connection->select('*')
                ->from($this->getTable());
        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $this->createEntities($query->fetchAll());
    }
}
