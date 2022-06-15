<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChiTietDangKy;
use App\Models\User;
use App\Models\Khoa;
use App\Models\Nganh;
use App\Models\Lop;
use App\Models\Tiet;
use App\Models\Phong;
use App\Models\May;
use App\Models\DanhSachDangKy;
use App\Models\ThoiKhoaBieu;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function computerRegister(Request $request)
    {
        $page_title = 'Đăng ký máy trực tuyến';
        $tiet = Tiet::orderBy('id', 'asc')->get();
        $danhsach_thoigiansd = $request->danhsach_thoigiansd;
        $phong = [];
        if ($request->danhsach_thoigiansd > now() && $request->tiet_id) {
            $phong = Phong::orderBy('id', 'asc')
            ->withCount(['dangky' => function ($query) use ($request) {
                $query->where('danhsach_tinhtrang', 1)
                    ->whereDate('danhsach_thoigiansd', $request->danhsach_thoigiansd)
                    ->where('tiet_id', $request->tiet_id);
            }])
            ->withCount(['may' => function ($query) use ($request) {
                $query->where('may_tinhtrang', 1);
            }])
            ->with(['thoikhoabieu' => function($query) use ($request) {
                $thu = Carbon::parse($request->danhsach_thoigiansd)->weekday() + 1;
                $query->where('tiet_id', $request->tiet_id)
                ->where('thu', $thu);
            }])->get();
        }
        $thoikhoabieu = ThoiKhoaBieu::all();
        return view('student.computer-register.index', compact('page_title', 'tiet', 'phong', 'thoikhoabieu', 'danhsach_thoigiansd'));
    }
    public function register(Request $request)
    {
        //Check chỉ đăng ký một phòng
        //Đây là một mảng
        $check_phong = $request->phong_id;

        if (is_null($request->tiet_id) || is_null($request->danhsach_thoigiansd) || is_null($check_phong)) {
            Toastr::error('Chưa chọn đủ thông tin', 'Thất bại');
            return redirect()->back();
        }
        //Đếm mảng nếu > 1 thì báo lỗi
        if (count($check_phong) > 1) {
            Toastr::error('Sinh viên chỉ được đăng ký một phòng', 'Thất bại');
            return redirect()->back();
        } else {
            //Mảng ở đây chỉ còn 1 cái Phong_id nên cần lặp ra vì đã khai báo là mảng nên mới cần lặp
            //Key là vị trí thứ 0 , item là value của phong_id
            foreach ($check_phong as $key => $item) {
                //Dữ liệu request dc chuẩn bị để thêm
                $phong_after_check_id = $item;
                $tiet_id = $request->tiet_id;
                $user_id = auth()->id();
                $danhsach_thoigiansd = $request->danhsach_thoigiansd;
                $danhsach_tinhtrang = 0;
                $quyen = $request->quyen;
                $data = [
                    date_default_timezone_set('Asia/Ho_Chi_Minh'),
                    'user_id' => $user_id,
                    'tiet_id' => $tiet_id,
                    'phong_id' => $phong_after_check_id,
                    'danhsach_thoigiansd' =>  $danhsach_thoigiansd,
                    'danhsach_tinhtrang' =>  $danhsach_tinhtrang,
                    'quyen' => $quyen
                ];

                $user_id =  Auth::user()->id;
                $check_dangky = DanhSachDangKy::where('danhsach_thoigiansd',  $danhsach_thoigiansd)
                    ->where('tiet_id', $tiet_id)
                    ->where('phong_id',  $phong_after_check_id)
                    ->where('user_id', $user_id)
                    ->count();
                if ($check_dangky == 0) {
                    //Chuyển ngày sang thứ
                    $ngay_convert = (Carbon::parse($danhsach_thoigiansd)->weekday()) + 1;
                    //dd($ngay_convert);

                    //Lấy ra số lượng tối đa từ thười khóa biểu
                    $thoikhoabieu = ThoiKhoaBieu::where('thu', $ngay_convert)
                        ->where('phong_id', $phong_after_check_id)
                        ->where('tiet_id', $tiet_id)
                        ->first();
                    $soluongtoida = $thoikhoabieu->soluongmaysudung;
                    //dd($soluongtoida);

                    //Lấy ra số lượng đã đăng ký
                    $soluongdadangky = DanhSachDangKy::where('danhsach_thoigiansd',  $danhsach_thoigiansd)
                        ->where('tiet_id', $tiet_id)
                        ->where('phong_id',  $phong_after_check_id)
                        ->sum('danhsach_soluong');
                    //dd($soluongdadangky);

                    //Tìm tổng số lượng amys của phòng theo phong_id
                    $phong = Phong::where('id', $phong_after_check_id)->first();
                    $tongsoluongmay = $phong->phong_soluong;

                    //Tìm tổng số máy bị hỏng của phòng đó
                    $soluongmayhong = May::where('phong_id', $phong_after_check_id)->where('may_tinhtrang', 0)->count();

                    //Tính tổng số máy còn lại của phòng, tiết, ngày đó
                    $soluongconlai = $tongsoluongmay - $soluongtoida - $soluongdadangky - $soluongmayhong;
                    //dd($soluongconlai);

                    //Check xem sinh viên đăng ký phòng được ko dựa trên số máy còn lại
                    if ($soluongconlai == 0) {
                        Toastr::error('Đã hết máy', 'Thất bại');
                        return redirect()->back();
                    } else {
                        DanhSachDangKy::create($data);
                        Toastr::success('Đăng ký thành công', 'Thành công');
                        return redirect()->route('student.computer-register.index');
                    }
                } else {
                    Toastr::error('Đã đăng ký', 'Thất bại');
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
        $phong = Phong::all();
        $danhsach = DanhSachDangKy::with('user', 'tiet', 'phong', 'chitietdangky')
            ->where('user_id', $user_id)
            ->get();
        return view('student.register-history.index', compact('page_title', 'user', 'user_id', 'tiet', 'phong', 'danhsach'));
    }

    public function registerResult($id)
    {
        $page_title = 'Xem kết quả';
        $chitiet = ChiTietDangKy::with('danhsachdangky', 'phong')->where('danhsach_id', $id)->get();
        return view('student.register-result.index', compact('page_title', 'chitiet'));
    }
}
