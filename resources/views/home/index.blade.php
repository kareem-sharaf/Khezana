@extends('layouts.home')

@section('content')
    <!-- Hero Section -->
    <section class="khezana-hero">
        <div class="khezana-container">
            <div class="khezana-hero-content">
                <h1 class="khezana-hero-title">
                    منصة موثوقة للملابس في سوريا
                </h1>
                <p class="khezana-hero-subtitle">
                    بيع، تأجير، تبرع، أو اطلب ملابس حسب حاجتك. كل شيء في مكان واحد موثوق وآمن
                </p>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="khezana-services">
        <div class="khezana-container">
            <h2 class="khezana-section-title">خدماتنا</h2>
            <p class="khezana-section-subtitle">
                اختر الخدمة التي تناسبك وابدأ الآن
            </p>
            
            <div class="khezana-services-grid">
                <!-- Buy -->
                <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">🛒</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.sell') ?? 'شراء' }}</h3>
                    <p class="khezana-service-description">
                        تصفح واشتري ملابس جديدة أو مستعملة بأسعار مناسبة. جميع الإعلانات مراجعة ومُوافق عليها
                    </p>
                </a>
                
                <!-- Rent -->
                <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">👔</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.rent') ?? 'تأجير' }}</h3>
                    <p class="khezana-service-description">
                        استأجر ملابس للمناسبات الخاصة. اختر مدة الإيجار المناسبة وادفع العربون
                    </p>
                </a>
                
                <!-- Donate -->
                <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-service-card">
                    <div class="khezana-service-icon">❤️</div>
                    <h3 class="khezana-service-title">{{ __('items.operation_types.donate') ?? 'تبرع' }}</h3>
                    <p class="khezana-service-description">
                        تبرع بملابسك للمحتاجين. ساعد الآخرين وشارك في بناء مجتمع أفضل
                    </p>
                </a>
                
                <!-- Request -->
                <a href="{{ route('public.requests.index') }}" class="khezana-service-card">
                    <div class="khezana-service-icon">📝</div>
                    <h3 class="khezana-service-title">اطلب لباسًا</h3>
                    <p class="khezana-service-description">
                        اكتب طلبك واحصل على عروض من بائعين ومتبرعين. احصل على ما تحتاجه بسهولة
                    </p>
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="khezana-how-it-works">
        <div class="khezana-container">
            <h2 class="khezana-section-title">كيف تعمل المنصة؟</h2>
            <p class="khezana-section-subtitle">
                عملية بسيطة وآمنة في ثلاث خطوات
            </p>
            
            <div class="khezana-steps">
                <div class="khezana-step">
                    <div class="khezana-step-number">1</div>
                    <h3 class="khezana-step-title">تصفح أو أضف</h3>
                    <p class="khezana-step-description">
                        تصفح الإعلانات المتاحة بدون تسجيل دخول، أو سجّل حسابك وأضف إعلانك الخاص
                    </p>
                </div>
                
                <div class="khezana-step">
                    <div class="khezana-step-number">2</div>
                    <h3 class="khezana-step-title">المراجعة والموافقة</h3>
                    <p class="khezana-step-description">
                        فريقنا يراجع كل إعلان وطلب لضمان الجودة والأمان قبل النشر
                    </p>
                </div>
                
                <div class="khezana-step">
                    <div class="khezana-step-number">3</div>
                    <h3 class="khezana-step-title">التواصل والتسليم</h3>
                    <p class="khezana-step-description">
                        تواصل مع البائع أو المتبرع واتفق على طريقة التسليم والدفع
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Items - Sell -->
    <section class="khezana-featured">
        <div class="khezana-container">
            <h2 class="khezana-section-title">إعلانات للبيع</h2>
            <p class="khezana-section-subtitle">
                تصفح أحدث الإعلانات المتاحة للشراء
            </p>
            
            @if(isset($featuredSell) && $featuredSell->count() > 0)
                <div class="khezana-items-grid">
                    @foreach($featuredSell->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}" class="khezana-item-card">
                            @if($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image" style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                @if($item->price)
                                    <div class="khezana-item-price">{{ number_format($item->price, 0) }} ل.س</div>
                                @endif
                                <span class="khezana-item-badge">{{ __('items.operation_types.sell') ?? 'بيع' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'sell']) }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                        عرض جميع إعلانات البيع
                    </a>
                </div>
            @else
                <p style="text-align: center; color: var(--khezana-text-light); padding: var(--khezana-spacing-xl) 0;">
                    لا توجد إعلانات متاحة حالياً
                </p>
            @endif
        </div>
    </section>

    <!-- Featured Items - Rent -->
    <section class="khezana-featured" style="background: var(--khezana-bg);">
        <div class="khezana-container">
            <h2 class="khezana-section-title">إعلانات للإيجار</h2>
            <p class="khezana-section-subtitle">
                استأجر ملابس للمناسبات الخاصة
            </p>
            
            @if(isset($featuredRent) && $featuredRent->count() > 0)
                <div class="khezana-items-grid">
                    @foreach($featuredRent->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}" class="khezana-item-card">
                            @if($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image" style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                @if($item->price)
                                    <div class="khezana-item-price">{{ number_format($item->price, 0) }} ل.س/يوم</div>
                                @endif
                                <span class="khezana-item-badge">{{ __('items.operation_types.rent') ?? 'إيجار' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'rent']) }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                        عرض جميع إعلانات الإيجار
                    </a>
                </div>
            @else
                <p style="text-align: center; color: var(--khezana-text-light); padding: var(--khezana-spacing-xl) 0;">
                    لا توجد إعلانات متاحة حالياً
                </p>
            @endif
        </div>
    </section>

    <!-- Featured Items - Donate -->
    <section class="khezana-featured">
        <div class="khezana-container">
            <h2 class="khezana-section-title">تبرعات متاحة</h2>
            <p class="khezana-section-subtitle">
                ملابس متاحة للتبرع مجاناً
            </p>
            
            @if(isset($featuredDonate) && $featuredDonate->count() > 0)
                <div class="khezana-items-grid">
                    @foreach($featuredDonate->take(6) as $item)
                        <a href="{{ route('public.items.show', ['id' => $item->id, 'slug' => $item->slug]) }}" class="khezana-item-card">
                            @if($item->primaryImage)
                                <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}" class="khezana-item-image" loading="lazy">
                            @else
                                <div class="khezana-item-image" style="display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    {{ __('common.ui.no_image') ?? 'لا توجد صورة' }}
                                </div>
                            @endif
                            <div class="khezana-item-content">
                                <h3 class="khezana-item-title">{{ $item->title }}</h3>
                                <span class="khezana-item-badge" style="background: var(--khezana-success); color: white;">
                                    {{ __('items.operation_types.donate') ?? 'تبرع مجاني' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="khezana-view-all">
                    <a href="{{ route('public.items.index', ['operation_type' => 'donate']) }}" class="khezana-btn khezana-btn-primary khezana-btn-large">
                        عرض جميع التبرعات
                    </a>
                </div>
            @else
                <p style="text-align: center; color: var(--khezana-text-light); padding: var(--khezana-spacing-xl) 0;">
                    لا توجد تبرعات متاحة حالياً
                </p>
            @endif
        </div>
    </section>

    <!-- Call to Action -->
    <section class="khezana-hero" style="background: linear-gradient(135deg, var(--khezana-primary) 0%, var(--khezana-primary-dark) 100%); color: white;">
        <div class="khezana-container">
            <div class="khezana-hero-content">
                <h2 class="khezana-hero-title" style="color: white;">
                    ابدأ الآن
                </h2>
                <p class="khezana-hero-subtitle" style="color: rgba(255, 255, 255, 0.9);">
                    سجّل حسابك مجاناً وابدأ بيع، تأجير، أو التبرع بملابسك
                </p>
                @auth
                    <a href="{{ route('items.create') }}" class="khezana-btn khezana-btn-large" style="background: white; color: var(--khezana-primary); margin-top: var(--khezana-spacing-lg);">
                        أضف إعلانك الآن
                    </a>
                @else
                    <div style="display: flex; gap: var(--khezana-spacing-md); justify-content: center; flex-wrap: wrap; margin-top: var(--khezana-spacing-lg);">
                        <a href="{{ route('register') }}" class="khezana-btn khezana-btn-large" style="background: white; color: var(--khezana-primary);">
                            إنشاء حساب
                        </a>
                        <a href="{{ route('public.items.index') }}" class="khezana-btn khezana-btn-large" style="background: transparent; color: white; border: 2px solid white;">
                            تصفح الإعلانات
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>
@endsection
