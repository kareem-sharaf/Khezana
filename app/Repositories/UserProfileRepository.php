<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\Models\UserProfile;

class UserProfileRepository extends BaseRepository
{
    public function __construct(UserProfile $model)
    {
        parent::__construct($model);
    }

    /**
     * Find profile by user ID.
     */
    public function findByUserId(int $userId): ?UserProfile
    {
        return $this->model->where('user_id', $userId)->first();
    }

    /**
     * Create or update profile from DTO.
     */
    public function createOrUpdateFromDTO(UserProfileDTO $dto): UserProfile
    {
        $profile = $this->findByUserId($dto->userId);

        if ($profile) {
            $profile->update($dto->toArray());
            return $profile;
        }

        return $this->create($dto->toArray());
    }
}
