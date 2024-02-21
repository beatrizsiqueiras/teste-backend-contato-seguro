<?php

namespace ContatoSeguro\TesteBackend\Model;

class ProductLog
{
    public function __construct(
        public int $id,
        public int $productId,
        public int $adminUserId,
        public string $action,
        public string $before,
        public string $after,
        public string $createdAt,
        public ?string $updatedAt,
        public ?string $deletedAt
    ) {
    }

    public static function hydrateByFetch($fetch): self
    {
        return new self(
            $fetch->id,
            $fetch->product_id,
            $fetch->admin_user_id,
            $fetch->action,
            $fetch->before,
            $fetch->after,
            $fetch->created_at,
            $fetch->updated_at,
            $fetch->deleted_at
        );
    }
}
