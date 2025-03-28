<?php

namespace App\Enums\Listing\Type\Item;

enum ItemProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case PRICE = 'price';
    case CATEGORIES = 'categories';
    case CONDITION = 'condition';
    case BRAND = 'brand';
    case MODEL = 'model';
    case YEAR = 'year';
    case COLOR = 'color';
    case SIZE = 'size';
    case WEIGHT = 'weight';
    case LENGTH = 'length';
    case WIDTH = 'width';
    case HEIGHT = 'height';
    case DIAMETER = 'diameter';
    case VOLUME = 'volume';
    case MATERIAL = 'material';
    case QUANTITY = 'quantity';
    case IMAGES = 'images';
    case VIDEOS = 'videos';
    case FEATURES = 'features';
    case SPECIFICATIONS = 'specifications';
    case SHIPPING = 'shipping';
    case SHIPPING_COST = 'shipping_cost';
    case SHIPPING_METHOD = 'shipping_method';
    case SHIPPING_TIME = 'shipping_time';
    case SHIPPING_REGIONS = 'shipping_regions';
    case PAYMENT = 'payment';
    case PAYMENT_METHODS = 'payment_methods';
    case PAYMENT_TERMS = 'payment_terms';
    case PAYMENT_OPTIONS = 'payment_options';
    case PAYMENT_GATEWAY = 'payment_gateway';
    case PAYMENT_GATEWAYS = 'payment_gateways';
    case PAYMENT_GATEWAY_ID = 'payment_gateway_id';
    case PAYMENT_GATEWAY_IDS = 'payment_gateway_ids';
    case PAYMENT_GATEWAY_SECRET = 'payment_gateway_secret';
    case PAYMENT_GATEWAY_SECRETS = 'payment_gateway_secrets';
    case PAYMENT_GATEWAY_PUBLIC_KEY = 'payment_gateway_public_key';
    case PAYMENT_GATEWAY_PUBLIC_KEYS = 'payment_gateway_public_keys';
    case PAYMENT_GATEWAY_PRIVATE_KEY = 'payment_gateway_private_key';
    case PAYMENT_GATEWAY_PRIVATE_KEYS = 'payment_gateway_private_keys';
    case PAYMENT_GATEWAY_USERNAME = 'payment_gateway_username';
    case PAYMENT_GATEWAY_USERNAMES = 'payment_gateway_usernames';
    case PAYMENT_GATEWAY_PASSWORD = 'payment_gateway_password';
    case PAYMENT_GATEWAY_PASSWORDS = 'payment_gateway_passwords';
    case PAYMENT_GATEWAY_SIGNATURE = 'payment_gateway_signature';
    case PAYMENT_GATEWAY_SIGNATURES = 'payment_gateway_signatures';
}