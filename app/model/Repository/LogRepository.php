<?php

namespace Model\Repository;

class LogRepository extends \LeanMapper\Repository
{   
	public function findByEntity($entity_name, $entity_id)
	{
        $rows = $this->connection->select('*')
            ->from($this->getTable())
            ->where('entity_name = %s', $entity_name)
            ->where('entity_identifier = %i', $entity_id)
            ->fetchAll();

        return $this->createEntities($rows);
	}
}
