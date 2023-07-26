<?php

namespace App\Virtual\Controllers\API\User;

/**
 *  @OA\Get(
 *      path="/v1/user/profile",
 *      operationId="getUserInfo",
 *      tags={"User"},
 *      summary="Returns info of a user",
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
 *          description="Success",
 *          @OA\JsonContent(ref="#/components/schemas/User"),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/user/id",
 *      operationId="getUserFromUsername",
 *      tags={"User"},
 *      summary="Returns id of a user from username",
 *      @OA\Parameter(
 *          name="username",
 *          in="query",
 *          description="The username",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(ref="#/components/schemas/UserSmall"),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/user/{userId}/crate",
 *      operationId="getCrate",
 *      tags={"User"},
 *      summary="Returns items that a user owns",
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          description="The user id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Parameter(
 *          name="type",
 *          in="query",
 *          description="Name of item type to return",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *          ),
 *      ),
 *      @OA\Parameter(
 *          name="search",
 *          in="query",
 *          description="Name of item to search for",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *          ),
 *      ),
 *      @OA\Parameter(ref="#components/parameters/limit"),
 *      @OA\Parameter(ref="#components/parameters/cursor"),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/user/{userId}/owns/{itemId}",
 *      operationId="getOwns",
 *      tags={"User"},
 *      summary="Returns whether a user owns an item or not",
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          description="The user id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Parameter(
 *          name="itemId",
 *          in="path",
 *          description="The item id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="owns", type="boolean"),
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/user/{userId}/value",
 *      operationId="getValue",
 *      tags={"User"},
 *      summary="Returns a users value, calculated daily",
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          description="The user id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 */
class UserAPIController
{
}
