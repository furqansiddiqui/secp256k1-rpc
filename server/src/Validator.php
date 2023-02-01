<?php
declare(strict_types=1);

namespace FurqanSiddiqui\Secp256k1_RPC;

use FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException;

/**
 * Class Validator
 * @package FurqanSiddiqui\Secp256k1_RPC
 */
class Validator
{
    /**
     * @param $input
     * @return string
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public static function validatePrivateKey($input): string
    {
        if (is_string($input) && preg_match('/^[a-f0-9]{64}$/i', $input)) {
            return $input;
        }

        throw new RPCServerException('Invalid private key', 11001);
    }

    /**
     * @param $input
     * @return string
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public static function validateMsg32($input): string
    {
        if (is_string($input) && preg_match('/^[a-f0-9]{64}$/i', $input)) {
            return $input;
        }

        throw new RPCServerException('Invalid msg32 hash', 11006);
    }

    /**
     * @param $input
     * @return string
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public static function validateCompactSignature($input): string
    {
        if (is_string($input) && preg_match('/^[a-f0-9]{130}$/i', $input)) {
            return $input;
        }

        throw new RPCServerException('Invalid compact signature', 11004);
    }

    /**
     * @param $input
     * @return string
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public static function validateDERSignature($input): string
    {
        if (is_string($input) && preg_match('/^30[a-f0-9]{8,}$/i', $input)) {
            return $input;
        }

        throw new RPCServerException('Invalid DER signature', 11005);
    }

    /**
     * @param $input
     * @return string
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    public static function validateUncompressedPublicKey($input): string
    {
        if (is_string($input) && preg_match('/^04[a-f0-9]{128}$/i', $input)) {
            return $input;
        }

        throw new RPCServerException('Invalid uncompressed public key', 11002);
    }
}
