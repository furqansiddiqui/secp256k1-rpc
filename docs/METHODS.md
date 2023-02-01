# validatePrivateKey

Validates a private key with `secp256k1_ec_seckey_verify` function. Result will be a boolean `true` or `false`.

## Request: `array`

| Index | Datatype        | Required | Description                                                                    |
|-------|-----------------|----------|--------------------------------------------------------------------------------|
| 0     | string / Byte32 | Yes      | A valid 32 bytes private key encoded in Hexadecimal (total 64 characters long) |

## Response: `bool`

---

# createPublicKey

Generates compressed and uncompressed public keys from the private key. Return [PublicKeyObject](#PublicKeyObject).

## Request: `array`

| Index | Datatype        | Required | Description                                                                                                            |
|-------|-----------------|----------|------------------------------------------------------------------------------------------------------------------------|
| 0     | string / Byte32 | Yes      | A valid 32 bytes private key encoded in Hexadecimal (total 64 characters long)                                         |
| 1     | boolean         | No       | If set to `true` then response will have both compressed and uncompressed variants of public key. Defaults to `false`. |

## Response: `PublicKeyObject`

### PublicKeyObject

| Key          | Datatype      | Description                                                                            |
|--------------|---------------|----------------------------------------------------------------------------------------|
| compressed   | string        | Compressed variant of public key encoded in Hexadecimal (66 hexits = 33 raw bytes)     |
| unCompressed | string / NULL | Uncompressed variant of public key. Only returned if second argument is set to `true`. |

---

# ecdsaSign

Performs ECDSA signature on given message hash using given private key.

* Returns a `DER` serialized signature as `string`.
* Returns a 65 byte compact signature `{v,r,s}` when making a recoverable signature.

## Request: `array`

| Index | Datatype        | Required | Description                                                                                                       |
|-------|-----------------|----------|-------------------------------------------------------------------------------------------------------------------|
| 0     | string / Byte32 | Yes      | A valid 32 bytes private key encoded in Hexadecimal (total 64 characters long)                                    |
| 1     | string / Byte32 | Yes      | A valid 32 bytes hash (`SHA256`) of message to be signed.                                                         |
| 2     | boolean         | No       | If `true` uses `secp256k1_ecdsa_sign_recoverable` instead of `secp256k1_ecdsa_sign` function. Defaults to `true`. |

## Response: `string`

---

# ecdsaRecover

Recovers a public key from given compact recoverable signature and message hash.

## Request: `array`

| Index | Datatype           | Required | Description                                                   |
|-------|--------------------|----------|---------------------------------------------------------------|
| 0     | string / Signature | Yes      | 65 byte compact recoverable signature                         |
| 1     | string / Byte32    | Yes      | A valid 32 bytes hash (`SHA256`) that was used for signature. |

## Response: `string`

--- 

# ecdsaVerify

Verifies a DER encoded signature with given arguments

## Request: `array`

| Index | Datatype | Required | Description                                                   |
|-------|----------|----------|---------------------------------------------------------------|
| 0     | string   | Yes      | 65 byte compact recoverable signature                         |
| 1     | string   | Yes      | A valid 32 bytes hash (`SHA256`) that was used for signature. |
| 2     | string   | Yes      | A valid 32 bytes hash (`SHA256`) that was used for signature. |

## Response: `bool`

