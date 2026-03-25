<?php

namespace LimeSurvey\RemoteControl;

use HelgeSverre\Toon\Toon;
use remotecontrol_handle;
use Exception;

/**
 * Class ToonRpcServer
 *
 * Implements TOON (Token-Oriented Object Notation) server for RemoteControl API.
 * Uses HelgeSverre\Toon\Toon for serialization/deserialization.
 */
class ToonRpcServer implements RpcServerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(remotecontrol_handle $handler)
    {
        // TOON doesn't have a standard Content-Type yet, but we'll use application/toon
        // or support it if specifically requested.

        $input = file_get_contents('php://input');

        try {
            // Check if it's an empty request
            if (empty($input)) {
                throw new Exception('Empty request body');
            }

            // Decode TOON input
            $request = Toon::decode($input);

            if (!is_array($request)) {
                throw new Exception('Unable to decode TOON data');
            }

            $method = isset($request['method']) ? $request['method'] : null;
            $params = isset($request['params']) ? $request['params'] : [];
            $id = isset($request['id']) ? $request['id'] : null;

            if (!$method) {
                throw new Exception('Method not specified in TOON request');
            }

            // Execute the method
            $result = @call_user_func_array([$handler, $method], $params);

            if ($result !== false) {
                $response = [
                    'id' => $id,
                    'result' => $result,
                    'error' => null
                ];
            } else {
                $response = [
                    'id' => $id,
                    'result' => null,
                    'error' => 'unknown method or incorrect parameters'
                ];
            }
        } catch (Exception $e) {
            $response = [
                'id' => isset($request['id']) ? $request['id'] : null,
                'result' => null,
                'error' => $e->getMessage()
            ];
        }

        // Output response in TOON format
        header('Content-Type: application/toon');
        echo Toon::encode($response);

        return true;
    }
}
