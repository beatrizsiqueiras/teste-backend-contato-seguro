<?php

namespace ContatoSeguro\TesteBackend\Model;

class ProductCategory
{
    public function __construct(
        public int $id,
        public int $categoryId,
        public int $productId,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }

    public static function hydrateByFetch($fetch): self
    {
        return new self(
            $fetch->id,
            $fetch->category_id,
            $fetch->product_id,
            $fetch->created_at,
            $fetch->updated_at,
            $fetch->deleted_at
        );
    }
}
