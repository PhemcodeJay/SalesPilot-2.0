<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models;

use Core\Utils\CoreHelper;
use Exception;
use stdClass;

/**
 * The card network or brand. Applies to credit, debit, gift, and payment cards.
 */
class CardBrand
{
    public const VISA = 'VISA';

    public const MASTERCARD = 'MASTERCARD';

    public const DISCOVER = 'DISCOVER';

    public const AMEX = 'AMEX';

    public const SOLO = 'SOLO';

    public const JCB = 'JCB';

    public const STAR = 'STAR';

    public const DELTA = 'DELTA';

    public const SWITCH_ = 'SWITCH';

    public const MAESTRO = 'MAESTRO';

    public const CB_NATIONALE = 'CB_NATIONALE';

    public const CONFIGOGA = 'CONFIGOGA';

    public const CONFIDIS = 'CONFIDIS';

    public const ELECTRON = 'ELECTRON';

    public const CETELEM = 'CETELEM';

    public const CHINA_UNION_PAY = 'CHINA_UNION_PAY';

    public const DINERS = 'DINERS';

    public const ELO = 'ELO';

    public const HIPER = 'HIPER';

    public const HIPERCARD = 'HIPERCARD';

    public const RUPAY = 'RUPAY';

    public const GE = 'GE';

    public const SYNCHRONY = 'SYNCHRONY';

    public const EFTPOS = 'EFTPOS';

    public const UNKNOWN = 'UNKNOWN';

    private const _ALL_VALUES = [
        self::VISA,
        self::MASTERCARD,
        self::DISCOVER,
        self::AMEX,
        self::SOLO,
        self::JCB,
        self::STAR,
        self::DELTA,
        self::SWITCH_,
        self::MAESTRO,
        self::CB_NATIONALE,
        self::CONFIGOGA,
        self::CONFIDIS,
        self::ELECTRON,
        self::CETELEM,
        self::CHINA_UNION_PAY,
        self::DINERS,
        self::ELO,
        self::HIPER,
        self::HIPERCARD,
        self::RUPAY,
        self::GE,
        self::SYNCHRONY,
        self::EFTPOS,
        self::UNKNOWN
    ];

    /**
     * Ensures that all the given values are present in this Enum.
     *
     * @param array|stdClass|null|string $value Value or a list/map of values to be checked
     *
     * @return array|null|string Input value(s), if all are a part of this Enum
     *
     * @throws Exception Throws exception if any given value is not in this Enum
     */
    public static function checkValue($value)
    {
        $value = json_decode(json_encode($value), true); // converts stdClass into array
        if (CoreHelper::checkValueOrValuesInList($value, self::_ALL_VALUES)) {
            return $value;
        }
        throw new Exception("$value is invalid for CardBrand.");
    }
}
