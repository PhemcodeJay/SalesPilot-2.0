<?php

declare(strict_types=1);

/*
 * PaypalServerSdkLib
 *
 * This file was automatically generated by APIMATIC v3.0 ( https://www.apimatic.io ).
 */

namespace PaypalServerSdkLib\Models;

use stdClass;

/**
 * Customizes the payer experience during the approval process for payment with PayPal.
 * <blockquote><strong>Note:</strong> Partners and Marketplaces might configure <code>brand_name</code>
 * and <code>shipping_preference</code> during partner account setup, which overrides the request
 * values.</blockquote>
 */
class PaypalWalletExperienceContext implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private $brandName;

    /**
     * @var string|null
     */
    private $locale;

    /**
     * @var string|null
     */
    private $shippingPreference = ShippingPreference::GET_FROM_FILE;

    /**
     * @var string|null
     */
    private $returnUrl;

    /**
     * @var string|null
     */
    private $cancelUrl;

    /**
     * @var string|null
     */
    private $landingPage = PaypalExperienceLandingPage::NO_PREFERENCE;

    /**
     * @var string|null
     */
    private $userAction = PaypalExperienceUserAction::CONTINUE_;

    /**
     * @var string|null
     */
    private $paymentMethodPreference = PayeePaymentMethodPreference::UNRESTRICTED;

    /**
     * Returns Brand Name.
     * The label that overrides the business name in the PayPal account on the PayPal site. The pattern is
     * defined by an external party and supports Unicode.
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * Sets Brand Name.
     * The label that overrides the business name in the PayPal account on the PayPal site. The pattern is
     * defined by an external party and supports Unicode.
     *
     * @maps brand_name
     */
    public function setBrandName(?string $brandName): void
    {
        $this->brandName = $brandName;
    }

    /**
     * Returns Locale.
     * The [language tag](https://tools.ietf.org/html/bcp47#section-2) for the language in which to
     * localize the error-related strings, such as messages, issues, and suggested actions. The tag is made
     * up of the [ISO 639-2 language code](https://www.loc.gov/standards/iso639-2/php/code_list.php), the
     * optional [ISO-15924 script tag](https://www.unicode.org/iso15924/codelists.html), and the [ISO-3166
     * alpha-2 country code](/api/rest/reference/country-codes/) or [M49 region code](https://unstats.un.
     * org/unsd/methodology/m49/).
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * Sets Locale.
     * The [language tag](https://tools.ietf.org/html/bcp47#section-2) for the language in which to
     * localize the error-related strings, such as messages, issues, and suggested actions. The tag is made
     * up of the [ISO 639-2 language code](https://www.loc.gov/standards/iso639-2/php/code_list.php), the
     * optional [ISO-15924 script tag](https://www.unicode.org/iso15924/codelists.html), and the [ISO-3166
     * alpha-2 country code](/api/rest/reference/country-codes/) or [M49 region code](https://unstats.un.
     * org/unsd/methodology/m49/).
     *
     * @maps locale
     */
    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * Returns Shipping Preference.
     * The location from which the shipping address is derived.
     */
    public function getShippingPreference(): ?string
    {
        return $this->shippingPreference;
    }

    /**
     * Sets Shipping Preference.
     * The location from which the shipping address is derived.
     *
     * @maps shipping_preference
     */
    public function setShippingPreference(?string $shippingPreference): void
    {
        $this->shippingPreference = $shippingPreference;
    }

    /**
     * Returns Return Url.
     * Describes the URL.
     */
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
    }

    /**
     * Sets Return Url.
     * Describes the URL.
     *
     * @maps return_url
     */
    public function setReturnUrl(?string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * Returns Cancel Url.
     * Describes the URL.
     */
    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }

    /**
     * Sets Cancel Url.
     * Describes the URL.
     *
     * @maps cancel_url
     */
    public function setCancelUrl(?string $cancelUrl): void
    {
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * Returns Landing Page.
     * The type of landing page to show on the PayPal site for customer checkout.
     */
    public function getLandingPage(): ?string
    {
        return $this->landingPage;
    }

    /**
     * Sets Landing Page.
     * The type of landing page to show on the PayPal site for customer checkout.
     *
     * @maps landing_page
     */
    public function setLandingPage(?string $landingPage): void
    {
        $this->landingPage = $landingPage;
    }

    /**
     * Returns User Action.
     * Configures a <strong>Continue</strong> or <strong>Pay Now</strong> checkout flow.
     */
    public function getUserAction(): ?string
    {
        return $this->userAction;
    }

    /**
     * Sets User Action.
     * Configures a <strong>Continue</strong> or <strong>Pay Now</strong> checkout flow.
     *
     * @maps user_action
     */
    public function setUserAction(?string $userAction): void
    {
        $this->userAction = $userAction;
    }

    /**
     * Returns Payment Method Preference.
     * The merchant-preferred payment methods.
     */
    public function getPaymentMethodPreference(): ?string
    {
        return $this->paymentMethodPreference;
    }

    /**
     * Sets Payment Method Preference.
     * The merchant-preferred payment methods.
     *
     * @maps payment_method_preference
     */
    public function setPaymentMethodPreference(?string $paymentMethodPreference): void
    {
        $this->paymentMethodPreference = $paymentMethodPreference;
    }

    /**
     * Encode this object to JSON
     *
     * @param bool $asArrayWhenEmpty Whether to serialize this model as an array whenever no fields
     *        are set. (default: false)
     *
     * @return array|stdClass
     */
    #[\ReturnTypeWillChange] // @phan-suppress-current-line PhanUndeclaredClassAttribute for (php < 8.1)
    public function jsonSerialize(bool $asArrayWhenEmpty = false)
    {
        $json = [];
        if (isset($this->brandName)) {
            $json['brand_name']                = $this->brandName;
        }
        if (isset($this->locale)) {
            $json['locale']                    = $this->locale;
        }
        if (isset($this->shippingPreference)) {
            $json['shipping_preference']       = ShippingPreference::checkValue($this->shippingPreference);
        }
        if (isset($this->returnUrl)) {
            $json['return_url']                = $this->returnUrl;
        }
        if (isset($this->cancelUrl)) {
            $json['cancel_url']                = $this->cancelUrl;
        }
        if (isset($this->landingPage)) {
            $json['landing_page']              = PaypalExperienceLandingPage::checkValue($this->landingPage);
        }
        if (isset($this->userAction)) {
            $json['user_action']               = PaypalExperienceUserAction::checkValue($this->userAction);
        }
        if (isset($this->paymentMethodPreference)) {
            $json['payment_method_preference'] =
                PayeePaymentMethodPreference::checkValue(
                    $this->paymentMethodPreference
                );
        }

        return (!$asArrayWhenEmpty && empty($json)) ? new stdClass() : $json;
    }
}