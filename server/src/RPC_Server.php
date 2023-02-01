<?php
declare(strict_types=1);

namespace FurqanSiddiqui\Secp256k1_RPC;

use FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException;

/**
 * Class RPC_Server
 * @package FurqanSiddiqui\Secp256k1_RPC
 */
class RPC_Server
{
    /** @var int */
    public int $port;
    /** @var string|null */
    public ?string $requestId = null;

    /**
     * @param int $port
     */
    public function __construct(int $port)
    {
        $this->port = $port;
    }

    /**
     * @param array|string|int|bool $data
     * @return void
     */
    public function sendResponse($data): void
    {
        header("Content-type: application/json");
        print(json_encode([
            "jsonrpc" => "2.0",
            "id" => $this->requestId,
            "result" => $data,
        ]));

        exit; // Terminate execution
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        try {
            $this->handleRequest();
        } catch (RPCServerException $e) {
            header("Content-type: application/json");
            print(json_encode([
                "jsonrpc" => "2.0",
                "id" => $this->requestId,
                "error" => [
                    "code" => $e->getCode(),
                    "message" => $e->getMessage(),
                    "data" => $e->data
                ],
            ]));

            exit; // Terminate execution
        }
    }

    /**
     * @return void
     * @throws \FurqanSiddiqui\Secp256k1_RPC\Exception\RPCServerException
     */
    private function handleRequest(): void
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RPCServerException('Parse error', -32700);
        }

        // JSONRPC 2.0 spec
        if (!isset($input["jsonrpc"]) || $input["jsonrpc"] !== "2.0") {
            throw RPCServerException::InvalidRequest(["jsonrpc"]);
        }

        // Request Identifier
        $id = $input["id"] ?? null;
        if (!$id || !preg_match('/^\w+$/', $id)) {
            throw RPCServerException::InvalidRequest(["id"]);
        }

        $this->requestId = $id;

        // Method & Params
        $method = $input["method"] ?? null;
        if (!$method) {
            throw RPCServerException::InvalidRequest(["method"]);
        }

        $params = $input["params"] ?? null;
        if (!is_null($params) && !is_array($params)) {
            throw new RPCServerException('Invalid params', -32602);
        }

        if ($method === "ping") { // Ping-Pong test
            $this->sendResponse("pong");
            return;
        }

        $secp256k1 = new Secp256k1($this, $params);
        if (!method_exists($secp256k1, "rpc_" . $method)) {
            throw new RPCServerException('Method not found', -32601);
        }

        call_user_func([$secp256k1, "rpc_" . $method]);
    }
}
