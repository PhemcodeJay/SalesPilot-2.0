<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models\Builders;

use Core\Utils\CoreHelper;
use PaypalServerSdkLib\Models\Token;

/**
 * Builder for model Token
 *
 * @see Token
 */
class TokenBuilder
{
    /**
     * @var Token
     */
    private $instance;

    private function __construct(Token $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Initializes a new token Builder object.
     */
    public static function init(string $id, string $type): self
    {
        return new self(new Token($id, $type));
    }

    /**
     * Initializes a new token object.
     */
    public function build(): Token
    {
        return CoreHelper::clone($this->instance);
    }
}