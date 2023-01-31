## Errors

| Error Code | Method(s)                                                                                                               | Details                                                 |
|------------|-------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------|
| 11001      | [createPublicKey](METHODS.md#createPublicKey), [ecdsaSign](METHODS.md#ecdsaSign), [ecdsaVerify](METHODS.md#ecdsaVerify) | Private key is not valid for ECDSA                      |
| 11101      | [createPublicKey](METHODS.md#createPublicKey)                                                                           | Failed to generate public keys                          |
| 11102      | [createPublicKey](METHODS.md#createPublicKey)                                                                           | Failed to serialize compressed/un-compressed public key |
| 11201      | [ecdsaSign](METHODS.md#ecdsaSign), [ecdsaVerify](METHODS.md#ecdsaVerify)                                                | Invalid msg32/message hash.                             |
| 11211      | [ecdsaSign](METHODS.md#ecdsaSign)                                                                                       | Failed to generate a signature                          |
| 11212      | [ecdsaSign](METHODS.md#ecdsaSign)                                                                                       | Failed to DER serialize generated signature             |

