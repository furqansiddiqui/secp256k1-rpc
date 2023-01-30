# validatePrivateKey

`validatePrivateKey(privateKey: string|Byte32): boolean`

Validates a private key with `secp256k1_ec_seckey_verify` function.

## Request

| Index | Datatype        | Required | Description                                                                                                            |
|-------|-----------------|----------|------------------------------------------------------------------------------------------------------------------------|
| 0     | string (Byte32) | Yes      | A valid 32 bytes private key encoded in Hexadecimal (total 64 characters long)                                         |

## Response

Datatype: `boolean`

# createPublicKey

`createPublicKey(privateKey: string|Byte32, createBoth: boolean = false): object`

Generates public key from the private key.

## Request

| Index | Datatype        | Required | Description                                                                                                            |
|-------|-----------------|----------|------------------------------------------------------------------------------------------------------------------------|
| 0     | string (Byte32) | Yes      | A valid 32 bytes private key encoded in Hexadecimal (total 64 characters long)                                         |
| 1     | boolean         | No       | If set to `true` then response will have both compressed and uncompressed variants of public key. Defaults to `false`. |

## Response

Datatype: `object`

| Key          | Datatype      | Description                                                                            |
|--------------|---------------|----------------------------------------------------------------------------------------|
| compressed   | string        | Compressed variant of public key encoded in Hexadecimal (66 hexits = 33 raw bytes)     |
| unCompressed | string / NULL | Uncompressed variant of public key. Only returned if second argument is set to `true`. |

