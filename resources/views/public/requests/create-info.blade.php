@extends('layouts.app')

@section('title', 'اطلب لباسًا - ' . config('app.name'))

@section('content')
    <div class="khezana-request-info-page">
        <!-- Hero Section -->
        <section class="khezana-request-hero">
            <div class="khezana-container">
                <div class="khezana-request-hero-content">
                    <div class="khezana-request-icon">📝</div>
                    <h1 class="khezana-request-hero-title">اطلب لباسًا</h1>
                    <p class="khezana-request-hero-subtitle">
                        لا تجد ما تبحث عنه؟ اكتب طلبك وسيجدك الآخرون
                    </p>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section class="khezana-request-how">
            <div class="khezana-container">
                <h2 class="khezana-section-title">كيف يعمل الطلب؟</h2>
                <p class="khezana-section-subtitle">
                    عملية بسيطة في ثلاث خطوات
                </p>

                <div class="khezana-steps">
                    <div class="khezana-step">
                        <div class="khezana-step-number">1</div>
                        <h3 class="khezana-step-title">اكتب طلبك</h3>
                        <p class="khezana-step-description">
                            صف ما تحتاجه بالتفصيل: النوع، المقاس، الحالة، وأي تفاصيل أخرى
                        </p>
                    </div>

                    <div class="khezana-step">
                        <div class="khezana-step-number">2</div>
                        <h3 class="khezana-step-title">المراجعة والموافقة</h3>
                        <p class="khezana-step-description">
                            فريقنا يراجع طلبك لضمان الجودة قبل النشر
                        </p>
                    </div>

                    <div class="khezana-step">
                        <div class="khezana-step-number">3</div>
                        <h3 class="khezana-step-title">استقبل العروض</h3>
                        <p class="khezana-step-description">
                            سيقدم لك الآخرون عروض بيع، إيجار، أو تبرع حسب ما لديهم
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits -->
        <section class="khezana-request-benefits">
            <div class="khezana-container">
                <h2 class="khezana-section-title">لماذا تطلب؟</h2>
                <div class="khezana-benefits-grid">
                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">🎯</div>
                        <h3 class="khezana-benefit-title">وفر وقتك</h3>
                        <p class="khezana-benefit-text">
                            لا حاجة للبحث في مئات الإعلانات. اكتب طلبك واجعل الآخرين يجدونك
                        </p>
                    </div>

                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">💰</div>
                        <h3 class="khezana-benefit-title">خيارات متعددة</h3>
                        <p class="khezana-benefit-text">
                            قد تجد من يبيع، يؤجر، أو حتى يتبرع لك بالملابس التي تحتاجها
                        </p>
                    </div>

                    <div class="khezana-benefit-card">
                        <div class="khezana-benefit-icon">🤝</div>
                        <h3 class="khezana-benefit-title">مجتمع متعاون</h3>
                        <p class="khezana-benefit-text">
                            انضم إلى مجتمع يساعد بعضه البعض في تلبية الاحتياجات
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Examples -->
        <section class="khezana-request-examples">
            <div class="khezana-container">
                <h2 class="khezana-section-title">أمثلة على الطلبات</h2>
                <p class="khezana-section-subtitle">
                    أفكار لمساعدتك في كتابة طلبك
                </p>

                <div class="khezana-examples-grid">
                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👔</div>
                        <h3 class="khezana-example-title">طلب بسيط</h3>
                        <p class="khezana-example-text">
                            "أحتاج حذاء رجالي لمناسبة عرس، مقاس 42، بحالة جيدة"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👗</div>
                        <h3 class="khezana-example-title">طلب تفصيلي</h3>
                        <p class="khezana-example-text">
                            "أبحث عن فستان زفاف كلاسيكي، مقاس M، لون أبيض أو عاجي، للإيجار أو الشراء"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👕</div>
                        <h3 class="khezana-example-title">طلب عاجل</h3>
                        <p class="khezana-example-text">
                            "أحتاج قميص رجالي رسمي، مقاس L، للبيع أو الإيجار، عاجل للمناسبة الأسبوع القادم"
                        </p>
                    </div>

                    <div class="khezana-example-card">
                        <div class="khezana-example-icon">👶</div>
                        <h3 class="khezana-example-title">طلب للأطفال</h3>
                        <p class="khezana-example-text">
                            "أبحث عن ملابس أطفال للتبرع، مقاسات مختلفة، بحالة جيدة"
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Response Types -->
        <section class="khezana-request-response-types">
            <div class="khezana-container">
                <h2 class="khezana-section-title">كيف يمكن للآخرين الرد على طلبك؟</h2>
                <p class="khezana-section-subtitle">
                    ثلاثة أنواع من العروض يمكن أن تستقبلها
                </p>

                <div class="khezana-response-types-grid">
                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-sell">🛒</div>
                        <h3 class="khezana-response-type-title">عرض بيع</h3>
                        <p class="khezana-response-type-text">
                            قد يجدك شخص لديه ما تحتاجه ويريد بيعه لك بسعر مناسب
                        </p>
                    </div>

                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-rent">👔</div>
                        <h3 class="khezana-response-type-title">عرض إيجار</h3>
                        <p class="khezana-response-type-text">
                            مثالي للمناسبات! استأجر ما تحتاجه لوقت محدد وادفع أقل
                        </p>
                    </div>

                    <div class="khezana-response-type-card">
                        <div class="khezana-response-type-icon khezana-response-type-donate">❤️</div>
                        <h3 class="khezana-response-type-title">عرض تبرع</h3>
                        <p class="khezana-response-type-text">
                            قد يجدك متبرع كريم يريد مساعدتك مجاناً
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="khezana-request-cta">
            <div class="khezana-container">
                <div class="khezana-request-cta-content">
                    <h2 class="khezana-request-cta-title">جاهز لطلب لباسك؟</h2>
                    <p class="khezana-request-cta-text">
                        سجّل حسابك مجاناً وابدأ بطلب ما تحتاجه الآن
                    </p>
                    @auth
                        <a href="{{ route('requests.create', [], false) }}"
                            class="khezana-btn khezana-btn-primary khezana-btn-large">
                            اكتب طلبك الآن
                        </a>
                    @else
                        <a href="{{ route('register', ['redirect' => route('requests.create', [], false)], false) }}"
                            class="khezana-btn khezana-btn-primary khezana-btn-large">
                            سجل واطلب لباسًا
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Browse Existing Requests -->
        <section class="khezana-request-browse">
            <div class="khezana-container">
                <div class="khezana-request-browse-content">
                    <h2 class="khezana-section-title">تصفح الطلبات الحالية</h2>
                    <p class="khezana-section-subtitle">
                        شاهد ما يطلبه الآخرون وقدم لهم المساعدة
                    </p>
                    <a href="{{ route('public.requests.index') }}"
                        class="khezana-btn khezana-btn-secondary khezana-btn-large">
                        عرض جميع الطلبات
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
