
# Google Pay Card

The payment card used to fund a Google Pay payment. Can be a credit or debit card.

## Structure

`GooglePayCard`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `name` | `?string` | Optional | The card holder's name as it appears on the card.<br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `300`, *Pattern*: `^.{1,300}$` | getName(): ?string | setName(?string name): void |
| `number` | `?string` | Optional | The primary account number (PAN) for the payment card.<br>**Constraints**: *Minimum Length*: `13`, *Maximum Length*: `19`, *Pattern*: `^[0-9]{13,19}$` | getNumber(): ?string | setNumber(?string number): void |
| `expiry` | `?string` | Optional | The year and month, in ISO-8601 `YYYY-MM` date format. See [Internet date and time format](https://tools.ietf.org/html/rfc3339#section-5.6).<br>**Constraints**: *Minimum Length*: `7`, *Maximum Length*: `7`, *Pattern*: `^[0-9]{4}-(0[1-9]\|1[0-2])$` | getExpiry(): ?string | setExpiry(?string expiry): void |
| `lastDigits` | `?string` | Optional | The last digits of the payment card.<br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `4`, *Pattern*: `^[0-9]{2,4}$` | getLastDigits(): ?string | setLastDigits(?string lastDigits): void |
| `type` | [`?string(CardType)`](../../doc/models/card-type.md) | Optional | Type of card. i.e Credit, Debit and so on.<br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getType(): ?string | setType(?string type): void |
| `brand` | [`?string(CardBrand)`](../../doc/models/card-brand.md) | Optional | The card network or brand. Applies to credit, debit, gift, and payment cards.<br>**Constraints**: *Minimum Length*: `1`, *Maximum Length*: `255`, *Pattern*: `^[A-Z_]+$` | getBrand(): ?string | setBrand(?string brand): void |
| `billingAddress` | [`?PortablePostalAddressMediumGrained`](../../doc/models/portable-postal-address-medium-grained.md) | Optional | The portable international postal address. Maps to [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and HTML 5.1 [Autofilling form controls: the autocomplete attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute). | getBillingAddress(): ?PortablePostalAddressMediumGrained | setBillingAddress(?PortablePostalAddressMediumGrained billingAddress): void |

## Example (as JSON)

```json
{
  "name": "name4",
  "number": "number2",
  "expiry": "expiry2",
  "last_digits": "last_digits8",
  "type": "DEBIT"
}
```
