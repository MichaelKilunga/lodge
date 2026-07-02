<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFacilityRequest;
use App\Models\Facility;
use App\Repositories\Interface\FacilityRepositoryInterface;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function __construct(
        private FacilityRepositoryInterface $facilityRepository
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->facilityRepository->getFacilitiesDatatable($request);
        }

        return view('facility.index');
    }

    public function create()
    {
        $view = view('facility.create')->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function store(StoreFacilityRequest $request)
    {
        $facility = Facility::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'icon' => $request->icon ?: 'fas fa-check',
        ]);

        return response()->json([
            'message' => 'Facility '.$facility->name.' created successfully!',
        ]);
    }

    public function edit(Facility $facility)
    {
        $view = view('facility.edit', [
            'facility' => $facility,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function update(Facility $facility, StoreFacilityRequest $request)
    {
        $facility->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'icon' => $request->icon ?: 'fas fa-check',
        ]);

        return response()->json([
            'message' => 'Facility '.$facility->name.' updated successfully!',
        ]);
    }

    public function destroy(Facility $facility)
    {
        try {
            $facility->delete();

            return response()->json([
                'message' => 'Facility '.$facility->name.' deleted successfully!',
            ]);
        } catch (\Exception $e) {
            $errorCode = isset($e->errorInfo[1]) ? $e->errorInfo[1] : $e->getCode();

            return response()->json([
                'message' => 'Facility '.$facility->name.' cannot be deleted! Error Code: '.$errorCode,
            ], 500);
        }
    }
}
