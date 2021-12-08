<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\Review;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class ApiProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
        $this->middleware('admin',['except' => ['search', 'getDetailProduct']]);
    }

    public function search(Request $request)
    {
        $data['page'] = $request->page ?? 1;
        $data['limit'] = $request->limit ?? 30;
        $data['select'] = $request->select ?? ['id','name','price','quantity_in_stock'];
        $data['sortBy'] =  $request->sortBy ?? 'created_at';
        $data['paginate'] =  isset($request->paginate) ? $request->paginate : true;
        $data['conditions'] = [];
        $errors = [];
        if (isset($request->name)) {
            $data['conditions'][] = [
                'key' => 'name',
                'value' => $request->name,
                'operation' => 'like',
            ];
        }
        if (isset($request->category_id)) {
            $data['conditions'][] = [
                'key' => 'category_id',
                'value' => $request->category_id
            ];
        }
        if (isset($request->brand)) {
            $data['conditions'][] = [
                'key' => 'brand',
                'value' => $request->brand,

            ];
        }
        if (isset($request->origin)) {
            $data['conditions'][] = [
                'key' => 'origin',
                'value' => $request->origin,

            ];
        }
        $conditionFieldInt = $request->only(['price', 'promotion_price']);
        foreach ($conditionFieldInt as $key => $condition) {
            $operation = $condition['operation'] ?? '=';
            switch ($operation) {
                case 'between':
                    if (!isset($condition['value']['start']) || !isset($condition['value']['end']) || ((int) $condition['value']['start'] >= (int) $condition['value']['end'])) {
                        $errors[] = 'Khoảng giá trị ' . $key . ' không đúng định dạng ';
                    }
                    break;

                default:
                    if (!is_numeric($condition['value'])) {
                        $errors[] = 'Khoảng giá trị ' . $key . ' không đúng định dạng ';
                    }
                    break;
            }
            $data['conditions'][] = [
                'key' => $key,
                'value' => $condition['value'] ?? 0,
                'operation' => $operation,
            ];
        }

        $data['conditions'][] = [
            'key' => 'enabled',
            'value' => 1,

        ];
        if (empty($errors)) {
            $products = $this->productService->search($data);
            return response(['success' => true, 'data' => $products]);
        } else {
            return response(['success' => false, 'message' => $errors ?? 'Có lỗi xảy ra vui lòng thử lại']);
        }
    }

    public function getDetailProduct(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response(['success' => false, 'message' => 'Thiếu id sản phẩm'],400);
        }
        $product_detail = Product::find($id);
        $listReview = Review::whereHas('order_item', function ($query) use ($id) {
            $query->where(['product_id' => $id, 'reviewed' => 1]);
        })->get();
        $product_detail->views++;
        $product_detail->save();
        return response(['success' => true, 'data' => ['product_detail' => $product_detail, 'listReview' => $listReview]]);
    }

    public function postAdd(Request $request)
    {
        $validator = FacadesValidator::make(
            $request->all(),
            [
                'description' => 'max:2000',
                'promotion_price' => 'numeric|lt:price',
                'name' => 'required',
                'origin' => 'required',
                'brand' => 'required',
                'category_id' => 'required',
                'quantity_in_stock' => 'required|numeric',
                'price' => 'required|numeric',
                'images' => 'required',
                'images.*' => 'mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'required' => 'Là bắt buộc',
                'max' => 'Trường trên tối đa :max ký tự',
                'lt' => 'Giá ưu đãi phải nhỏ hơn giá thường',
                'mimes' => 'Ảnh định dạng jpeg,jpg,png'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }
        $category = Category::find($request->category_id);
        if (empty($category)) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại'], 404);
        }
        DB::beginTransaction();
        try {
            $product = new Product;
            $product->category_id = $request->category_id;
            $product->name = $request->name;
            $product->brand = $request->brand;
            $product->origin = $request->origin;
            $product->price = $request->price;
            $product->promotion_price = $request->promotion_price;
            $product->description = ($request->description != "") ? $request->description : "";
            $product->enabled = $request->enabled ?? 1;
            $product->quantity_in_stock = $request->quantity_in_stock;
            $product->views = 0;
            #save product vào csdl
            $product->save();

            $images = $request->file('images');
            foreach ($images as $index => $image) {
                # code...
                $imageName = time() . 'ProductId' . $product->id . 'ImageId' . $index . '.png';
                $image->move('images/product', $imageName);
                $dbImage = new Image;
                $dbImage->name = $imageName;
                $dbImage->product_id = $product->id;
                $dbImage->save();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Tạo mới sản phẩm thành công']);
        } catch (\Throwable $th) {
            DB::rollBack();
            TelegramService::sendMessage(htmlentities($th->getMessage()));
            return response()->json([
                'success' => false,
                'message' => htmlentities($th->getMessage()),
            ], 500);
        }
    }

    public function postEdit(Request $request)
    {
        // dd($request);
        $validator = FacadesValidator::make(
            $request->all(),
            [
                'id' => 'required',
                'description' => 'max:2000',
                'promotion_price' => 'lt:price',
                'name' => 'required',
                'origin' => 'required',
                'brand' => 'required',
                'category_id' => 'required',
                'quantity_in_stock' => 'required',
                'price' => 'required|numeric',
                'images' => 'required',
                'images.*' => 'mimes:jpeg,png,jpg|max:2048'
            ],
            [
                'required' => 'Là bắt buộc',
                'max' => 'Trường trên tối đa :max ký tự',
                'lt' => 'Giá ưu đãi phải nhỏ hơn giá thường',
                'mimes' => 'Ảnh định dạng jpeg,jpg,png'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }
        $id = $request->id;
        $product = Product::find($id);
        if(empty($product)){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }
        $category = Category::find($request->category_id);
        if (empty($category)) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại'], 404);
        }
        DB::beginTransaction();
        try {
            
            
            $product->name = $request->name;
            $product->brand = $request->brand;
            $product->origin = $request->origin;
            $product->price = $request->price;
            $product->promotion_price = $request->promotion_price;
            $product->description = $request->description;
            $product->enabled = $request->enabled;
            $product->quantity_in_stock = $request->quantity_in_stock;
            #save product vào csdl
            $product->save();

            if ($request->hasFile('images')) {
                #xóa ảnh cũ
                $oldImages = Image::where('product_id', $id)->get();
                foreach ($oldImages as $oldImage) {
                    if (file_exists('images/product/' . $oldImage->name))
                        unlink('images/product/' . $oldImage->name);
                }
                Image::where('product_id', $id)->delete();
                #thêm ảnh mới
                $images = $request->file('images');
                foreach ($images as $index => $image) {
                    # code...
                    $imageName = time() . 'ProductId' . $product->id . 'ImageId' . $index . '.png';
                    $image->move('images/product', $imageName);

                    $dbImage = new Image;
                    $dbImage->name = $imageName;
                    $dbImage->product_id = $product->id;
                    $dbImage->save();
                }
            }
            return response(['success' => true, 'message' => 'Sửa sản phẩm thành công']);
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
     * @api {DELETE} /api/product/delete Xóa sản phẩm
     * @apiName Xóa sản phẩm
     * @apiGroup Product
     *
     * @apiHeader {String} Content-Type application/json
     * @apiHeader {String} Authorization Bearer Token
     * @apiSuccess {boolean} success Trạng thái (Thành công hoặc thất bại).
     * @apiSuccess {string} message Nội dung thông báo (nếu có lỗi thì thông báo lỗi).
     * @apiSampleRequest https://qhshop.xyz/api/auth/delete
     * @apiBody {String} [id="6"]
     * @apiExample Curl
     *  curl --location --request DELETE "https://qhshop.xyz/api/auth/delete" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *      -d '{
     *             "id": 6
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'DELETE',
     *   url: 'https://qhshop.xyz/api/auth/delete',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     *   data: {
     *             "id": 6
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
     * $request->setRequestUrl('https://qhshop.xyz/api/auth/delete');
     * $request->setRequestMethod('DELETE');
     * $body = new http\Message\Body;
     * $body->append('{
     *             "id": 6
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
     * payload = '{
     *             "id": 6
     * }'
     * conn.request("DELETE", "/api/auth/delete", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Xóa sản phẩm thành công"
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 401 Unauthorized
     *{
     *"success": false,
     *"message": "Unauthenticated."
     *}
     * 
     * *
     *HTTP/1.1 401 Unauthorized
     *{
     *"success": false,
     *"message": "Tài khoản không có quyền thực hiện hành động này."
     *}
     *
     *HTTP/1.1 400 Bad Request
     *{
     *"success": false,
     *"message": "Thiếu id sản phẩm"
     *}
     *
     *HTTP/1.1 404 Not Found
     *{
     *"success": false,
     *"message": "Không tìm thấy sản phẩm"
     *}
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu id sản phẩm',
            ], 400);
        }
        $product = Product::find($id);
        if(empty($product)){
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }
        DB::beginTransaction();
        try {
            $oldImages = Image::where('product_id', $id)->get();
            // dd($oldImages);
            foreach ($oldImages as $oldImage) {
                if (file_exists('images/product/' . $oldImage->name))
                    unlink('images/product/' . $oldImage->name);
            }
            Product::destroy($id);
            DB::commit();
            return response(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
        } catch (\Throwable $th) {
            DB::rollBack();
            TelegramService::sendMessage(htmlentities($th->getMessage()));
            return response()->json([
                'success' => false,
                'message' => htmlentities($th->getMessage()),
            ], 500);
        }
    }
}
