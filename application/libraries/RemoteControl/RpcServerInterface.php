<?php

namespace LimeSurvey\RemoteControl;

use remotecontrol_handle;

/**
 * Interface RpcServerInterface
 *
 * This interface defines the contract for any RPC server implementation.
 * It follows the strategy pattern to allow different formats (JSON, XML, TOON)
 * to be handled by the same controller.
 */
interface RpcServerInterface
{
    /**
     * Handles the incoming RPC request.
     *
     * @param remotecontrol_handle $handler The object containing API methods.
     * @return bool True if the request was handled, false otherwise.
     */
    public function handle(remotecontrol_handle $handler);
}
