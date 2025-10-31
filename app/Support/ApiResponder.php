<?php
declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponder
{
    public const KEY_OK      = 'ok';
    public const KEY_MESSAGE = 'message';
    public const KEY_DATA    = 'data';
    public const KEY_ERRORS  = 'errors';
    public const KEY_CODE    = 'code';

    /**
     * Respuesta de éxito uniforme.
     */
    public static function success(
        mixed $data = [],
        string $message = 'OK',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            self::KEY_OK      => true,
            self::KEY_MESSAGE => $message,
            self::KEY_DATA    => $data,
            self::KEY_ERRORS  => null,
            self::KEY_CODE    => $status,
        ], $status);
    }

    /**
     * Respuesta de error uniforme.
     */
    public static function error(
        string $message,
        int $status,
        array|null $errors = null,
        mixed $data = []
    ): JsonResponse {
        return response()->json([
            self::KEY_OK      => false,
            self::KEY_MESSAGE => $message,
            self::KEY_DATA    => $data,   // ← “información vacía” -> []
            self::KEY_ERRORS  => $errors,
            self::KEY_CODE    => $status,
        ], $status);
    }
}
