<?php

declare(strict_types=1);

namespace App\Read\Requests\Models;

use App\Enums\OfferStatus;
use App\Models\Offer;
use App\Models\Request;
use App\Read\Offers\Models\OfferReadModel;
use App\Read\Shared\Models\AttributeReadModel;
use App\Read\Shared\Models\CategoryReadModel;
use App\Read\Shared\Models\UserReadModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RequestReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $status,
        public readonly string $statusLabel,
        public readonly string $slug,
        public readonly string $url,
        public readonly ?UserReadModel $user,
        public readonly ?CategoryReadModel $category,
        public readonly Collection $attributes,
        public readonly Collection $offers,
        public readonly int $offersCount,
        public readonly Carbon $createdAt,
        public readonly Carbon $updatedAt,
        public readonly string $createdAtFormatted,
        public readonly string $updatedAtFormatted,
        public readonly string $canonicalUrl,
        public readonly array $metaTags,
    ) {
    }

    public static function fromModel(Request $request): self
    {
        $slug = $request->slug ?? Str::slug($request->title);
        $url = route('public.requests.show', ['id' => $request->id, 'slug' => $slug]);
        $canonicalUrl = route('public.requests.show', ['id' => $request->id, 'slug' => $slug]);

        $offersCount = $request->offers_count ?? $request->offers->where('status', OfferStatus::PENDING)->count();

        return new self(
            id: $request->id,
            title: $request->title,
            description: $request->description ?? '',
            status: $request->status->value,
            statusLabel: $request->status->label(),
            slug: $slug,
            url: $url,
            user: $request->user ? UserReadModel::fromModel($request->user) : null,
            category: $request->category ? CategoryReadModel::fromModel($request->category) : null,
            attributes: $request->itemAttributes->map(fn($attr) => AttributeReadModel::fromModel($attr)),
            offers: $request->offers->map(fn(Offer $offer) => OfferReadModel::fromModel($offer)),
            offersCount: $offersCount,
            createdAt: $request->created_at,
            updatedAt: $request->updated_at,
            createdAtFormatted: $request->created_at->diffForHumans(),
            updatedAtFormatted: $request->updated_at->diffForHumans(),
            canonicalUrl: $canonicalUrl,
            metaTags: [
                'robots' => 'index, follow',
                'og:type' => 'article',
                'og:title' => $request->title,
                'og:description' => Str::limit($request->description ?? '', 160),
            ],
        );
    }
}
