
# Portable Postal Address Medium Grained

The portable international postal address. Maps to [AddressValidationMetadata](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) and HTML 5.1 [Autofilling form controls: the autocomplete attribute](https://www.w3.org/TR/html51/sec-forms.html#autofilling-form-controls-the-autocomplete-attribute).

## Structure

`PortablePostalAddressMediumGrained`

## Fields

| Name | Type | Tags | Description | Getter | Setter |
|  --- | --- | --- | --- | --- | --- |
| `addressLine1` | `?string` | Optional | The first line of the address, such as number and street, for example, `173 Drury Lane`. Needed for data entry, and Compliance and Risk checks. This field needs to pass the full address.<br>**Constraints**: *Maximum Length*: `300` | getAddressLine1(): ?string | setAddressLine1(?string addressLine1): void |
| `addressLine2` | `?string` | Optional | The second line of the address, for example, a suite or apartment number.<br>**Constraints**: *Maximum Length*: `300` | getAddressLine2(): ?string | setAddressLine2(?string addressLine2): void |
| `adminArea2` | `?string` | Optional | A city, town, or village. Smaller than `admin_area_level_1`.<br>**Constraints**: *Maximum Length*: `120` | getAdminArea2(): ?string | setAdminArea2(?string adminArea2): void |
| `adminArea1` | `?string` | Optional | The highest-level sub-division in a country, which is usually a province, state, or ISO-3166-2 subdivision. This data is formatted for postal delivery, for example, `CA` and not `California`. Value, by country, is:<ul><li>UK. A county.</li><li>US. A state.</li><li>Canada. A province.</li><li>Japan. A prefecture.</li><li>Switzerland. A *kanton*.</li></ul><br>**Constraints**: *Maximum Length*: `300` | getAdminArea1(): ?string | setAdminArea1(?string adminArea1): void |
| `postalCode` | `?string` | Optional | The postal code, which is the ZIP code or equivalent. Typically required for countries with a postal code or an equivalent. See [postal code](https://en.wikipedia.org/wiki/Postal_code).<br>**Constraints**: *Maximum Length*: `60` | getPostalCode(): ?string | setPostalCode(?string postalCode): void |
| `countryCode` | `string` | Required | The [2-character ISO 3166-1 code](/api/rest/reference/country-codes/) that identifies the country or region.<blockquote><strong>Note:</strong> The country code for Great Britain is <code>GB</code> and not <code>UK</code> as used in the top-level domain names for that country. Use the `C2` country code for China worldwide for comparable uncontrolled price (CUP) method, bank card, and cross-border transactions.</blockquote><br>**Constraints**: *Minimum Length*: `2`, *Maximum Length*: `2`, *Pattern*: `^([A-Z]{2}\|C2)$` | getCountryCode(): string | setCountryCode(string countryCode): void |

## Example (as JSON)

```json
{
  "address_line_1": "address_line_16",
  "address_line_2": "address_line_24",
  "admin_area_2": "admin_area_22",
  "admin_area_1": "admin_area_10",
  "postal_code": "postal_code6",
  "country_code": "country_code6"
}
```
