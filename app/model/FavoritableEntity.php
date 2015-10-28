<?php

namespace Model\Entity;

use DateTime;
use Model\Repository\FavoriteRepository;
use Model\Entity\Favorite;

class FavoritableEntity extends Entity 
{
    private $favoriteRepository;
    
    public function favorite($user) 
    {
        if ($favorite = $this->getFavoriteByUser($user))
        {
            $this->favoriteRepository->delete($favorite->id);
            return true;
        }

        $favorite = new Favorite;
        $favorite->user = $user;
        $favorite->entity = $this->getConventionalName();
        $favorite->entity_id = $this->id;
        $favorite->saved_at = new DateTime;
        $this->favoriteRepository->persist($favorite);
        return true;
    }
    
    public function removeFavorite($user) 
    {
        
    }
    
    public function isFavoritedBy($user) 
    {
        return $this->favoriteRepository->findByUserAndId(
            $user, $this->getConventionalName(), $this->id
        ) ? true : false;
    }
    
    public function getFavoriteByUser($user)
    {
        return $this->favoriteRepository->findByUserAndId(
            $user, $this->getConventionalName(), $this->id
        );
    }    
    
    public function countFavorites() 
    {
        return $this->favoriteRepository->countFavoritesOfEntity(
            $this->getConventionalName(), $this->id
        );
    }
    
    public function setFavoriteRepository(FavoriteRepository $repository) 
    {   
        $this->favoriteRepository = $repository;

    }
}
