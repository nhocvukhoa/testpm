@extends('admin.home')
@section('page_content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h1 class="h3 mb-0 text-gray-800">Chào mừng giảng viên {{Auth::user()->name}} đến trang 
                    quản lý phòng máy
                </h1>
            </div>
        </div>
    </div>
</div>
@endsection