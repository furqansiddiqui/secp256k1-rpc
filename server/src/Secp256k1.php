<?php
declare(strict_types=1);

namespace FurqanSiddiqui\Secp256k1_RPC;

use FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException;

/**
 * Class Secp256k1
 * @package FurqanSiddiqui\Secp256k1_RPC
 */
class Secp256k1
{
    /** @var \FurqanSiddiqui\Secp256k1_RPC\RPC_Server */
    private RPC_Server $server;
    /** @var array */
    private array $params;

    /**
     * @param \FurqanSiddiqui\Secp256k1_RPC\RPC_Server $server
     * @param array|null $params
     */
    public function __construct(RPC_Server $server, ?array $params = null)
    {
        $this->server = $server;
        $this->params = $params ?? [];
    }

    /**
     * @return void
     */
    public function rpc_validatePrivateKey(): void
    {
        try {
            $privateKey = Validator::validatePrivateKey($this->params[0] ?? null);
        } catch (\Exception $e) {
            $this->server->sendResponse(false);
            return;
        }

        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        $this->server->sendResponse(@secp256k1_ec_seckey_verify($context, pack("H*", $privateKey)) === 1);
    }

    /**
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public function rpc_createPublicKey(): void
    {
        $privateKey = Validator::validatePrivateKey($this->params[0] ?? null);
        $getUncompressed = isset($this->params[1]) && $this->params[1] === true;
        $prvBytes = pack("H*", $privateKey);
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        if (@secp256k1_ec_seckey_verify($context, $prvBytes) !== 1) {
            throw new RPCServerException('Private key is not valid for Secp256k1', 11001);
        }

        /** @var null|resource $publicKey */
        $publicKey = null;
        if (@secp256k1_ec_pubkey_create($context, $publicKey, $prvBytes) !== 1) {
            throw new RPCServerException('Failed to generate public keys', 11101, ["trace" => "secp256k1_ec_pubkey_create"]);
        }

