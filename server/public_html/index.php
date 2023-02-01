<?php
declare(strict_types=1);

require "../vendor/autoload.php";

(new \FurqanSiddiqui\Secp256k1_RPC\RPC_Server(intval(getenv("PORT"))))
    ->listen();
