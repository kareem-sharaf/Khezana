<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Services\StorePickupService;
use App\Services\QrService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * StorePickupController
 *
 * API endpoints لعمليات الاستلام من المتجر
 */
class StorePickupController extends Controller
{
    public function __construct(
        protected StorePickupService $storePickupService,
        protected QrService $qrService,
    ) {}

    /**
     * POST /api/store/verify-qr
     * التحقق من صحة QR في المتجر
     */
    public function verifyQr(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'qr_code' => 'required|string',
            ]);

            $orderData = $this->storePickupService->verifyQrCode($validated['qr_code']);

            return response()->json([
                'success' => true,
                'message' => 'تم التحقق من الكود بنجاح',
                'data' => $orderData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * POST /api/store/complete-pickup
     * إكمال عملية الاستلام
     */
    public function completePickup(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'payment_amount' => 'nullable|numeric',
                'payment_method' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            // التحقق من أن المستخدم موظف متجر
            if (auth()->user()->user_type !== 'STORE_STAFF') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بإجراء هذه العملية',
                ], 403);
            }

            $order = Order::findOrFail($validated['order_id']);

            $order = $this->storePickupService->completePickup(
                $order,
                auth()->id(),
                [
                    'amount' => $validated['payment_amount'] ?? $order->total_amount,
                    'payment_method' => $validated['payment_method'] ?? $order->payment_method,
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'تم استلام الطلب بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * GET /api/store/pickup-orders
     * قائمة طلبات الاستلام المعلقة
     */
    public function getPendingPickups(): JsonResponse
    {
        // التحقق من أن المستخدم موظف متجر
        if (auth()->user()->user_type !== 'STORE_STAFF') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بعرض هذه البيانات',
            ], 403);
        }

        // هنا يجب أن نحصل على المتجر الخاص بالموظف
        // مثلاً من علاقة StoreStaff
        // لأغراض التطوير، نستخدم store_id من الطلب الأول للموظف

        $orders = Order::where('channel', 'IN_STORE_PICKUP')
            ->whereIn('status', ['CONFIRMED', 'READY_FOR_PICKUP'])
            ->with(['items.item', 'customer', 'qrCode'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer->name,
                'customer_phone' => $order->customer->phone,
                'total_amount' => $order->total_amount,
                'pickup_expiry' => $order->pickup_expiry,
                'hours_left' => $order->pickup_expiry->diffInHours(now()),
                'qr_code' => $order->qrCode->qr_code,
                'items_count' => $order->items->count(),
            ]),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
            ]
        ]);
    }

    /**
     * GET /api/store/statistics
     * إحصائيات المتجر
     */
    public function getStatistics(Request $request): JsonResponse
    {
        // التحقق من أن المستخدم موظف متجر
        if (auth()->user()->user_type !== 'STORE_STAFF') {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بعرض هذه البيانات',
            ], 403);
        }

        // الحصول على معرف المتجر من الطلب أو من بيانات الموظف
        $storeId = $request->input('store_id', 1); // مثال مؤقت
        $date = $request->input('date', null);

        $stats = $this->storePickupService->getStoreStatistics($storeId, $date);

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
