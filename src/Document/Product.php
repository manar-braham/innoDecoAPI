<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;

#[MongoDB\Document(db: "innoDeco_db", collection: "fournitures")]
class Product
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: Type::STRING)]
    private string $url;

    #[MongoDB\Field(type: Type::STRING)]
    private ?string $category;

    #[MongoDB\Field(type: Type::STRING)]
    private ?string $type;

    #[MongoDB\Field(type: Type::STRING)]
    private ?string $color;

    /**
     * @param string $url
     */
    public function __construct(string $url, string $category, string $type, string $color)
    {
        $this->url = $url;
        $this->category = $category;
        $this->type = $type;
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}