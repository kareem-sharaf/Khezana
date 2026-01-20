<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Models\Request;
use App\Services\RequestService;
use Illuminate\Support\Facades\DB;

/**
 * Action to update an existing request
 */
class UpdateRequestAction
{
    public function __construct(
        private readonly RequestService $requestService
    ) {
    }

    /**
     * Update a request
     *
     * @param Request $request The request to update
     * @param array $data Updated data
     * @param array|null $attributes Updated attributes
     * @return Request
     * @throws \Exception If validation fails or request is closed
     */
    public function execute(Request $request, array $data, ?array $attributes = null): Request
    {
        // Cannot update closed or fulfilled requests
        if ($request->isClosed() || $request->isFulfilled()) {
            throw new \Exception('Cannot update a closed or fulfilled request');
        }

        // Validate request data
        $this->requestService->validateRequestData($data);

        return DB::transaction(function () use ($request, $data, $attributes) {
            // Update the request
            $request->update([
                'category_id' => $data['category_id'] ?? $request->category_id,
                'title' => $data['title'] ?? $request->title,
                'description' => $data['description'] ?? $request->description,
            ]);

            // Update dynamic attributes if provided
            if ($attributes !== null) {
                $this->requestService->validateCategoryAttributes($request, $attributes);
                // Remove old attributes and set new ones
                $request->itemAttributes()->delete();
                $request->setAttributeValues($attributes);
            }

            return $request->fresh(['user', 'category']);
        });
    }
}
