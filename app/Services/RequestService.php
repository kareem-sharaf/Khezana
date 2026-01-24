<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AttributeType;
use App\Enums\RequestStatus;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Request Service
 * 
 * Handles business logic for requests:
 * - Validates category attributes
 * - Validates request lifecycle transitions
 */
class RequestService
{
    /**
     * Validate request data
     *
     * @param array $data Request data
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateRequestData(array $data): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];

        Validator::make($data, $rules)->validate();
    }

    /**
     * Validate category attributes for a request
     *
     * @param Request $request The request model
     * @param array $attributes Attribute values (slug => value)
     * @throws \Exception If validation fails
     */
    public function validateCategoryAttributes(Request $request, array $attributes): void
    {
        $category = $request->category;
        
        if (!$category) {
            throw new \Exception('Request must have a category');
        }

        // Get all required attributes for this category (including inherited)
        $requiredAttributes = $category->getAllRequiredAttributes();

        // Filter out 'condition' attribute for requests (not shown in request form)
        $requiredAttributes = $requiredAttributes->filter(function ($attribute) {
            return $attribute->slug !== 'condition';
        });

        // Check that all required attributes are provided
        foreach ($requiredAttributes as $attribute) {
            $slug = $attribute->slug;
            
            if (!isset($attributes[$slug]) || empty($attributes[$slug])) {
                throw new \Exception("Required attribute '{$attribute->name}' is missing");
            }

            // Validate attribute value based on type
            $this->validateAttributeValue($attribute, $attributes[$slug]);
        }
    }

    /**
     * Validate attribute value based on attribute type
     *
     * @param Attribute $attribute The attribute
     * @param mixed $value The value to validate
     * @throws \Exception If validation fails
     */
    private function validateAttributeValue(Attribute $attribute, mixed $value): void
    {
        switch ($attribute->type) {
            case AttributeType::SELECT:
                // Value must be one of the predefined values
                $validValues = $attribute->values()->pluck('value')->toArray();
                if (!in_array($value, $validValues)) {
                    throw new \Exception("Invalid value for attribute '{$attribute->name}'. Must be one of: " . implode(', ', $validValues));
                }
                break;

            case AttributeType::NUMBER:
                // Value must be numeric
                if (!is_numeric($value)) {
                    throw new \Exception("Attribute '{$attribute->name}' must be a number");
                }
                break;

            case AttributeType::TEXT:
                // Value must be a string
                if (!is_string($value)) {
                    throw new \Exception("Attribute '{$attribute->name}' must be text");
                }
                break;
        }
    }

    /**
     * Validate status transition
     *
     * @param Request $request The request
     * @param RequestStatus $newStatus The new status
     * @throws \Exception If transition is invalid
     */
    public function validateStatusTransition(Request $request, RequestStatus $newStatus): void
    {
        $currentStatus = $request->status;

        // Cannot change status if already closed
        if ($currentStatus === RequestStatus::CLOSED && $newStatus !== RequestStatus::CLOSED) {
            throw new \Exception('Cannot change status of a closed request');
        }

        // Cannot change status if already fulfilled
        if ($currentStatus === RequestStatus::FULFILLED && $newStatus !== RequestStatus::FULFILLED) {
            throw new \Exception('Cannot change status of a fulfilled request');
        }
    }

    /**
     * Close a request
     *
     * @param Request $request The request to close
     * @return Request
     * @throws \Exception If request cannot be closed
     */
    public function closeRequest(Request $request): Request
    {
        $this->validateStatusTransition($request, RequestStatus::CLOSED);
        
        $request->update(['status' => RequestStatus::CLOSED]);
        
        return $request->fresh();
    }

    /**
     * Mark request as fulfilled
     *
     * @param Request $request The request to mark as fulfilled
     * @return Request
     * @throws \Exception If request cannot be fulfilled
     */
    public function fulfillRequest(Request $request): Request
    {
        $this->validateStatusTransition($request, RequestStatus::FULFILLED);
        
        $request->update(['status' => RequestStatus::FULFILLED]);
        
        return $request->fresh();
    }
}
