@extends('admin.home')
@section('page_content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary text-center">Đăng ký phòng</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="" width="100%" cellspacing="0">
                <form action="{{route('teacher.computer-register.index')}}">
                    @csrf
                    <div class="d-flex register-header">
                        <div class="d-flex flex-column mr-4">
                            <h5 class="text-primary">Chọn ngày sử dụng</h5>
                            <input type="date" name="danhsach_thoigiansd" class="danhsach_thoigiansd form-control" value="{{ request()->danhsach_thoigiansd }}">
                        </div>
                        <div class="d-flex flex-column mr-4">
                            <h5 class="text-primary">Tiết học</h5>
                            <select name="tiet_id" id="tiet_id" class="form-control">
                                <option selected disabled>---Chọn tiết học---</option>
                                @foreach($tiet as $key => $item)
                                <option value="{{$item->id}}" @if($item->id == request()->tiet_id) selected @endif>{{$item->tiet_ten}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="text-primary">Số lượng</h5>
                            <input type="number" min="1" name="danhsach_soluong" placeholder="Chọn số lượng" class="form-control"
                            value="{{ request()->danhsach_soluong }}">
                        </div>
                        <div class="d-flex align-items-end">
                             <button class="btn btn-primary ml-2">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
                <form action="{{route('teacher.computer-register.register')}}" method="POST" id="formUpdateTeacher" class="d-block">
                    <div class="d-flex flex-column register-footer mt-3">
                        <h5 class="text-primary">Chọn phòng</h5>
                        <input type="hidden" name="user_id" class="user_id" value="{{Auth::id()}}">
                        <input type="hidden" name="quyen" class="quyen" value="{{Auth::user()->quyen_id}}">
                        <div class="d-flex flex-wrap">
                            @foreach($phong as $key => $item)
                            @php
                                $soluongconlai = $item->may_count - optional($item->thoikhoabieu)->first()->soluongmaysudung - $item->dangky_count
                            @endphp
                            @if($soluongconlai >= request()->danhsach_soluong)
                                <div class="col-md-3 mb-3">
                                    <div class="card h-100">
                                        <div>
                                            <img class="card-img-top" src="{{asset('admin/img/computer.png')}}">
                                        </div>
                                        <div class="card-content h-100 text-center">
                                            <h5 class="mt-2" style="font-weight: bold">
                                            {{$item->phong_ten}} - <span>Số lượng máy trống: {{ $soluongconlai }}/</span><span>{{$item->phong_soluong}}</span>
                                            </h5>
                                        </div>
                                        <div class="card-footer text-center">
                                            <input type="checkbox" name="phong_id[]" class="phong_id_{{$item->id}}" value="{{$item->id}}">
                                        </div>
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    <hr>
                    @csrf
                        <div class="col-md-12 d-flex justify-content-center">
                            <input type="hidden" name="danhsach_thoigiansd" id="update_danhsach_thoigiansd"/>
                            <input type="hidden" name="tiet_id" id="update_tiet_id"/>
                            <input type="hidden" name="danhsach_soluong" id="update_danhsach_soluong"/>
                            <button type="submit" class="btn btn-success btn-lg" id="buttonUpdateTeacher">Đăng ký</button>
                        </div>
                </form>
            </table>
        </div>
    </div>
    @endsection
    @section('script')
        <script>
            $(function() {
                console.log(123);
                $('#buttonUpdateTeacher').click(function(e) {
                    e.preventDefault()
                    const formUpdate = $('#formUpdateTeacher')
                    $('input#update_danhsach_thoigiansd').val("{{request()->danhsach_thoigiansd}}")
                    $('input#update_tiet_id').val("{{request()->tiet_id}}")
                    $('input#update_danhsach_soluong').val("{{request()->danhsach_soluong}}")
                    formUpdateTeacher.submit()
                })
            })
        </script>
    @endsection