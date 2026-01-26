<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Item;
use App\Models\InspectionCenter;
use App\Services\OrderService;
use App\Services\QrService;
use App\Services\StorePickupService;
use Tests\TestCase;

class OrderSystemTest extends TestCase
{
    protected User $customer;
    protected User $storeStaff;
    protected Item $item;
    protected InspectionCenter $store;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء بيانات الاختبار
        $this->customer = User::factory()->create(['user_type' => 'CUSTOMER']);
        $this->storeStaff = User::factory()->create(['user_type' => 'STORE_STAFF']);
        $this->store = InspectionCenter::factory()->create(['is_pickup_location' => true]);
        $this->item = Item::factory()->create(['item_status' => 'AVAILABLE']);
    }

    /**
     * ✅ اختبار: إنشاء طلب توصيل
     */
    public function test_create_delivery_order()
    {
        $this->actingAs($this->customer);

        $response = $this->postJson('/api/orders', [
            'channel' => 'DELIVERY',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'operation_type' => 'SALE',
                ]
            ],
            'delivery_address' => 'شارع النيل',
            'delivery_city' => 'الرياض',
            'payment_method' => 'CASH_ON_DELIVERY',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'تم إنشاء الطلب بنجاح',
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $this->customer->id,
            'channel' => 'DELIVERY',
            'status' => 'PENDING_PAYMENT',
        ]);
    }

    /**
     * ✅ اختبار: إنشاء طلب استلام من المتجر
     */
    public function test_create_store_pickup_order()
    {
        $this->actingAs($this->customer);

        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'operation_type' => 'SALE',
                ]
            ],
            'pickup_store_id' => $this->store->id,
            'payment_method' => 'CASH_IN_STORE',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonHasPath('data.qr_code')
            ->assertJsonHasPath('data.order_number');

        $order = Order::latest()->first();
        $this->assertEquals('IN_STORE_PICKUP', $order->channel);
        $this->assertEquals('CONFIRMED', $order->status);
        $this->assertNotNull($order->qrCode);
    }

    /**
     * ✅ اختبار: حجز المنتجات تلقائياً
     */
    public function test_items_automatically_reserved()
    {
        $this->actingAs($this->customer);

        $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                ['item_id' => $this->item->id, 'operation_type' => 'SALE']
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $this->item->refresh();
        $this->assertEquals('RESERVED', $this->item->item_status);
        $this->assertNotNull($this->item->reservation_expiry);
    }

    /**
     * ✅ اختبار: توليد QR Code
     */
    public function test_qr_code_generation()
    {
        $this->actingAs($this->customer);

        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                ['item_id' => $this->item->id, 'operation_type' => 'SALE']
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $qrCode = $response->json('data.qr_code');
        $this->assertNotNull($qrCode);

        $this->assertDatabaseHas('order_qr_codes', [
            'qr_code' => $qrCode,
            'status' => 'ACTIVE',
        ]);
    }

    /**
     * ✅ اختبار: التحقق من QR صحيح
     */
    public function test_verify_valid_qr_code()
    {
        $this->actingAs($this->customer);

        // إنشاء طلب
        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                ['item_id' => $this->item->id, 'operation_type' => 'SALE']
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $qrCode = $response->json('data.qr_code');

        // التحقق من QR
        $this->actingAs($this->storeStaff);
        $verifyResponse = $this->postJson('/api/store/verify-qr', [
            'qr_code' => $qrCode,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.customer_name', $this->customer->name);
    }

    /**
     * ✅ اختبار: رفض QR منتهي الصلاحية
     */
    public function test_reject_expired_qr_code()
    {
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'channel' => 'IN_STORE_PICKUP',
            'status' => 'CONFIRMED',
            'pickup_expiry' => now()->subHours(1), // انتهى منذ ساعة
        ]);

        $qrCode = $order->qrCode()->create([
            'qr_code' => 'QR-TEST',
            'qr_data' => json_encode(['order_id' => $order->id]),
            'expiry_date' => now()->subHours(1),
        ]);

        $this->actingAs($this->storeStaff);
        $response = $this->postJson('/api/store/verify-qr', [
            'qr_code' => $qrCode->qr_code,
        ]);

        $response->assertStatus(400)
            ->assertJson(['success' => false]);
    }

    /**
     * ✅ اختبار: إكمال عملية الاستلام
     */
    public function test_complete_pickup_process()
    {
        // إنشاء طلب
        $this->actingAs($this->customer);
        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                ['item_id' => $this->item->id, 'operation_type' => 'SALE']
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $orderId = $response->json('data.order_id');

        // إكمال الاستلام
        $this->actingAs($this->storeStaff);
        $completeResponse = $this->postJson('/api/store/complete-pickup', [
            'order_id' => $orderId,
            'payment_amount' => $response->json('data.total_amount'),
            'payment_method' => 'CASH_IN_STORE',
        ]);

        $completeResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $order = Order::find($orderId);
        $this->assertEquals('COMPLETED', $order->status);
        $this->assertEquals('PAID', $order->payment_status);

        $this->item->refresh();
        $this->assertEquals('SOLD', $this->item->item_status);
    }

    /**
     * ✅ اختبار: إلغاء طلب
     */
    public function test_cancel_order()
    {
        $this->actingAs($this->customer);

        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                ['item_id' => $this->item->id, 'operation_type' => 'SALE']
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $orderId = $response->json('data.order_id');

        // إلغاء الطلب
        $cancelResponse = $this->postJson("/api/orders/{$orderId}/cancel", [
            'reason' => 'غيرت رأيي',
        ]);

        $cancelResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $order = Order::find($orderId);
        $this->assertEquals('CANCELLED', $order->status);

        // التحقق من إطلاق المنتج
        $this->item->refresh();
        $this->assertEquals('AVAILABLE', $this->item->item_status);
    }

    /**
     * ✅ اختبار: انتهاء صلاحية الطلب تلقائياً
     */
    public function test_auto_expire_order()
    {
        // إنشاء طلب منتهي الصلاحية
        $order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'channel' => 'IN_STORE_PICKUP',
            'status' => 'CONFIRMED',
            'pickup_expiry' => now()->subMinutes(30),
        ]);

        $order->items()->create([
            'item_id' => $this->item->id,
            'operation_type' => 'SALE',
            'unit_price' => 100,
            'item_total' => 100,
        ]);

        $order->qrCode()->create([
            'qr_code' => 'QR-EXPIRE-TEST',
            'qr_data' => json_encode(['order_id' => $order->id]),
            'expiry_date' => now()->subMinutes(30),
        ]);

        // تشغيل المهمة
        $this->artisan('orders:check-expired')->assertExitCode(0);

        // التحقق من التحديثات
        $order->refresh();
        $this->assertEquals('EXPIRED', $order->status);
        $this->assertEquals('EXPIRED', $order->qrCode->status);

        $this->item->refresh();
        $this->assertEquals('AVAILABLE', $this->item->item_status);
    }

    /**
     * ✅ اختبار: حساب سعر الإيجار
     */
    public function test_calculate_rent_price()
    {
        $this->actingAs($this->customer);

        $rentItem = Item::factory()->create(['item_status' => 'AVAILABLE']);

        $response = $this->postJson('/api/orders', [
            'channel' => 'IN_STORE_PICKUP',
            'items' => [
                [
                    'item_id' => $rentItem->id,
                    'operation_type' => 'RENT',
                    'rent_days' => 5,
                    'rent_price_per_day' => 50,
                    'deposit_amount' => 200,
                ]
            ],
            'pickup_store_id' => $this->store->id,
        ]);

        $response->assertStatus(201);

        // تحقق من الحساب: (5 * 50) + 200 = 450
        $this->assertEquals(450.00, $response->json('data.total_amount'));
    }

    /**
     * ✅ اختبار: الحصول على قائمة طلبات المستخدم
     */
    public function test_get_user_orders()
    {
        // إنشاء عدة طلبات
        $this->actingAs($this->customer);

        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/orders', [
                'channel' => 'DELIVERY',
                'items' => [
                    ['item_id' => $this->item->id, 'operation_type' => 'SALE']
                ],
                'delivery_address' => "الشارع {$i}",
                'delivery_city' => 'الرياض',
            ]);
        }

        // الحصول على قائمة الطلبات
        $response = $this->getJson('/api/user/orders');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    /**
     * ✅ اختبار: الصلاحيات - العميل لا يستطيع رؤية طلبات الآخرين
     */
    public function test_customer_cannot_view_other_orders()
    {
        $otherCustomer = User::factory()->create(['user_type' => 'CUSTOMER']);

        $this->actingAs($this->customer);
        $order = Order::factory()->create(['customer_id' => $otherCustomer->id]);

        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(403);
    }

    /**
     * ✅ اختبار: إحصائيات المتجر
     */
    public function test_store_statistics()
    {
        // إنشاء عدة طلبات مكتملة
        for ($i = 0; $i < 3; $i++) {
            $order = Order::factory()->create([
                'pickup_store_id' => $this->store->id,
                'status' => 'COMPLETED',
                'total_amount' => 100,
                'pickup_actual_date' => now(),
            ]);
        }

        $this->actingAs($this->storeStaff);
        $response = $this->getJson('/api/store/statistics');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'completed_orders' => 3,
                    'total_revenue' => 300,
                ]
            ]);
    }
}
