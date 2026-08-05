<?php

namespace App\Repositories\Implementation;

use App\Models\Room;
use App\Repositories\Interface\ReservationRepositoryInterface;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getUnocuppiedroom($request, $occupiedRoomId)
    {
        $perPage = $request->input('per_page', 12);
        if ($perPage === 'all') {
            $perPage = 1000;
        }

        $sortColumn = strtolower($request->sort_name ?? 'number');
        if (!in_array($sortColumn, ['price', 'number', 'capacity'])) {
            $sortColumn = 'number';
        }
        $sortType = strtolower($request->sort_type ?? 'asc') === 'desc' ? 'desc' : 'asc';

        return Room::with('type', 'roomStatus')
            ->whereNotIn('id', $occupiedRoomId)
            ->when(!empty($request->search), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('type', function ($q2) use ($request) {
                          $q2->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            })
            ->when(!empty($request->type_id), function ($query) use ($request) {
                $query->where('type_id', $request->type_id);
            })
            ->orderBy($sortColumn, $sortType)
            ->paginate($perPage);
    }

    public function countUnocuppiedroom($request, $occupiedRoomId)
    {
        return Room::with('type', 'roomStatus')
            ->whereNotIn('id', $occupiedRoomId)
            ->when(!empty($request->search), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('number', 'like', '%' . $request->search . '%')
                      ->orWhereHas('type', function ($q2) use ($request) {
                          $q2->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            })
            ->when(!empty($request->type_id), function ($query) use ($request) {
                $query->where('type_id', $request->type_id);
            })
            ->count();
    }
}
