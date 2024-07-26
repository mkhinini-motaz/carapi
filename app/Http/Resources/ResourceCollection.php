<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection as BaseResourceCollection;
use Illuminate\Support\Collection;

class ResourceCollection extends BaseResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $result = [ 'data' => $this->collection, ];

        if ($this->resource instanceof LengthAwarePaginator) {
            $result['total']       = $this->resource->total();
            $result['lastPage']    = $this->resource->lastPage();
            $result['currentPage'] = $this->resource->currentPage();
        }

        return $result;
    }

    /**
     * Get the resource that this resource collects.
     *
     * @return string|null
     */
    protected function collects()
    {
        $result = 'App\Http\Resources\\' . class_basename($this->resource->first()) . 'Resource';
        $result = str_replace('ResourceResource', 'Resource', $result);

        return $result;
    }

    /**
     * @param LengthAwarePaginator|Collection $data
     *
     * @return JsonResponse
     */
    public static function jsonResponse($data)
    {
        return (new self($data))->response();
    }
}