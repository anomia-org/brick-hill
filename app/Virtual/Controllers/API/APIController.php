<?php

namespace App\Virtual\Controllers\API;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="Sample API Documentation"
 * )
 * 
 * @OA\Components(
 *     @OA\Response(
 *         response="404",
 *         description="Specified id not found",
 *         @OA\JsonContent(ref="#/components/schemas/NotFound"),
 *     ),
 *     @OA\Parameter(
 *         @OA\Schema(
 *             type="enum",
 *             enum={1, 10, 25, 50, 100},
 *             default=10
 *         ),
 *         in="query",
 *         name="limit",
 *         description="Amount of results",
 *     ),
 *     @OA\Parameter(
 *         @OA\Schema(
 *             type="string",
 *         ),
 *         in="query",
 *         name="cursor",
 *         description="Paging cursor",
 *     )
 * )
 */
class APIController
{
}
