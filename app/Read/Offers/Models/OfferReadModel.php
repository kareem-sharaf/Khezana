<?php

declare(strict_types=1);

namespace App\Read\Offers\Models;

use App\Models\Offer;
use App\Read\Items\Models\ItemReadModel;
use App\Read\Requests\Models\RequestReadModel;
use App\Read\Shared\Models\UserReadModel;
use Carbon\Carbon;

class OfferReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $operationType,
        public readonly string $operationTypeLabel,
        public readonly ?float $price,
        public readonly ?float $depositAmount,
        public readonly string $status,
        public readonly string $statusLabel,
        public readonly ?string $message,
        public readonly ?UserReadModel $user,
        public readonly ?ItemReadModel $item,
        public readonly ?RequestReadModel $request,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly string $createdAtFormatted,
        public readonly string $updatedAtFormatted,
    ) {
    }

    public static function fromModel(Offer $offer): self
    {
        return new self(
            id: $offer->id,
            operationType: $offer->operation_type->value,
            operationTypeLabel: $offer->operation_type->getLabel(),
            price: $offer->price,
            depositAmount: $offer->deposit_amount,
            status: $offer->status->value,
            statusLabel: $offer->status->label(),
            message: $offer->message,
            user: $offer->user ? UserReadModel::fromModel($offer->user) : null,
            item: $offer->item ? ItemReadModel::fromModel($offer->item) : null,
            request: $offer->request ? RequestReadModel::fromModel($offer->request) : null,
            createdAt: $offer->created_at,
            updatedAt: $offer->updated_at,
            createdAtFormatted: $offer->created_at->diffForHumans(),
            updatedAtFormatted: $offer->updated_at->diffForHumans(),
        );
    }
}
