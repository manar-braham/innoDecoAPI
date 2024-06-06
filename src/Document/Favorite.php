<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;

#[MongoDB\Document(db: "innoDeco_db", collection: "favorites")]
class Favorite
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: Type::STRING)]
    private $userId;

    #[MongoDB\Field(type: Type::STRING)]
    private $productId;

    #[MongoDB\Field(type: Type::BOOLEAN)]
    private $favorite;

    // Constructeur
    public function __construct(string $userId, string $productId, bool $favorite)
    {
        $this->userId = $userId;
        $this->productId = $productId;
        $this->favorite = $favorite;
        
    }

    // Getters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function isFavorite(): ?bool
    {
        return $this->favorite;
    }
    

    // Setters
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function setFavorite(bool $favorite): void
    {
        $this->favorite = $favorite;
    }
}
