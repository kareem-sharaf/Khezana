<?php

declare(strict_types=1);

namespace App\ViewModels\Profile;

use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Profile ViewModel
 * 
 * Encapsulates all presentation logic for user profile pages
 * Separates UI concerns from business logic
 */
final class ProfileViewModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?Carbon $emailVerifiedAt,
        public readonly ?Carbon $createdAt,
        public readonly ?string $avatar = null,
        public readonly ?string $phone = null,
        public readonly ?string $bio = null,
    ) {
    }

    /**
     * Create ViewModel from User model
     */
    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            emailVerifiedAt: $user->email_verified_at,
            createdAt: $user->created_at,
            avatar: null, // Avatar not in User model currently
            phone: $user->phone ?? null,
            bio: null, // Bio not in User model currently
        );
    }

    /**
     * Check if email is verified
     */
    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    /**
     * Get formatted email verification status
     */
    public function getEmailVerificationStatus(): string
    {
        return $this->isEmailVerified() 
            ? __('profile.email_verified') 
            : __('profile.email_not_verified');
    }

    /**
     * Get member since date
     */
    public function getMemberSince(): string
    {
        return $this->createdAt?->format('Y-m-d') ?? '';
    }

    /**
     * Get member since formatted
     */
    public function getMemberSinceFormatted(): string
    {
        return $this->createdAt?->diffForHumans() ?? '';
    }

    /**
     * Convert to array for Blade
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'emailVerifiedAt' => $this->emailVerifiedAt?->toDateTimeString(),
            'createdAt' => $this->createdAt?->toDateTimeString(),
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'bio' => $this->bio,
            'isEmailVerified' => $this->isEmailVerified(),
            'emailVerificationStatus' => $this->getEmailVerificationStatus(),
            'memberSince' => $this->getMemberSince(),
            'memberSinceFormatted' => $this->getMemberSinceFormatted(),
        ];
    }
}
