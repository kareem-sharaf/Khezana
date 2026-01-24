# Phase 5.4: Failover (Cache / Queue)

## الهدف
التعامل مع فشل Redis (أو غيره) عند استخدامه للـ Cache أو Queue.

## الإجراء اليدوي (Manual Failover)

عند تعطّل Redis:

1. **Cache**: غيّر `CACHE_STORE` في `.env` من `redis` إلى `database` أو `file`:
   ```env
   CACHE_STORE=database
   ```

2. **Queue**: غيّر `QUEUE_CONNECTION` من `redis` إلى `database` أو `sync`:
   ```env
   QUEUE_CONNECTION=database
   ```

3. تشغيل migrations إذا استخدمت `database` ولم تكن الجداول موجودة:
   ```bash
   php artisan migrate
   ```

## ملاحظات

- لا يوجد failover تلقائي في الكود؛ الخيار أعلاه يدوي عند الحاجة.
- يُنصح بعدم استخدام `sync` للـ Queue في الإنتاج إلا كحل مؤقت.
- لتفعيل failover تلقائي لاحقاً يمكن إضافة wrapper حول Cache/Queue يحاول Redis ثم يتراجع إلى `database`/`file` عند الفشل.

---
**التاريخ**: يناير 2026
