<?php

declare(strict_types=1);

namespace App\Actions\Request;

use App\Models\Request;
use App\Models\User;
use App\Services\RequestService;

/**
 * Action to close a request
 */
class CloseRequestAction
{
    public function __construct(
        private readonly RequestService $requestService
    ) {
    }

    /**
     * Close a request
     *
     * @param Request $request The request to close
     * @param User $user The user closing the request
     * @return Request
     * @throws \Exception If request cannot be closed
     */
    public function execute(Request $request, User $user): Request
    {
        // Only request owner can close
        if ($request->user_id !== $user->id) {
            throw new \Exception('Only the request owner can close the request');
        }

        // Use service to validate and close
        return $this->requestService->closeRequest($request);
    }
}
