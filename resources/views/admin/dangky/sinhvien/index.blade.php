@extends('admin.home')
@section('page_content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary text-center">Danh sách đăng ký của sinh viên</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="" width="100%" cellspacing="0">
               
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sinh viên</th>                       
                        <th>Tiết</th>
                        <th>Phần mềm</th>
                        <th>Phòng</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>  
                    @foreach($danhsach as $key => $item)  
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->user->name}}</td> 
                        <td>{{$item->tiet->tiet_ten}}</td> 
                        <td>{{$item->phanmem->phanmem_ten}}</td> 
                        <td>{{$item->phong->phong_ten}}</td>                        
                        <td>
                            <a href="{{route('admin.dangky.sinhvien.get_computer', $item->id)}}" class="btn btn-success text-uppercase" title="Sửa">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </div>
</div>
@endsection