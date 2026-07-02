@extends('template.master')
@section('title', 'Facility Management')
@section('content')
    <div class="container-fluid">
        <!-- Add Facility Button -->
        <div class="row mb-4">
            <div class="col-12">
                <button id="add-button" type="button" class="add-facility-btn">
                    <i class="fas fa-plus"></i>
                    Add New Facility
                </button>
            </div>
        </div>

        <!-- Professional Table Container -->
        <div class="professional-table-container">
            <!-- Table Header -->
            <div class="table-header">
                <h4><i class="fas fa-concierge-bell me-2"></i>Facility Management</h4>
                <p>Manage hotel amenities, services, and icons displayed across room profiles</p>
            </div>

            <!-- Professional Table -->
            <div class="table-responsive">
                <table id="facility-table" class="professional-table table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">
                                <i class="fas fa-hashtag me-1"></i>#
                            </th>
                            <th scope="col">
                                <i class="fas fa-icons me-1"></i>Icon preview
                            </th>
                            <th scope="col">
                                <i class="fas fa-tag me-1"></i>Name
                            </th>
                            <th scope="col">
                                <i class="fas fa-info-circle me-1"></i>Detail / Description
                            </th>
                            <th scope="col">
                                <i class="fas fa-cog me-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTable will populate this -->
                    </tbody>
                </table>
            </div>

            <!-- Table Footer -->
            <div class="table-footer">
                <h3><i class="fas fa-concierge-bell me-2"></i>Hotel Facilities</h3>
            </div>
        </div>
    </div>
@endsection
