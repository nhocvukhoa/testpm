@extends('admin.home')
@section('page_content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary text-center">Quản lý lớp</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="" width="100%" cellspacing="0">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="" method="GET">
                            @csrf
                            <div class="search-box d-flex">
                                <input type="search" class="search-txt form-control mr-2 col-md-7" name="search" placeholder="Nhập tên ngành ...">
                                <input type="submit" class="btn btn-info btn-sm mr-4" value="Tìm kiếm" name="search_items">
                            </div>
                        </form>
                    </div>
                    <a href="{{route('admin.lop.create')}}" class="btn btn-primary text-uppercase" title="Thêm">
                        Thêm
                    </a>
                </div>

                <div style="margin-top: 30px;"></div>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên lớp</th>
                        <th>Tên ngành</th>
                        <th class="col-md-2">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lop as $key => $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->lop_ten}}</td>
                        <td>{{$item->nganh->nganh_ten}}</td>
                        <td>
                            <a href="{{route('admin.lop.edit', $item->id)}}" class="btn btn-success text-uppercase" title="Sửa">
                                Edit
                            </a>
                            <a href="{{route('admin.lop.delete', $item->id)}}" class="btn btn-danger text-uppercase delete" title="Xóa" onclick="return confirm('Bạn có muốn xóa lớp này không?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=" col-sm-12 text-right text-center-xs mt-2">
                <div class="pagination d-flex justify-content-center"> {{$lop->links('paginationlinks')}}</div>
            </div>
        </div>
    </div>
    @endsection










    