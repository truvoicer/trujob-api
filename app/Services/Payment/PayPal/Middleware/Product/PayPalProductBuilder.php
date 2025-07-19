<?php

namespace App\Services\Payment\PayPal\Middleware\Product;

use InvalidArgumentException;

/**
 * Class PayPalProductBuilder
 *
 * A fluent builder for constructing PayPal product data arrays.
 * This helps in creating well-structured product data for API calls.
 */
class PayPalProductBuilder
{
    /**
     * @var array The product data being built.
     */
    protected array $data = [];

    /**
     * Private constructor to enforce static `build()` method usage.
     */
    private function __construct()
    {
        // Initialize with default values or empty array
        $this->data = [];
    }

    /**
     * Static factory method to start building a new product.
     *
     * @return static
     */
    public static function build(): static
    {
        return new static();
    }

    /**
     * Sets the name of the product.
     *
     * @param string $name The product name.
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Sets the ID of the product. This is often used for existing products
     * or for idempotency keys, but typically not for creation where PayPal assigns one.
     * Including it for completeness if needed for specific scenarios.
     *
     * @param string $id The product ID.
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Sets the type of the product.
     *
     * @param string $type The product type (e.g., 'DIGITAL', 'PHYSICAL', 'SERVICE').
     * @return $this
     * @throws InvalidArgumentException If the type is not valid.
     */
    public function setType(string $type): self
    {
        $validTypes = ['DIGITAL', 'PHYSICAL', 'SERVICE'];
        $type = strtoupper($type);
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException("Invalid product type '{$type}'. Must be one of: " . implode(', ', $validTypes));
        }
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * Sets the category of the product.
     *
     * @param string $category The product category (e.g., 'SOFTWARE', 'ELECTRONICS', 'EDUCATION').
     * @return $this
     */
    public function setCategory(string $category): self
    {
        $this->data['category'] = $category;
        return $this;
    }

    /**
     * Sets the description of the product.
     *
     * @param string $description The product description.
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * Sets the image URL for the product.
     *
     * @param string $imageUrl The URL of the product image.
     * @return $this
     */
    public function setImageUrl(string $imageUrl): self
    {
        $this->data['image_url'] = $imageUrl;
        return $this;
    }

    /**
     * Sets the home URL for the product.
     *
     * @param string $homeUrl The URL to the product's home page.
     * @return $this
     */
    public function setHomeUrl(string $homeUrl): self
    {
        $this->data['home_url'] = $homeUrl;
        return $this;
    }

    /**
     * Sets the product's tax category.
     *
     * @param string $taxCategory The tax category of the product.
     * @return $this
     */
    public function setTaxCategory(string $taxCategory): self
    {
        $this->data['tax_category'] = $taxCategory;
        return $this;
    }

    /**
     * Sets the product's representation (e.g., 'DIGITAL_GOODS', 'PHYSICAL_GOODS').
     *
     * @param string $representation The product representation.
     * @return $this
     */
    public function setRepresentation(string $representation): self
    {
        $this->data['representation'] = $representation;
        return $this;
    }

    /**
     * Sets the product's usage type (e.g., 'MERCHANT_INITIATED_BILLING').
     *
     * @param string $usageType The product usage type.
     * @return $this
     */
    public function setUsageType(string $usageType): self
    {
        $this->data['usage_type'] = $usageType;
        return $this;
    }

    /**
     * Sets the product's status (e.g., 'CREATED', 'ACTIVE', 'INACTIVE').
     *
     * @param string $status The product status.
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * Returns the built product data array.
     *
     * @return array
     * @throws InvalidArgumentException If required fields are missing.
     */
    public function get(): array
    {
        // Basic validation for required fields
        if (!isset($this->data['name'])) {
            throw new InvalidArgumentException("Product name is required.");
        }
        if (!isset($this->data['type'])) {
            throw new InvalidArgumentException("Product type is required.");
        }
        // if (!isset($this->data['category'])) {
        //     throw new InvalidArgumentException("Product category is required.");
        // }

        return $this->data;
    }
}
