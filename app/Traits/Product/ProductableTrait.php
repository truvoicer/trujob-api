<?php

namespace App\Traits\Product;

use App\Models\Product;

trait ProductableTrait
{

    /**
     * Generates a unique SKU for a given product.
     * The SKU format is: [PRODUCT_TYPE]-[BRAND_CODE]-[CATEGORY_CODE]-[COLOR_CODE]-[PRODUCT_ID]
     *
     * @param Product $product The product model instance.
     * @return string The generated SKU.
     */
    public function generateSku(): string
    {
        // Ensure necessary relationships are loaded to avoid N+1 queries
        $this->loadMissing(['productCategories', 'categories', 'colors', 'brands']);


        // 1. Get Product Category Code
        // Assuming a product can have multiple brands, we take the first one.
        // If no brand, use a generic code.
        $productCategoryName = $this->productCategories()->first()->name ?? null;
        $productCategoryCode = $this->getAbbreviation($productCategoryName, 'GEN'); // GEN for Generic

        // 2. Get Brand Code
        // Assuming a product can have multiple brands, we take the first one.
        // If no brand, use a generic code.
        $brandName = $this->brands->first()->name ?? null;
        $brandCode = $this->getAbbreviation($brandName, 'GEN'); // GEN for Generic

        // 3. Get Category Code
        // Assuming a product can have multiple categories, we take the first one.
        // If no category, use an uncategorized code.
        $categoryName = $this->categories->first()->name ?? null;
        $categoryCode = $this->getAbbreviation($categoryName, 'UNC'); // UNC for Uncategorized

        // 4. Get Color Code
        // Assuming a product can have multiple colors, we take the first one.
        // If no color, use a no-color code.
        $colorName = $this->colors->first()->name ?? null;
        $colorCode = $this->getAbbreviation($colorName, 'NCL'); // NCL for No Color

        // 5. Get Product ID (zero-padded for consistent length)
        // Using the product's ID ensures uniqueness for products with similar attributes.
        $productIdPadded = str_pad($this->id, 5, '0', STR_PAD_LEFT); // Pad to 5 digits, e.g., 1 -> 00001

        // Combine all parts with hyphens
        $sku = sprintf(
            "%s-%s-%s-%s-%s",
            $productCategoryCode,
            $brandCode,
            $categoryCode,
            $colorCode,
            $productIdPadded
        );

        // Convert to uppercase for consistency
        return strtoupper($sku);
    }


    /**
     * Helper to generate a 3-character abbreviation from a string,
     * or return a default code if the string is null or empty.
     *
     * @param string|null $name
     * @param string $defaultCode
     * @return string
     */
    private function getAbbreviation(?string $name, string $defaultCode): string
    {
        if (empty($name)) {
            return $defaultCode;
        }
        // Take the first 3 characters, convert to uppercase, and remove spaces/special chars if any
        return substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3) ?: $defaultCode;
    }

    /**
     * You might want to add a function to check for SKU uniqueness in your database
     * before assigning it, especially if you plan to store the SKU on the product model.
     * This is crucial if your abbreviation logic could lead to collisions for different products.
     * For example, if "Red Shoes" and "Red Shirts" both abbreviate to "RED-SH-..."
     * The product ID at the end helps, but a full check is safer.
     *
     * @param string $sku
     * @return bool
     */
    public function isSkuUnique(string $sku): bool
    {
        return !Product::where('sku', $sku)->exists();
    }
}
