<?php
namespace App\Enums\Block;

use App\Enums\Widget\Widget;

enum BlockType: string
{
    case HERO = 'hero-block';
    case FEATURED = 'featured-block';
    case ICON_GRID = 'icon-grid-block';
    case PRODUCTS_GRID = 'products-grid-block';
    case LOGIN = 'login-block';
    case REGISTER = 'register-block';
    case MANAGE_PAGES = 'manage-pages-block';
    case MANAGE_PRODUCTS = 'manage-products-block';
    case MANAGE_SIDEBARS = 'manage-sidebars-block';
    case MANAGE_WIDGETS = 'manage-widgets-block';
    case MANAGE_MENUS = 'manage-menus-block';
    case MANAGE_USERS = 'manage-users-block';
    case MANAGE_COLORS = 'manage-colors-block';
    case MANAGE_BRANDS = 'manage-brands-block';
    case MANAGE_CATEGORIES = 'manage-categories-block';
    case MANAGE_PRODUCT_CATEGORIES = 'manage-product-categories-block';
    case MANAGE_FEATURES = 'manage-features-block';
    case MANAGE_REVIEWS = 'manage-reviews-block';
    case MANAGE_SITE_SETTINGS = 'manage-site-settings-block';
    case MANAGE_PAYMENT_METHODS = 'manage-payment-methods-block';
    case MANAGE_PAYMENT_GATEWAYS = 'manage-payment-gateways-block';
    case MANAGE_TAX_RATES = 'manage-tax-rates-block';
    case MANAGE_DISCOUNTS = 'manage-discounts-block';
    case MANAGE_SHIPPING_METHODS = 'manage-shipping-methods-block';
    case MANAGE_SHIPPING_ZONES = 'manage-shipping-zones-block';

    case MANAGE_ADDRESSES = 'manage-addresses-block';

    public function isAdminBlock(): bool
    {
        return match ($this) {
            BlockType::MANAGE_PAGES,
            BlockType::MANAGE_PRODUCTS,
            BlockType::MANAGE_SIDEBARS,
            BlockType::MANAGE_WIDGETS,
            BlockType::MANAGE_USERS,
            BlockType::MANAGE_COLORS,
            BlockType::MANAGE_BRANDS,
            BlockType::MANAGE_CATEGORIES,
            BlockType::MANAGE_FEATURES,
            BlockType::MANAGE_PRODUCT_CATEGORIES,
            BlockType::MANAGE_REVIEWS,
            BlockType::MANAGE_SITE_SETTINGS,
            BlockType::MANAGE_PAYMENT_METHODS,
            BlockType::MANAGE_PAYMENT_GATEWAYS,
            BlockType::MANAGE_MENUS => true,
            default => false,
        };
    }

    public function getSidebarWidgets(): array
    {
        return match ($this) {
            BlockType::PRODUCTS_GRID => Widget::cases(),
            default => [],
        };
    }
}