        $this->serializeFromPubKeyResource($context, $publicKey, $getUncompressed);
    }

    /**
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public function rpc_ecdsaSign(): void
    {
        $isRecoverable = $this->params[2] ?? null;
        if (!is_bool($isRecoverable)) {
            $isRecoverable = true;
        }

        $privateKey = Validator::validatePrivateKey($this->params[0] ?? null);
        $msg32 = Validator::validateMsg32($this->params[1] ?? null);
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);
        /** @var null|resource $signature */
        $signature = null;
        if ($isRecoverable) {
            if (@secp256k1_ecdsa_sign_recoverable($context, $signature, pack("H*", $msg32), pack("H*", $privateKey)) !== 1) {
                throw new RPCServerException(
                    'Failed to generate ECDSA recoverable signature',
                    11211,
                    ["trace" => "secp256k1_ecdsa_sign_recoverable"]
                );
            }
        } else {
            if (@secp256k1_ecdsa_sign($context, $signature, pack("H*", $msg32), pack("H*", $privateKey), "secp256k1_nonce_function_rfc6979", null) !== 1) {
                throw new RPCServerException(
                    'Failed to generate ECDSA signature',
                    11211,
                    ["trace" => "secp256k1_ecdsa_sign"]
                );
            }
        }

        /** @var string|null $serialized */
        $serialized = null;
        /** @var int|null $recId */
        $recId = null;
        if ($isRecoverable) {
            if (@secp256k1_ecdsa_recoverable_signature_serialize_compact($context, $serialized, $recId, $signature) !== 1) {
                throw new RPCServerException(
                    'Failed to serialize compact recoverable ECDSA signature',
                    11212,
                    ["trace" => "secp256k1_ecdsa_recoverable_signature_serialize_compact"]
                );
            }

            $this->server->sendResponse(unpack("H*", chr($recId) . $serialized)[1]);
            return;
        }

        if (@secp256k1_ecdsa_signature_serialize_der($context, $serialized, $signature) !== 1) {
            throw new RPCServerException(
                'Failed to DER serialize ECDSA signature',
                11212,
                ["trace" => "secp256k1_ecdsa_signature_serialize_der"]
            );
        }

        $this->server->sendResponse(unpack("H*", $serialized)[1]);
    }

    /**
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public function rpc_ecdsaRecover(): void
    {
        $signature = pack("H*", Validator::validateCompactSignature($this->params[0] ?? null));
        $recId = ord($signature[0]);
        $signature = substr($signature, 1);
        $msg32 = Validator::validateMsg32($this->params[1] ?? null);
        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        /** @var null|resource $recoverableSignature */
        $recoverableSignature = null;
        if (@secp256k1_ecdsa_recoverable_signature_parse_compact($context, $recoverableSignature, $signature, $recId) !== 1) {
            throw new RPCServerException(
                'Failed to recover signature resource',
                11241,
                ["trace" => "secp256k1_ecdsa_recoverable_signature_parse_compact"]
            );
        }

        /** @var null|resource $publicKey */
        $publicKey = null;
        if (@secp256k1_ecdsa_recover($context, $publicKey, $recoverableSignature, pack("H*", $msg32)) !== 1) {
            throw new RPCServerException(
                'Failed to retrieve a public key from signature',
                11242,
                ["trace" => "secp256k1_ecdsa_recover"]
            );
        }

        $this->serializeFromPubKeyResource($context, $publicKey, true);
    }

    /**
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public function rpc_ecdsaVerify(): void
    {
        $public = pack("H*", Validator::validateUncompressedPublicKey($this->params[0] ?? null));
        $sign = pack("H*", Validator::validateDERSignature($this->params[1] ?? null));
        $msg32 = pack("H*", Validator::validateMsg32($this->params[2] ?? null));

        $context = secp256k1_context_create(SECP256K1_CONTEXT_SIGN | SECP256K1_CONTEXT_VERIFY);

        /** @var null|resource $publicKey */
        $publicKey = null;
        if (@secp256k1_ec_pubkey_parse($context, $publicKey, $public) !== 1) {
            throw new RPCServerException(
                'Failed to parse uncompressed public key',
                11261,
                ["trace" => "secp256k1_ec_pubkey_parse"]
            );
        }

        /** @var null|resource $signature */
        $signature = null;
        if (@secp256k1_ecdsa_signature_parse_der($context, $signature, $sign) !== 1) {
            throw new RPCServerException(
                'Failed to parse DER encoded signature',
                11262,
                ["trace" => "secp256k1_ecdsa_signature_parse_der"]
            );
        }

        $this->server->sendResponse(@secp256k1_ecdsa_verify($context, $signature, $msg32, $publicKey) === 1);
    }

    /**
     * @param $context
     * @param $publicKeyResource
     * @param bool $getUncompressed
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    private function serializeFromPubKeyResource($context, $publicKeyResource, bool $getUncompressed): void
    {
        $compressed = "";
        if (@secp256k1_ec_pubkey_serialize($context, $compressed, $publicKeyResource, SECP256K1_EC_COMPRESSED) !== 1) {
            throw new RPCServerException(
                'Failed to serialize compressed public key',
                11102,
                ["trace" => "secp256k1_ec_pubkey_serialize", "flag" => "SECP256K1_EC_COMPRESSED"]
            );
        }

        $unCompressed = "";
        if ($getUncompressed) {
            if (@secp256k1_ec_pubkey_serialize($context, $unCompressed, $publicKeyResource, SECP256K1_EC_UNCOMPRESSED) !== 1) {
                throw new RPCServerException(
                    'Failed to serialize uncompressed public key',
                    11102,
                    ["trace" => "secp256k1_ec_pubkey_serialize", "flag" => "SECP256K1_EC_UNCOMPRESSED"]
                );
            }
        }

        $this->server->sendResponse([
            "compressed" => unpack("H*", $compressed)[1],
            "unCompressed" => $unCompressed ? unpack("H*", $unCompressed)[1] : null
        ]);
    }
}
