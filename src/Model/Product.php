<?php

namespace ContatoSeguro\TesteBackend\Model;

class Product
{

    public array $category;

    public function __construct(
        public int $id,
        public int $companyId,
        public string $title,
        public float $price,
        public bool $active,
        public string $createdAt,
        public ?string $deletedAt,
        public ?string $updatedAt
    ) {
    }

    public static function hydrateByFetch($fetch): self
    {
        return new self(
            $fetch->id,
            $fetch->company_id,
            $fetch->title,
            $fetch->price,
            $fetch->active,
            $fetch->created_at,
            $fetch->deleted_at,
            $fetch->updated_at
        );
    }

    public function setCategory(array $category)
    {
        $this->category = $category;
    }
}
