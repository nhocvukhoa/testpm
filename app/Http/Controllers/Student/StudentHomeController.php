<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDangKy;
use App\Models\User;
use App\Models\Khoa;
use App\Models\Nganh;
use App\Models\Lop;
use App\Models\PhanMem;
use App\Models\Tiet;
use App\Models\Phong;
use App\Models\DanhSachDangKy;
use App\Models\ThoiKhoaBieu;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class StudentHomeController extends Controller
{
    public function home()
    {
        $page_title = 'Home';
        return view('student.dashboard.index', compact('page_title'));
    }

    public function information()
    {
        $page_title = 'Thông tin sinh viên';
        $student_id = Auth::user()->id;
        $lop = Lop::with('nganh')->orderBy('id', 'asc')->get();
        $nganh = Nganh::with('khoa')->orderBy('id', 'asc')->get();
        $khoa = Khoa::orderBy('id', 'asc')->get();
        $student = User::where('id', $student_id)->with('lop')->first();
        //Lấy lịch học sinh viên

        return view('student.information.index', compact('page_title', 'lop', 'nganh', 'khoa', 'student'));
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        User::find($id)->update($data);
        Toastr::success('Cập nhật thông tin thành công', 'Thành công');
        return redirect()->route('student.information');
    }

    public function computerRegister()
    {
        $page_title = 'Đăng ký máy trực tuyến';
        $tiet = Tiet::orderBy('id', 'asc')->get();
        $phanmem = PhanMem::orderBy('id', 'asc')->get();
        $phong = Phong::orderBy('id', 'asc')->get();

        $student_id = Auth::user()->id;
        $thoikhoabieu = ThoiKhoaBieu::where('user_id', $student_id)->get();

        return view('student.computer-register.index', compact('page_title', 'tiet', 'phanmem', 'phong', 'thoikhoabieu'));
    }

    public function register(Request $request)
    {
        //Check chỉ cho đăng ký 1 phòng
        $check_phong = $request->phong_id;
        if (count($check_phong) > 1) {
            Toastr::error('Sinh viên chỉ được đăng ký một phòng', 'Thất bại');
            return redirect()->back();
        } else {
            foreach ($check_phong as $key => $item) {
                $phong_check_id = $item;
                $user_id = $request->user_id;
                $tiet_id = $request->tiet_id;
                $phanmem_id = $request->phanmem_id;
                $danhsach_thoigiansd = $request->danhsach_thoigiansd;
                $danhsach_tinhtrang = 0;
                $quyen = $request->quyen;
                $data = [
                    date_default_timezone_set('Asia/Ho_Chi_Minh'),
                    'user_id' => $user_id,
                    'tiet_id' => $tiet_id,
                    'phanmem_id' => $phanmem_id,
                    'phong_id' => $phong_check_id,
                    'danhsach_thoigiansd' =>  $danhsach_thoigiansd,
                    'danhsach_tinhtrang' =>  $danhsach_tinhtrang,
                    'quyen' => $quyen
                ];
                //Check thời khóa biểu xem cấn lịch học không
                $student_id = Auth::user()->id;
                $check_thoikhoabieu = ThoiKhoaBieu::where('user_id', $student_id)
                    ->where('ngay', $request->danhsach_thoigiansd)
                    ->where('phong_id', $request->phong_id)
                    ->where('tiet_id', $request->tiet_id)->count();
                if ($check_thoikhoabieu == 0) {
                    //Ko cho đăng ký cùng tiết cùng ngày đang học
                    $check_trungtiet = ThoiKhoaBieu::where('user_id', $student_id)
                        ->where('ngay', $request->danhsach_thoigiansd)
                        ->where('tiet_id', $request->tiet_id)
                        ->count();
                    if ($check_trungtiet == 0) {
                        //Check sinh viên đã đăng ký chưa
                        $check_user = DanhSachDangKy::where('user_id', $user_id)
                            ->where('tiet_id', $tiet_id)
                            ->where('phong_id', $phong_check_id)
                            ->count();
                        if ($check_user == 0) {
                            DanhSachDangKy::create($data);
                            Toastr::success('Đăng ký thành công', 'Thành công');
                            return redirect()->route('student.computer-register.index');
                        } else {
                            Toastr::error('Sinh viên đã đăng ký', 'Thất bại');
                            return redirect()->back();
                        }
                    }
                    else{
                        Toastr::error('Không được đăng ký trùng tiết cùng ngày', 'Thất bại');
                        return redirect()->back();
                    }
                } else {
                    Toastr::error('Cấn lịch', 'Thất bại');
                    return redirect()->back();
                }
            }
        }
    }

    public function registerHistory()
    {
        $page_title = 'Lịch sử đăng ký';
        $user_id =  Auth::user()->id;
        $user = User::all();
        $tiet = Tiet::all();
        $phanmem = PhanMem::all();
        $phong = Phong::all();
        $danhsach = DanhSachDangKy::with('user', 'tiet', 'phanmem', 'phong', 'chitietdangky')
            ->where('user_id', $user_id)
            ->get();
        return view('student.register-history.index', compact('page_title', 'user', 'tiet', 'phanmem', 'phong', 'danhsach'));
    }

    public function registerResult($id)
    {
        $page_title = 'Xem kết quả';
        $chitiet = ChiTietDangKy::with('danhsachdangky', 'phong')->where('danhsach_id', $id)->get();
        return view('student.register-result.index', compact('page_title', 'chitiet'));
    }
}
