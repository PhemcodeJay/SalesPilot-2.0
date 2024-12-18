<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models\Builders;

use Core\Utils\CoreHelper;
use PaypalServerSdkLib\Models\GiropayPaymentObject;

/**
 * Builder for model GiropayPaymentObject
 *
 * @see GiropayPaymentObject
 */
class GiropayPaymentObjectBuilder
{
    /**
     * @var GiropayPaymentObject
     */
    private $instance;

    private function __construct(GiropayPaymentObject $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Initializes a new giropay payment object Builder object.
     */
    public static function init(): self
    {
        return new self(new GiropayPaymentObject());
    }

    /**
     * Sets name field.
     */
    public function name(?string $value): self
    {
        $this->instance->setName($value);
        return $this;
    }

    /**
     * Sets country code field.
     */
    public function countryCode(?string $value): self
    {
        $this->instance->setCountryCode($value);
        return $this;
    }

    /**
     * Sets bic field.
     */
    public function bic(?string $value): self
    {
        $this->instance->setBic($value);
        return $this;
    }

    /**
     * Initializes a new giropay payment object object.
     */
    public function build(): GiropayPaymentObject
    {
        return CoreHelper::clone($this->instance);
    }
}
