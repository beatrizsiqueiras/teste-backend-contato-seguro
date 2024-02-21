<?php

namespace ContatoSeguro\TesteBackend\Model;

class AdminUser
{

    public function __construct(
        public int $id,
        public int $companyId,
        public string $email,
        public string $name,
        public string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }

    public static function hydrateByFetch($fetch): self
    {
        return new self(
            $fetch->id,
            $fetch->company_id,
            $fetch->email,
            $fetch->name,
            $fetch->created_at,
            $fetch->updated_at,
            $fetch->deleted_at
        );
    }
}
