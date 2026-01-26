<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * OrderController
 *
 * API endpoints لإدارة الطلبات
 */
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    /**
     * POST /api/orders
     * إنشاء طلب جديد
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'channel' => 'required|in:DELIVERY,IN_STORE_PICKUP',
                'items' => 'required|array|min:1',
                'items.*.item_id' => 'required|integer|exists:items,id',
                'items.*.operation_type' => 'required|in:RENT,SALE,DONATE',
                'items.*.rent_days' => 'nullable|integer|min:1',
                'items.*.rent_price_per_day' => 'nullable|numeric',
                'items.*.deposit_amount' => 'nullable|numeric',
                'delivery_address' => 'required_if:channel,DELIVERY|string',
                'delivery_city' => 'required_if:channel,DELIVERY|string',
                'delivery_notes' => 'nullable|string',
                'pickup_store_id' => 'required_if:channel,IN_STORE_PICKUP|integer|exists:inspection_centers,id',
                'payment_method' => 'required|in:CASH_ON_DELIVERY,CASH_IN_STORE,ONLINE',
            ]);

            $order = $this->orderService->createOrder($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الطلب بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'qr_code' => $order->qrCode?->qr_code,
                    'total_amount' => $order->total_amount,
                    'channel' => $order->channel,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/orders/{id}
     * الحصول على تفاصيل الطلب
     */
    public function getDetails(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderDetails($id);

            // التحقق من الصلاحيات
            if ($order->customer_id !== auth()->id() && !auth()->user()?->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بعرض هذا الطلب',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatOrderData($order),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * POST /api/orders/{id}/cancel
     * إلغاء الطلب
     */
    public function cancel(int $id, Request $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);

            // التحقق من الصلاحيات
            if ($order->customer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بإلغاء هذا الطلب',
                ], 403);
            }

            $reason = $request->input('reason', 'إلغاء من قبل العميل');
            $this->orderService->cancelOrder($order, $reason);

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/user/orders
     * قائمة طلبات المستخدم
     */
    public function listUserOrders(): JsonResponse
    {
        $orders = $this->orderService->getUserOrders(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn($order) => $this->formatOrderData($order)),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ]
        ]);
    }

    /**
     * تنسيق بيانات الطلب للإرجاع
     */
    private function formatOrderData(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'channel' => $order->channel,
            'total_amount' => $order->total_amount,
            'delivery_fee' => $order->delivery_fee,
            'payment_status' => $order->payment_status,
            'qr_code' => $order->qrCode?->qr_code,
            'qr_image' => $order->qrCode ? asset('storage/qr-codes/' . $order->id . '.png') : null,
            'pickup_expiry' => $order->pickup_expiry,
            'hours_left' => $order->pickup_expiry ? $order->pickup_expiry->diffInHours(now()) : null,
            'items' => $order->items->map(fn($item) => [
                'item_id' => $item->item_id,
                'title' => $item->item->title,
                'operation_type' => $item->operation_type,
                'price' => $item->item_total,
            ]),
            'customer' => [
                'name' => $order->customer->name,
                'email' => $order->customer->email,
            ],
            'store' => $order->pickupStore ? [
                'id' => $order->pickupStore->id,
                'name' => $order->pickupStore->name,
                'address' => $order->pickupStore->address,
            ] : null,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }
}
