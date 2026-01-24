<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Models\Request;
use App\Models\User;
use App\Services\RequestService;
use Illuminate\Support\Facades\DB;

/**
 * Action to create a new request
 */
class CreateRequestAction
{
    public function __construct(
        private readonly RequestService $requestService,
        private readonly SubmitForApprovalAction $submitForApprovalAction
    ) {
    }

    /**
     * Create a new request
     *
     * @param array $data Request data
     * @param User $user The user creating the request
     * @param array|null $attributes Dynamic attributes
     * @return Request
     * @throws \Exception If validation fails
     */
    public function execute(array $data, User $user, ?array $attributes = null): Request
    {
        // Validate request data
        $this->requestService->validateRequestData($data);

        return DB::transaction(function () use ($data, $user, $attributes) {
            // Create the request
            $request = Request::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            // Set dynamic attributes if provided
            if ($attributes) {
                $this->requestService->validateCategoryAttributes($request, $attributes);
                $request->setAttributeValues($attributes);
            }

            // Create approval automatically
            $this->submitForApprovalAction->execute($request, $user);

            return $request->fresh(['user', 'category']);
        });
    }
}
