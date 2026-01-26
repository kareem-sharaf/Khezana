<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderQrCode;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * QrService
 *
 * خدمة إدارة رموز QR - التوليد والتحقق والاستخدام
 */
class QrService
{
    /**
     * توليد QR Code للطلب
     *
     * @param Order $order
     * @return OrderQrCode
     */
    public function generateQrCode(Order $order): OrderQrCode
    {
        // إنشاء بيانات QR
        $qrData = $this->createQrData($order);

        // توليد الرمز الفريد
        $qrCode = $this->generateUniqueQrCode();

        // حفظ QR في قاعدة البيانات
        $orderQr = OrderQrCode::create([
            'order_id' => $order->id,
            'qr_code' => $qrCode,
            'qr_data' => json_encode($qrData),
            'status' => 'ACTIVE',
            'expiry_date' => now()->addHours(24),
        ]);

        // توليد صورة QR
        $this->generateQrImage($orderQr);

        return $orderQr;
    }

    /**
     * إنشاء بيانات QR
     *
     * @param Order $order
     * @return array
     */
    private function createQrData(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'customer_id' => $order->customer_id,
            'timestamp' => now()->timestamp,
            'store_id' => $order->pickup_store_id,
            'hash' => hash('sha256', $order->id . $order->customer_id . now()->timestamp),
        ];
    }

    /**
     * توليد رمز QR فريد
     *
     * @return string
     */
    private function generateUniqueQrCode(): string
    {
        do {
            $code = 'QR-' . Str::random(20);
        } while (OrderQrCode::where('qr_code', $code)->exists());

        return $code;
    }

    /**
     * توليد صورة QR
     *
     * @param OrderQrCode $orderQr
     */
    private function generateQrImage(OrderQrCode $orderQr): void
    {
        $data = json_decode($orderQr->qr_data, true);

        // إنشاء مسار الحفظ
        $path = storage_path('app/public/qr-codes/' . $orderQr->order_id . '.png');

        // التأكد من وجود المجلد
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // توليد الصورة
        QrCode::format('png')
            ->size(300)
            ->generate(json_encode($data), $path);
    }

    /**
     * التحقق من صحة رمز QR
     *
     * @param string $qrCode
     * @return OrderQrCode|null
     */
    public function validateQrCode(string $qrCode): ?OrderQrCode
    {
        $orderQr = OrderQrCode::where('qr_code', $qrCode)->first();

        if (!$orderQr) {
            return null;
        }

        // التحقق من الصلاحية
        if (!$orderQr->isValid()) {
            return null;
        }

        // التحقق من حالة الطلب
        if (!$orderQr->order->isReadyForPickup()) {
            return null;
        }

        return $orderQr;
    }

    /**
     * التحقق من QR مع فك التشفير
     *
     * @param string $qrData
     * @return array|null
     */
    public function decodeQrData(string $qrData): ?array
    {
        try {
            $data = json_decode($qrData, true);

            // التحقق من البيانات الأساسية
            if (!isset($data['order_id'], $data['hash'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * تعليم QR كمستخدم
     *
     * @param OrderQrCode $orderQr
     * @param int $userId
     */
    public function markQrAsUsed(OrderQrCode $orderQr, int $userId): void
    {
        $orderQr->markAsUsed($userId);
    }

    /**
     * الحصول على صورة QR
     *
     * @param Order $order
     * @return string|null
     */
    public function getQrImage(Order $order): ?string
    {
        $path = storage_path('app/public/qr-codes/' . $order->id . '.png');

        if (file_exists($path)) {
            return asset('storage/qr-codes/' . $order->id . '.png');
        }

        return null;
    }

    /**
     * تعليم QR كمنتهي الصلاحية
     *
     * @param OrderQrCode $orderQr
     */
    public function expireQrCode(OrderQrCode $orderQr): void
    {
        $orderQr->update([
            'status' => 'EXPIRED',
        ]);
    }
}
