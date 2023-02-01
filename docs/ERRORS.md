## Errors

| Error Code | Method(s)                                                                              | Details                                                                |
|------------|----------------------------------------------------------------------------------------|------------------------------------------------------------------------|
| 11001      | [createPublicKey](METHODS.md#createPublicKey), [ecdsaSign](METHODS.md#ecdsaSign)       | Private key is not valid for ECDSA                                     |
| 11002      | [ecdsaVerify](METHODS.md#ecdsaVerify)                                                  | Invalid uncompressed public key (65 bytes starting with `\x04` prefix) |
| 11004      | [ecdsaRecover](METHODS.md#ecdsaRecover)                                                | Invalid compact recoverable signature                                  |
| 11005      | [ecdsaVerify](METHODS.md#ecdsaVerify)                                                  | Invalid DER formatted signature                                        |
| 11006      | [ecdsaSign](METHODS.md#ecdsaSign), [ecdsaVerify](METHODS.md#ecdsaVerify)               | Invalid msg32/message hash                                             |
| 11101      | [createPublicKey](METHODS.md#createPublicKey), [ecdsaRecover](METHODS.md#ecdsaRecover) | Failed to generate public keys                                         |
| 11102      | [createPublicKey](METHODS.md#createPublicKey), [ecdsaRecover](METHODS.md#ecdsaRecover) | Failed to serialize compressed/un-compressed public key                |
| 11211      | [ecdsaSign](METHODS.md#ecdsaSign)                                                      | Failed to generate a signature                                         |
| 11212      | [ecdsaSign](METHODS.md#ecdsaSign)                                                      | Failed to DER serialize generated signature                            |
| 11241      | [ecdsaRecover](METHODS.md#ecdsaRecover)                                                | Failed to recover signature resource                                   |
| 11242      | [ecdsaRecover](METHODS.md#ecdsaRecover)                                                | Failed to retrieve a public key from signature                         |
| 11261      | [ecdsaVerify](METHODS.md#ecdsaVerify)                                                  | Failed to parse uncompressed public key                                |
| 11262      | [ecdsaVerify](METHODS.md#ecdsaVerify)                                                  | Failed to parse DER encoded signature                                  |

