<?php

namespace Model\Repository;

use Model\Entity\User;

class FavoriteRepository extends Repository
{
    public function findByUserAndId(User $user, $entity, $entity_id) 
    {
        $where = array(
            'user_id%i' => $user->id,
            'entity%s' => $entity,
            'entity_id%i' => $entity_id   
        );
        
        $query = $this->connection->select('*')
            ->from($this->getTable())
            ->where($where);
        
        return $query->fetch();
    }
    
    public function findAllByScope($entity, array $entity_ids) 
    {
        $where = array(
            'entity%s' => $entity,
            'entity_id%in' => $entity_ids
        );
        
        $query = $this->connection->select('[entity_id], count(*) as [count]')
            ->from($this->getTable())
            ->groupBy('[entity_id]')
            ->where($where);
        
        return $query->fetchPairs('entity_id', 'count'); 
    }
      
    public function countFavoritesOfEntity($entity, $entity_id) 
    {
        $where = array(
            'entity%s' => $entity,
            'entity_id%i' => $entity_id   
        );
        
        $query = $this->connection->select('count(*)')
            ->from($this->getTable())
            ->where($where);
            
        return $query->fetchSingle();
    }
}
