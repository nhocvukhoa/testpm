<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nganh;
use App\Models\Lop;
use Brian2694\Toastr\Facades\Toastr;

class AdminLopController extends Controller
{
     public function index() {
        $page_title = 'Quản Lý Lớp';
        $nganh = Nganh::all();
        $lop = Lop::orderBy('id', 'asc')->with('nganh')->paginate(10);
        return view('admin.lop.index', compact('page_title', 'nganh', 'lop'));
    }

    public function create(){
        $page_title = 'Thêm lớp';
        $nganh = Nganh::all();
        $lop = Lop::orderBy('id', 'asc')->with('nganh')->get();
        return view('admin.lop.add', compact('page_title', 'lop', 'nganh'));
    }

    public function store(Request $request) {
        $request->validate(
            [
                'lop_ten' => 'required',  
                'nganh_id' => 'required',             
            ],
            [
                'lop_ten.required' => 'Tên lớp là bắt buộc',
                'nganh_id.required' =>   'Tên ngành là bắt buộc',
            ]
        );
        $data = [
            'lop_ten' => $request->lop_ten,
            'nganh_id' => $request->nganh_id
        ];
        Lop::create($data);
        Toastr::success('Thêm lớp thành công', 'Thành công');
        return redirect()->route('admin.lop.index');
    }

    public function edit($id){
        $page_title = 'Cập nhật lớp';
        $lop = Lop::find($id);
        $nganh = Nganh::all();
        return view('admin.lop.edit', compact('page_title','nganh', 'lop'));
    }

    public function update($id,Request $request){
        $data = [
            'lop_ten' => $request->lop_ten,
            'nganh_id' => $request->nganh_id,
        ];
        Lop::find($id)->update($data);
        Toastr::success('Cập nhật lớp thành công', 'Thành công');
        return redirect()->route('admin.lop.index');    
    }

    public function delete($id){
        Lop::find($id)->delete();
        Toastr::success('Xóa lớp thành công', 'Thành công');
        return redirect()->route('admin.lop.index');
    }
}
