<?php
namespace App\Services\Payment\PayPal\Middleware;

use InvalidArgumentException;

class PayPalAddressBuilder {
    /**
     * @var array The address data being built.
     */
    protected array $data = [];

    /**
     * Private constructor to enforce static `build()` method usage.
     */
    private function __construct()
    {
        $this->data = [];
    }

    /**
     * Static factory method to start building a new address.
     *
     * @return static
     */
    public static function build(): static
    {
        return new static();
    }

    /**
     * Sets the street address.
     *
     * @param string $street The street address.
     * @return $this
     */
    public function setAddressLine1(string $addressLine1): self
    {
        $this->data['address_line_1'] = $addressLine1;
        return $this;
    }

    public function getAddressLine1(): string
    {
        return $this->data['address_line_1'] ?? '';
    }

    /**
     * Sets the second address line.
     *
     * @param string $addressLine2 The second address line.
     * @return $this
     */
    public function setAddressLine2(string $addressLine2): self
    {
        $this->data['address_line_2'] = $addressLine2;
        return $this;
    }

    public function getAddressLine2(): string
    {
        return $this->data['address_line_2'] ?? '';
    }

    /**
     * Sets the city of the address.
     *
     * @param string $city The city name.
     * @return $this
     */
    public function setAdminArea2(string $city): self
    {
        $this->data['admin_area_2'] = $city;
        return $this;
    }

    public function getAdminArea2(): string
    {
        return $this->data['admin_area_2'] ?? '';
    }

    /**
     * Sets the state of the address.
     *
     * @param string $state The state or province name.
     * @return $this
     */
    public function setAdminArea1(string $state): self
    {
        $this->data['admin_area_1'] = $state;
        return $this;
    }

    public function getAdminArea1(): string
    {
        return $this->data['admin_area_1'] ?? '';
    }

    /**
     * Sets the postal code of the address.
     *
     * @param string $postalCode The postal or ZIP code.
     * @return $this
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->data['postal_code'] = $postalCode;
        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->data['postal_code'] ?? '';
    }

    /**
     * Sets the country code of the address.
     *
     * @param string $countryCode The ISO 3166-1 alpha-2 country code (e.g., 'US').
     * @return $this
     */
    public function setCountryCode(string $countryCode): self
    {
        if (empty($countryCode) || !preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException(
                "Invalid country code format. Must be a valid ISO 3166-1 alpha-2 code."
            );
        }
        $this->data['country_code'] = $countryCode;
        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->data['country_code'] ?? '';
    }

    public function validate(): void
    {
        if (empty($this->data['address_line_1'])) {
            throw new InvalidArgumentException("Address line 1 is required.");
        }
        if (empty($this->data['admin_area_2'])) {
            throw new InvalidArgumentException("City is required.");
        }
        if (empty($this->data['admin_area_1'])) {
            throw new InvalidArgumentException("State is required.");
        }
        if (empty($this->data['postal_code'])) {
            throw new InvalidArgumentException("Postal code is required.");
        }
        if (empty($this->data['country_code'])) {
            throw new InvalidArgumentException("Country code is required.");
        }
    }

}
