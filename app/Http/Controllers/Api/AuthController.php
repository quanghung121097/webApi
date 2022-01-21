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
use Illuminate\Support\Facades\Hash;
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
        // $this->middleware('customer', ['except' => ['login', 'register']]);
    }

    /**
     * @api {post} /api/auth/login Đăng nhập tài khoản
     * @apiName login
     * @apiGroup Authentication
     *
     * @apiHeader {String} Content-Type application/json
     *
     * @apiParam (Body) {String} username Tài khoản của người dùng.
     * @apiParam (Body) {String} password Mật khẩu tài khoản người dùng.
     * @apiSampleRequest https://qhshop.xyz/api/auth/login
     * @apiBody {String} [username="quanghung1"]
     * @apiBody {String} [password="Quanghung1210"]
     * @apiExample Curl
     *  curl --location --request POST "https://qhshop.xyz/api/auth/login" \
     *      -H  'Content-Type: application/json' \
     *      -d '{
     *        "username":"quanghung1",
     *        "password":"Quanghung1210"
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'POST',
     *   url: 'https://qhshop.xyz/api/auth/login',
     *   headers: {
     *      'Content-Type': 'application/json'
     *   },
     *   data: {
     *      "username":"quanghung1",
     *      "password":"Quanghung1210"
     *  }
     * });
     * console.log(response);
     * } catch (error) {
     * console.error(error);
     * }
     * @apiExample PHP
     * <?php
     * //Sử dụng pecl_http
     * $client = new http\Client;
     * $request = new http\Client\Request;
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/login');
     * $request->setRequestMethod('POST');
     * $body = new http\Message\Body;
     * $body->append('{
     *      "username":"quanghung1",
     *      "password":"Quanghung1210"
     * }');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json'
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {
     *      "username":"quanghung1",
     *        "password":"Quanghung1210"
     * }
     *headers = {
     *  'Content-type': 'application/json'
     *}
     * conn.request("POST", "/api/auth/login", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     * "success": true,
     * "data": {
     *   "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk3NTMwMywiZXhwIjoxNjM4OTc4OTAzLCJuYmYiOjE2Mzg5NzUzMDMsImp0aSI6IlNxT0ZBcjhpV25VdWhscEIiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.CV7hGvuDLMG_PiC4exfWeLLdYlIhDJg117ZILtPl5tU",
     *   "user": {
     *      "id": 18,
     *       "username": "quanghung1",
     *      "role": "customer",
     *      "created_at": "2021-12-05T11:18:11.000000Z",
     *     "updated_at": "2021-12-05T16:55:23.000000Z"
     * }
     *}
     * }
     * @apiErrorExample  Error-Response
     * HTTP/1.1 422 Unprocessable Entity
     *{
     *  "success": false,
     *  "message": "Sai thông tin đăng nhập"
     *}
     *
     *HTTP/1.1 400 Bad Request
     *{
     *"success": false,
     *"message": {
     *      "username": [
     *          "Tên tài khoản là bắt buộc."
     *      ],
     *      "password": [
     *          "Mật khẩu là bắt buộc."
     *      ]
     *  }
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     * 
     */

    public function login(Request $request)
    {

        $validator = FacadesValidator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'required' => ':attribute là bắt buộc'
            ],
            [
                'username' => 'Tên tài khoản',
                'password' => 'Mật khẩu'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
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
                'data' => compact('token', 'user')
            ]
        );
    }

    /**
     * @api {post} /api/auth/register Đăng ký tài khoản (khách hàng)
     * @apiName register-custommer
     * @apiGroup Authentication
     *
     * @apiHeader {String} Content-Type application/json
     *
     * @apiParam (Body) {String} username Tên tài khoản dùng để đăng nhập (bắt buộc,5 ký tự trở lên, duy nhất).
     * @apiParam (Body) {String} password Mật khẩu tài khoản (ít nhất 8 ký tự, gồm 1 ký tự viết hoa, 1 ký tự viết thường, 1 số).
     * @apiParam (Body) {String} confirm_password Xác nhận lại mật khẩu đăng ký (bắt buộc).
     * @apiParam (Body) {String} phone Số điện thoại (bắt buộc).
     * @apiParam (Body) {String} gender Giới tính (1 nam, 0 nữ, mặc định là 1).
     * @apiParam (Body) {String} email Địa chỉ email (bắt buộc, đúng định dạng email, duy nhất).
     * @apiParam (Body) {String} full_name Họ và tên (bắt buộc, tối đa 40 ký tự).
     * @apiParam (Body) {String} address Địa chỉ (bắt buộc),
     * @apiParam (Body) {String} dateOfBirth (bắt buộc, định dạng Y-m-d).
     * @apiSampleRequest https://qhshop.xyz/api/auth/register
     * @apiBody {String} [username="quanghung4"]
     * @apiBody {String} [password="Quanghung1210"]
     * @apiBody {String} [confirm_password="Quanghung1210"]
     * @apiBody {String} [phone="0788337682"]
     * @apiBody {String} [gender="1"]
     * @apiBody {String} [email="quanghung121097@gmail.com"]
     * @apiBody {String} [full_name="Nguyễn Văn Quang Hưng"]
     * @apiBody {String} [address="Hải Phòng"]
     * @apiBody {String} [dateOfBirth="1997-10-12"]
     * @apiExample Curl
     *  curl --location --request POST "https://qhshop.xyz/api/auth/register" \
     *      -H  'Content-Type: application/json' \
     *      -d '{
     *             "username": "quanghung4",
     *             "password": "Quanghung1210",
     *             "confirm_password": "Quanghung1210",
     *             "full_name": "Nguyễn Văn Quang Hưng",
     *             "gender" : 1,
     *             "address" : "Hải Phòng",
     *             "dateOfBirth": "1997-10-12",
     *             "phone" : "0788337682",
     *             "email" : "quanghung121097@gmail.com"
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'POST',
     *   url: 'https://qhshop.xyz/api/auth/register',
     *   headers: {
     *      'Content-Type': 'application/json'
     *   },
     *   data: {
     *             "username": "quanghung4",
     *             "password": "Quanghung1210",
     *             "confirm_password": "Quanghung1210",
     *             "full_name": "Nguyễn Văn Quang Hưng",
     *             "gender" : 1,
     *             "address" : "Hải Phòng",
     *             "dateOfBirth": "1997-10-12",
     *             "phone" : "0788337682",
     *             "email" : "quanghung121097@gmail.com"
     *  }
     * });
     * console.log(response);
     * } catch (error) {
     * console.error(error);
     * }
     * @apiExample PHP
     * <?php
     * //Sử dụng pecl_http
     * $client = new http\Client;
     * $request = new http\Client\Request;
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/register');
     * $request->setRequestMethod('POST');
     * $body = new http\Message\Body;
     * $body->append('{
     *             "username": "quanghung4",
     *             "password": "Quanghung1210",
     *             "confirm_password": "Quanghung1210",
     *             "full_name": "Nguyễn Văn Quang Hưng",
     *             "gender" : 1,
     *             "address" : "Hải Phòng",
     *             "dateOfBirth": "1997-10-12",
     *             "phone" : "0788337682",
     *             "email" : "quanghung121097@gmail.com"
     * }');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json'
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {
     *             "username": "quanghung4",
     *             "password": "Quanghung1210",
     *             "confirm_password": "Quanghung1210",
     *             "full_name": "Nguyễn Văn Quang Hưng",
     *             "gender" : 1,
     *             "address" : "Hải Phòng",
     *             "dateOfBirth": "1997-10-12",
     *             "phone" : "0788337682",
     *             "email" : "quanghung121097@gmail.com"
     * }
     * headers = {
     *  'Content-type': 'application/json'
     *}
     * conn.request("POST", "/api/auth/register", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Đã đăng ký thành công mời bạn đăng nhập",
     *      "person": {
     *          "account_id": 19,
     *          "full_name": "Nguyễn Văn Quang Hưng",
     *          "gender": "Nam",
     *          "address": "Hải Phòng",
     *          "date_of_birth": "1997-10-11T17:00:00.000000Z",
     *          "phone": "0788337682",
     *          "email": "quanghung1210971@gmail.com",
     *          "updated_at": "2021-12-08T16:17:59.000000Z",
     *          "created_at": "2021-12-08T16:17:59.000000Z",
     *          "id": 12
     *      }
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 400 Bad Request
     *{
     *"success": false,
     *"message": {
     *      "username": [
     *          "Tên tài khoản là bắt buộc."
     *      ],
     *      "password": [
     *          "Mật khẩu tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số"
     *      ],
     *      ...
     *  }
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
    public function register(Request $request)
    {
        $data = $request->all();
        $validator = FacadesValidator::make(
            $data,
            [
                'username' => 'unique:account,username|min:5',
                'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
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
                'regex' => ':attribute tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số'

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
     * @api {post} /api/auth/logout Đăng xuất tài khoản
     * @apiName logout
     * @apiGroup Authentication
     *
     * @apiHeader {String} Content-Type application/json
     * @apiHeader {String} Authorization Bearer Token
     * @apiSampleRequest https://qhshop.xyz/api/auth/logout
     * @apiExample Curl
     *  curl --location --request POST "https://qhshop.xyz/api/auth/logout" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'POST',
     *   url: 'https://qhshop.xyz/api/auth/logout',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     * });
     * console.log(response);
     * } catch (error) {
     * console.error(error);
     * }
     * @apiExample PHP
     * <?php
     * //Sử dụng pecl_http
     * $client = new http\Client;
     * $request = new http\Client\Request;
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/logout');
     * $request->setRequestMethod('POST');
     * $body = new http\Message\Body;
     * $body->append('{}');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json',
     * 'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {}
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("POST", "/api/auth/logout", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Đăng xuất thành công"
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 401 Unauthorized
     *{
     *"success": false,
     *"message": "Unauthenticated."
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
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
     * @api {get} /api/auth/user-profile Lấy thông tin user đăng nhập
     * @apiName user-profile
     * @apiGroup Authentication
     *
     * @apiHeader {String} Content-Type application/json
     * @apiHeader {String} Authorization Bearer Token
     * @apiSampleRequest https://qhshop.xyz/api/auth/user-profile
     * @apiExample Curl
     *  curl --location --request GET "https://qhshop.xyz/api/auth/user-profile" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'GET',
     *   url: 'https://qhshop.xyz/api/auth/user-profile',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     * });
     * console.log(response);
     * } catch (error) {
     * console.error(error);
     * }
     * @apiExample PHP
     * <?php
     * //Sử dụng pecl_http
     * $client = new http\Client;
     * $request = new http\Client\Request;
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/user-profile');
     * $request->setRequestMethod('GET');
     * $body = new http\Message\Body;
     * $body->append('{}');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json',
     * 'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {}
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("GET", "/api/auth/user-profile", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "data": {
     *          "id": 19
     *          "username": "quanghung4",
     *          "role": "customer",
     *          "created_at": "2021-12-08T16:17:59.000000Z",
     *          "updated_at": "2021-12-08T16:17:59.000000Z"
     *      }
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 401 Unauthorized
     *{
     *"success": false,
     *"message": "Unauthenticated."
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
    public function userProfile()
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user(),
        ]);
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

    /**
     * @api {post} /api/auth/change-password Đổi mật khẩu user
     * @apiName change-password
     * @apiGroup Authentication
     * @apiHeader {String} Content-Type application/json
     * @apiHeader {String} Authorization Bearer Token
     * @apiParam (Body) {String} old_password Mật khẩu cũ (bắt buộc).
     * @apiParam (Body) {String} new_password Mật khẩu mới (ít nhất 8 ký tự, gồm 1 ký tự viết hoa, 1 ký tự viết thường, 1 số).
     * @apiParam (Body) {String} new_password_confirmation Xác nhận lại mật khẩu đăng ký (bắt buộc).
     * @apiSampleRequest https://qhshop.xyz/api/auth/change-password
     * @apiBody {String} [old_password="Quanghung1210"]
     * @apiBody {String} [new_password="Quanghung121097"]
     * @apiBody {String} [new_password_confirmation="Quanghung121097"]
     * @apiExample Curl
     *  curl --location --request POST "https://qhshop.xyz/api/auth/change-password" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *      -d '{
     *             "old_password": "Quanghung1210",
     *             "new_password": "Quanghung121097",
     *             "new_password_confirmation": "Quanghung121097"
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'POST',
     *   url: 'https://qhshop.xyz/api/auth/change-password',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     *   data: {
     *             "old_password": "Quanghung1210",
     *             "new_password": "Quanghung121097",
     *             "new_password_confirmation": "Quanghung121097"
     *  }
     * });
     * console.log(response);
     * } catch (error) {
     * console.error(error);
     * }
     * @apiExample PHP
     * <?php
     * //Sử dụng pecl_http
     * $client = new http\Client;
     * $request = new http\Client\Request;
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/change-password');
     * $request->setRequestMethod('POST');
     * $body = new http\Message\Body;
     * $body->append('{
     *             "old_password": "Quanghung1210",
     *             "new_password": "Quanghung121097",
     *             "new_password_confirmation": "Quanghung121097"
     * }');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json',
     * 'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {
     *             "old_password": "Quanghung1210",
     *             "new_password": "Quanghung121097",
     *             "new_password_confirmation": "Quanghung121097"
     * }
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("POST", "/api/auth/change-password", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Thay đổi mật khẩu thành công",
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 400 Bad Request
     *{
     *"success": false,
     *"message": {
     *      "old_password": [
     *          "Mật khẩu cũ là bắt buộc."
     *      ],
     *      "new_password": [
     *          "Mật khẩu tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số"
     *      ],
     *      "new_password_confirmation": [
     *          "Mật khẩu xác nhận không khớp"
     *      ]
     *  }
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
    public function changePassWord(Request $request)
    {
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => ['old_password' => 'Mật khẩu cũ không chính xác']], 400);
        }
        $validator = FacadesValidator::make(
            $request->all(),
            [
                'old_password' => 'required|string|min:5|max:15',
                'new_password' => 'required|string|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
            ],
            [
                'required' => ':attribute là bắt buộc',
                'confirmed' => 'Mật khẩu xác nhận không khớp',
                'regex' => ':attribute tối thiểu tám ký tự, ít nhất một chữ cái viết hoa, một chữ cái viết thường và một số'

            ],
            [
                'old_password' => 'Mật khẩu cũ',
                'new_password' => 'Mật khẩu mới'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $userId = auth()->user()->id;

        $user = Account::where('id', $userId)->update(
            ['password' => bcrypt($request->new_password)]
        );
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'Thay đổi mật khẩu thành công',
        ], 201);
    }
}
