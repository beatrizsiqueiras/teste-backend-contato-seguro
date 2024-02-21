<?php

namespace ContatoSeguro\TesteBackend\Model;

class Company
{
    public function __construct(
        public int $id,
        public string $name,
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
            $fetch->name,
            $fetch->active,
            $fetch->created_at,
            $fetch->updated_at,
            $fetch->deleted_at
        );
    }
}
