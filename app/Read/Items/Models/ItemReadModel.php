<?php

declare(strict_types=1);

namespace App\Read\Items\Models;

use App\Models\Item;
use App\Read\Shared\Models\AttributeReadModel;
use App\Read\Shared\Models\CategoryReadModel;
use App\Read\Shared\Models\ImageReadModel;
use App\Read\Shared\Models\UserReadModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ItemReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $governorate,
        public readonly ?string $condition,
        public readonly ?float $price,
        public readonly ?float $depositAmount,
        public readonly string $operationType,
        public readonly string $operationTypeLabel,
        public readonly string $availabilityStatus,
        public readonly string $availabilityStatusLabel,
        public readonly string $slug,
        public readonly string $url,
        public readonly ?UserReadModel $user,
        public readonly ?CategoryReadModel $category,
        public readonly Collection $images,
        public readonly ?ImageReadModel $primaryImage,
        public readonly Collection $attributes,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly string $createdAtFormatted,
        public readonly string $updatedAtFormatted,
        public readonly string $canonicalUrl,
        public readonly array $metaTags,
    ) {
    }

    public static function fromModel(Item $item): self
    {
        $slug = $item->slug ?? Str::slug($item->title);
        $url = route('public.items.show', ['id' => $item->id, 'slug' => $slug]);
        $canonicalUrl = route('public.items.show', ['id' => $item->id, 'slug' => $slug]);

        $images = $item->images->map(fn($img) => ImageReadModel::fromModel($img));
        $primaryImage = $images->first();

        $availabilityStatus = $item->availability_status?->value ?? ($item->is_available ? 'available' : 'unavailable');
        $availabilityStatusLabel = $item->availability_status?->label() ?? ($item->is_available ? 'Available' : 'Unavailable');

        return new self(
            id: $item->id,
            title: $item->title,
            description: $item->description ?? '',
            governorate: $item->governorate ?? null,
            condition: $item->condition ?? null,
            price: $item->price ? (float) $item->price : null,
            depositAmount: $item->deposit_amount ? (float) $item->deposit_amount : null,
            operationType: $item->operation_type->value,
            operationTypeLabel: $item->operation_type->getLabel(),
            availabilityStatus: $availabilityStatus,
            availabilityStatusLabel: $availabilityStatusLabel,
            slug: $slug,
            url: $url,
            user: $item->user ? UserReadModel::fromModel($item->user) : null,
            category: $item->category ? CategoryReadModel::fromModel($item->category) : null,
            images: $images,
            primaryImage: $primaryImage,
            attributes: $item->itemAttributes->map(fn($attr) => AttributeReadModel::fromModel($attr)),
            createdAt: $item->created_at ?? now(),
            updatedAt: $item->updated_at ?? now(),
            createdAtFormatted: ($item->created_at ?? now())->diffForHumans(),
            updatedAtFormatted: ($item->updated_at ?? now())->diffForHumans(),
            canonicalUrl: $canonicalUrl,
            metaTags: [
                'robots' => 'index, follow',
                'og:type' => 'product',
                'og:title' => $item->title,
                'og:description' => Str::limit($item->description ?? '', 160),
                'og:image' => $primaryImage?->path ?? config('app.default_image', ''),
            ],
        );
    }

    public function getFormattedPrice(): ?string
    {
        return $this->price ? number_format($this->price, 2) . ' ' . config('app.currency', 'SAR') : null;
    }
}
