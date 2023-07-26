<?php

namespace App\Virtual\Controllers\API\User;

/**
 *  @OA\Get(
 *      path="/v1/shop/list",
 *      operationId="getLatestItems",
 *      tags={"Shop"},
 *      summary="Returns items for the shop page",
 *      @OA\Parameter(
 *          name="sort",
 *          in="query",
 *          description="Method of sorting items",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *              enum={"updated", "newest", "oldest", "expensive", "inexpensive"},
 *              default="updated"
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
 *          description="Search for specific item name",
 *          required=false,
 *          @OA\Schema(
 *              type="string",
 *          ),
 *      ),
 *      @OA\Parameter(ref="#components/parameters/limit"),
 *      @OA\Parameter(ref="#components/parameters/cursor"),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="data", @OA\Items(ref="#/components/schemas/ItemV2"), type="array"),
 *              @OA\Property(property="next_cursor", type="string"),
 *              @OA\Property(property="previous_cursor", type="string"),
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/shop/{itemId}",
 *      operationId="getItemInfo",
 *      tags={"Shop"},
 *      summary="Returns info of an item",
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
 *              @OA\Property(property="data", ref="#/components/schemas/ItemV2")
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/shop/{itemId}/owners",
 *      operationId="getItemOwners",
 *      tags={"Shop"},
 *      summary="Returns a list of owners of an item",
 *      @OA\Parameter(
 *          name="itemId",
 *          in="path",
 *          description="The item id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Parameter(ref="#components/parameters/limit"),
 *      @OA\Parameter(ref="#components/parameters/cursor"),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="data", @OA\Items(ref="#/components/schemas/Crate"), type="array"),
 *              @OA\Property(property="next_cursor", type="string"),
 *              @OA\Property(property="previous_cursor", type="string"),
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/shop/{itemId}/resellers",
 *      operationId="getItemResellers",
 *      tags={"Shop"},
 *      summary="Returns a list of resellers of an item",
 *      @OA\Parameter(
 *          name="itemId",
 *          in="path",
 *          description="The item id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Parameter(ref="#components/parameters/limit"),
 *      @OA\Parameter(ref="#components/parameters/cursor"),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(type="object",
 *              @OA\Property(property="data", @OA\Items(ref="#/components/schemas/SpecialSeller"), type="array"),
 *              @OA\Property(property="next_cursor", type="string"),
 *              @OA\Property(property="previous_cursor", type="string"),
 *          ),
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 */
class ShopAPIController
{
}
