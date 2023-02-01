<?php
declare(strict_types=1);

namespace FurqanSiddiqui\Secp256k1_RPC\Exception;

/**
 * Class RPCServerException
 * @package FurqanSiddiqui\Secp256k1_RPC\Exception
 */
class RPCServerException extends \Exception
{
    /** @var array|null */
    public ?array $data = null;

    /**
     * @param string $message
     * @param int $code
     * @param array|null $data
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, ?array $data = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    /**
     * @return static
     */
    public static function InvalidRequest(?array $data = null): self
    {
        return new static("Invalid JSON request", -32600, $data);
    }
}
