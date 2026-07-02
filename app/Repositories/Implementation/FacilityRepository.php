<?php

namespace App\Repositories\Implementation;

use App\Models\Facility;
use App\Repositories\Interface\FacilityRepositoryInterface;
use Illuminate\Http\Request;

class FacilityRepository implements FacilityRepositoryInterface
{
    public function getFacilitiesDatatable(Request $request)
    {
        $columns = [
            0 => 'facilities.id',
            1 => 'facilities.name',
            2 => 'facilities.detail',
            3 => 'facilities.id',
        ];

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderIndex = $request->input('order.0.column', 0);
        $order = isset($columns[$orderIndex]) ? $columns[$orderIndex] : 'facilities.id';
        $dir = $request->input('order.0.dir', 'asc');

        $main_query = Facility::select(
            'facilities.id as number',
            'facilities.name',
            'facilities.icon',
            'facilities.detail',
            'facilities.id',
        );

        $totalData = $main_query->count();

        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $main_query->where(function ($query) use ($search, $columns) {
                $i = 0;
                foreach ($columns as $column) {
                    if ($i == 0) {
                        $query->where($column, 'LIKE', "%{$search}%");
                    } else {
                        $query->orWhere($column, 'LIKE', "%{$search}%");
                    }
                    $i++;
                }
            });
        }

        $totalFiltered = $main_query->count();

        $models = $main_query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($models as $model) {
            $data[] = [
                'number' => $model->id,
                'name' => $model->name,
                'icon' => $model->icon ?? 'fas fa-check',
                'detail' => $model->detail,
                'id' => $model->id,
            ];
        }

        return json_encode([
            'draw' => intval($request->input('draw', 1)),
            'iTotalRecords' => $totalData,
            'iTotalDisplayRecords' => $totalFiltered,
            'aaData' => $data,
        ]);
    }
}
