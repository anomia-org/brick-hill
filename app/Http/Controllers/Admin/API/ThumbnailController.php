<?php

namespace App\Http\Controllers\Admin\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Thumbnails\ThumbnailController as OrigThumbnailController;

use App\Http\Requests\General\BulkThumbnail;

class ThumbnailController extends Controller
{
    /**
     * Return the bulkThumbnails api with $bypassOverwrite true
     * This ignores any model overwrites and returns the Thumbnail no matter what
     * Used to display thumbnails when pending items
     * 
     * @param \App\Http\Requests\General\BulkThumbnail $request 
     * @return \Illuminate\Support\Collection[] 
     */
    public function bulkThumbnails(BulkThumbnail $request)
    {
        return (new OrigThumbnailController)->bulkThumbnails($request, true);
    }
}
