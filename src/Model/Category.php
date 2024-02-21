<?php

namespace ContatoSeguro\TesteBackend\Model;

class Category
{
    public function __construct(
        public int $id,
        public ?int $companyId,
        public string $title,
        public bool $active,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }

    public static function hydrateByFetch($fetch): self
    {
        return new self(
            $fetch->id,
            $fetch->company_id,
            $fetch->title,
            $fetch->active,
            $fetch->created_at,
            $fetch->updated_at,
            $fetch->deleted_at
        );
    }
}
