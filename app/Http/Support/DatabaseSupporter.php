<?php


namespace App\Http\Support;


use App\Log;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseSupporter
{
    public function getPaginatedDBData (Builder $query, Request $request) : Collection
    {
        try
        {
            if($request->has('page')) {
                $perPage = $request->has('per_page') ? $request->per_page : 10;
                $page = $request->page;

                $offset = $perPage * ($page - 1);
                $query->offset($offset)->limit($perPage);
            }
            $data = $query->select(DB::raw('*, COUNT(*) OVER() AS total'))->get();

            return $data;
        }
        catch(\Exception $exception)
        {
            Log::error($exception);
            return null;
        }
    }
}
