<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Task::class => \App\Policies\TaskPolicy::class,
        \App\Models\Chat::class => \App\Policies\ChatPolicy::class,
        \App\Models\Message::class => \App\Policies\MessagePolicy::class,
        \App\Models\ChatAttachment::class => \App\Policies\ChatAttachmentPolicy::class,
        \App\Models\Cart::class => \App\Policies\CartPolicy::class,
        \App\Models\CartItem::class => \App\Policies\CartItemPolicy::class,
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
        \App\Models\LandingPageElement::class => \App\Policies\LandingPageElementPolicy::class,
        \App\Models\LandingPageSection::class => \App\Policies\LandingPageSectionPolicy::class,
        \App\Models\Order::class => \App\Policies\OrderPolicy::class,
        \App\Models\OrderItem::class => \App\Policies\OrderItemPolicy::class,
        \App\Models\OrderNotification::class => \App\Policies\OrderNotificationPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\ProductImage::class => \App\Policies\ProductImagePolicy::class,
        \App\Models\Rating::class => \App\Policies\RatingPolicy::class,
        \App\Models\Review::class => \App\Policies\ReviewPolicy::class,
        \App\Models\SiteSetting::class => \App\Policies\SiteSettingPolicy::class,
        \App\Models\StockMovement::class => \App\Policies\StockMovementPolicy::class,
        \App\Models\TaskImage::class => \App\Policies\TaskImagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}