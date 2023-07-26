<?php

namespace App\Virtual\Controllers\API\User;

/**
 *  @OA\Get(
 *      path="/v1/user/trades/{userId}/{type}",
 *      operationId="getMyTrades",
 *      tags={"Trade"},
 *      summary="Returns trades based on authenticated user",
 *      @OA\Parameter(
 *          name="userId",
 *          in="path",
 *          description="The user id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer"
 *          ),
 *      ),
 *      @OA\Parameter(
 *          name="type",
 *          in="path",
 *          description="Sort type of trades",
 *          required=true,
 *          @OA\Schema(
 *              type="string",
 *              enum={"inbound", "outbound", "history", "accepted", "declined"},
 *              default="inbound"
 *          ),
 *      ),
 *      @OA\Parameter(ref="#components/parameters/limit"),
 *      @OA\Parameter(ref="#components/parameters/cursor"),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 * 
 *  @OA\Get(
 *      path="/v1/user/trades/{tradeId}",
 *      operationId="getTradeInfo",
 *      tags={"Trade"},
 *      summary="Returns trade information",
 *      @OA\Parameter(
 *          name="tradeId",
 *          in="path",
 *          description="The trade id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer",
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *      ),
 *      @OA\Response(response="404", ref="#/components/responses/404"),
 *  )
 */
class TradeAPIController
{
}
