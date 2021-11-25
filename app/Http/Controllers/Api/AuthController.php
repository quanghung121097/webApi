<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TelegramService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->middleware('customer',['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = FacadesValidator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $credentials = $request->only('username', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sai thông tin đăng nhập'
                ], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['Tạo token thất bại'], 500);
        }
        $user = auth()->user();
        return response()->json(
            [
                'success' => true,
                'data' => compact('token','user')
            ]
        );
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $validator = FacadesValidator::make(
            $data,
            [
                'username' => 'unique:account,username|min:5|max:15',
                'password' => 'min:5|max:15',
                'confirm_password' => 'same:password',
                'phone' => 'digits:10',
                'gender' => 'required|integer|in:0,1',
                'email' => 'unique:person,email|email',
                'full_name' => 'required|max:40',
                'address' => 'required',
                'dateOfBirth' => 'required|date|date_format:Y-m-d',
            ],
            [
                'unique' => ':attribute đã tồn tại',
                'email' => 'Không đúng định dạng email',
                'min' => ':attribute tối thiểu :min ký tự',
                'max' => ':attribute tối đa :max ký tự',
                'digits' => ':attribute gồm :digits số',
                'same' => ':attribute không khớp mật khẩu đăng ký',
                'in' => ':attribute nam (1), nữ (0)',
                'required' => ':attribute là bắt buộc',
                'integer' => ':attribute là kiểu số nguyên',

            ],
            [
                'username' => 'Tên tài khoản',
                'password' => 'Mật khẩu',
                'confirm_password' => 'Mật khẩu xác nhận',
                'phone' => 'Số điện thoại',
                'gender' => 'Giới tính',

            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }
        DB::beginTransaction();
        try {
            $account = new Account();
            $account->username = $request->username;
            if ($request->password != $request->confirm_password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xác nhận mật khẩu không chính xác'
                ]);
            }
            $account->password = bcrypt($request->password);
            $account->role = 'customer';
            $account->save();
            # add person
            $person = new Person();
            $person->account_id = $account->id;
            $person->full_name = $request->full_name;
            $person->gender = ($request->gender == 1) ? 'Nam' : 'Nữ';
            $person->address = $request->address;
            $person->date_of_birth =  Carbon::parse($request->dateOfBirth);
            $person->phone = $request->phone;
            $person->email = $request->email;
            $person->save();
            # add customer
            $customer = new Customer();
            $customer->person_id = $person->id;
            $customer->save();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Đã đăng ký thành công mời bạn đăng nhập',
                'data' => ['person' => $person]
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            TelegramService::sendMessage(htmlentities($th->getMessage()));
            return response()->json([
                'success' => false,
                'message' => htmlentities($th->getMessage()),
            ], 500);
        }
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'old_password' => 'required|string|min:5|max:15',
            'new_password' => 'required|string|confirmed|min:5|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $userId = auth()->user()->id;

        $user = Account::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Thay đổi mật khẩu thành công',
            'user' => $user,
        ], 201);
    }
}
