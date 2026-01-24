<?php

return [
    'title' => 'Offers',
    'singular' => 'Offer',
    'plural' => 'Offers',
    
    'fields' => [
        'operation_type' => 'Operation Type',
        'price' => 'Price',
        'deposit_amount' => 'Deposit Amount',
        'status' => 'Status',
        'message' => 'Message',
        'item' => 'Item',
        'linked_item' => 'Linked Item',
        'offer_owner' => 'Offer Owner',
    ],
    
    'status' => [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'cancelled' => 'Cancelled',
    ],
    
    'messages' => [
        'created_successfully' => 'Offer created successfully',
        'updated_successfully' => 'Offer updated successfully',
        'cancelled_successfully' => 'Offer cancelled successfully',
        'accepted_successfully' => 'Offer accepted successfully',
        'rejected_successfully' => 'Offer rejected successfully',
        'no_offers' => 'No offers found',
    ],
    
    'validation' => [
        'price_required_for_sell' => 'Price is required for selling',
        'price_required_for_rent' => 'Price is required for renting',
        'deposit_required_for_rent' => 'Deposit amount is required for renting',
        'request_not_approved' => 'Request is not approved',
        'request_not_open' => 'Request is not open',
        'request_already_fulfilled' => 'Request is already fulfilled',
        'request_closed' => 'Request is closed',
        'duplicate_offer' => 'You already have an offer on this request',
        'owner_cannot_offer_self' => 'Request owner cannot submit an offer on their own request',
        'cannot_accept_non_pending' => 'Cannot accept a non-pending offer',
        'cannot_reject_non_pending' => 'Cannot reject a non-pending offer',
        'cannot_update_final' => 'Cannot update a final offer (accepted or rejected)',
        'cannot_cancel_non_pending' => 'Cannot cancel a non-pending offer',
        'only_owner_can_cancel' => 'Only offer owner can cancel',
        'only_request_owner_can_accept' => 'Only request owner can accept offer',
        'only_request_owner_can_reject' => 'Only request owner can reject offer',
    ],
    
    'placeholders' => [
        'price' => 'Enter price',
        'deposit_amount' => 'Enter deposit amount',
        'message' => 'Enter message (optional)',
        'no_item' => 'No linked item',
    ],
    
    'actions' => [
        'create' => 'Create Offer',
        'edit' => 'Edit Offer',
        'update' => 'Update',
        'cancel' => 'Cancel Offer',
        'accept' => 'Accept',
        'reject' => 'Reject',
    ],
];
