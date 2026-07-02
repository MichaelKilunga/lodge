<?php

namespace App\Repositories\Interface;

use Illuminate\Http\Request;

interface FacilityRepositoryInterface
{
    public function getFacilitiesDatatable(Request $request);
}
