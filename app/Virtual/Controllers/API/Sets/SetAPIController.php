<?php

namespace App\Virtual\Controllers\API\Sets;

/**
 *  @OA\Get(
 *      path="/v1/games/retrieveAvatar",
 *      operationId="getAvatarInfo",
 *      tags={"Sets"},
 *      summary="Returns avatar info",
 *      @OA\Parameter(
 *          name="id",
 *          in="query",
 *          description="The user id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success"
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/sets/{setId}",
 *      operationId="getSetInfo",
 *      tags={"Sets"},
 *      summary="Returns info of an set",
 *      @OA\Parameter(
 *          name="setId",
 *          in="path",
 *          description="The set id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="data", ref="#/components/schemas/Set")
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 */
class SetAPIController
{
}
