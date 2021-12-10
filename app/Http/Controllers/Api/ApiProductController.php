<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Image;
use App\Models\Review;
use App\Services\ImageService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class ApiProductController extends Controller
{
    private $productService, $imageService;

    public function __construct(ProductService $productService, ImageService $imageService)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
        $this->middleware('admin', ['except' => ['search', 'getDetailProduct']]);
    }

    /**
     * @api {GET} /api/product/search Bộ lọc sản phẩm
     * @apiName Search
     * @apiGroup Product
     *
     * @apiHeader {String} Content-Type application/json
     * @apiSampleRequest https://qhshop.xyz/api/product/search
     * @apiBody {Number} [limit="1"]
     * @apiBody {String} [name="iphone"]
     * @apiExample Curl
     *  curl --location --request GET "https://qhshop.xyz/api/product/search" \
     *      -H  'Content-Type: application/json' \
     *      -d '{
     *              "limit": 1,
     *              "name": "iphone",
     *              "category_id": 9,
     *              "price": {
     *                  "operation": "between",
     *                  "value": {
     *                      "start": 1000000,
     *                      "end": 32500000
     *                  }
     *              },
     *              "promotion_price": {
     *                  "operation": ">=",
     *                  "value": 12800000
     *              }
     *
     *       }'
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'GET',
     *   url: 'https://qhshop.xyz/api/product/search',
     *   headers: {
     *      'Content-Type': 'application/json'
     *   },
     *   data: {
     *            "limit": 1,
     *              "name": "iphone",
     *              "category_id": 9,
     *              "price": {
     *                  "operation": "between",
     *                  "value": {
     *                      "start": 1000000,
     *                      "end": 32500000
     *                  }
     *              },
     *              "promotion_price": {
     *                  "operation": ">=",
     *                  "value": 12800000
     *              }
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
     * $request->setRequestUrl('https://qhshop.xyz/api/product/search');
     * $request->setRequestMethod('GET');
     * $body = new http\Message\Body;
     * $body->append('{
     *            "limit": 1,
     *              "name": "iphone",
     *              "category_id": 9,
     *              "price": {
     *                  "operation": "between",
     *                  "value": {
     *                      "start": 1000000,
     *                      "end": 32500000
     *                  }
     *              },
     *              "promotion_price": {
     *                  "operation": ">=",
     *                  "value": 12800000
     *              }
     * }');
     * $request->setBody($body);
     * $request->setOptions(array());
     * $request->setHeaders(array(
     * 'Content-Type' => 'application/json',
     * ));
     * $client->enqueue($request)->send();
     * $response = $client->getResponse();
     * echo $response->getBody();
     * @apiExample Python:
     * import http.client
     * import mimetypes
     * conn = http.client.HTTPSConnection("https://qhshop.xyz")
     * payload = {
     *      "limit": 1,
     *              "name": "iphone",
     *              "category_id": 9,
     *              "price": {
     *                  "operation": "between",
     *                  "value": {
     *                      "start": 1000000,
     *                      "end": 32500000
     *                  }
     *              },
     *              "promotion_price": {
     *                  "operation": ">=",
     *                  "value": 12800000
     *              }
     * }
     * headers = {
     *  'Content-type': 'application/json'
     *}
     * conn.request("GET", "/api/product/search", payload, headers);
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
    public function search(Request $request)
    {
        $data['page'] = $request->page ?? 1;
        $data['limit'] = $request->limit ?? 30;
        $data['select'] = $request->select ?? ['id', 'name', 'price', 'quantity_in_stock'];
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
    /**
     * @api {GET} /api/product/detail Chi tiết sản phẩm
     * @apiName detail
     * @apiGroup Product
     *
     * @apiParam (Body) {int} id Id sản phẩm cần xem (bắt buộc)
     * @apiHeader {String} Content-Type application/json
     * @apiSampleRequest https://qhshop.xyz/api/product/detail
     * @apiBody {String} [id="6"]
     * @apiExample Curl
     *  curl --location --request GET "https://qhshop.xyz/api/product/detail" \
     *      -H  'Content-Type: application/json' \
     *      -d '{
     *              "id": 6
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'GET',
     *   url: 'https://qhshop.xyz/api/product/detail',
     *   headers: {
     *      'Content-Type': 'application/json'
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
     * $request->setRequestUrl('https://qhshop.xyz/api/product/detail');
     * $request->setRequestMethod('GET');
     * $body = new http\Message\Body;
     * $body->append('{
     *             "id": 6
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
     *      "id": 6
     * }
     * headers = {
     *  'Content-type': 'application/json'
     *}
     * conn.request("GET", "/api/product/detail", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "data": {
     *        "product_detail": {
     *            "id": 6,
     *            "category_id": 12,
     *            "name": "Apple Watch Seri 3",
     *            "brand": "Apple",
     *            "origin": "US",
     *            "price": 1000000,
     *            "promotion_price": 0,
     *            "description": null,
     *            "enabled": 1,
     *            "quantity_in_stock": 99,
     *            "views": 24,
     *            "created_at": "2020-11-02T02:58:04.000000Z",
     *            "updated_at": "2021-12-10T16:01:03.000000Z"
     *        },
     *        "listReview": [
     *            {
     *                "id": 3,
     *                "rate": 4,
     *                "comment": "bthg",
     *                "reviewed": 1,
     *                "created_at": "2020-11-26T19:18:24.000000Z",
     *            },
     *            {
     *                "id": 8,
     *                "rate": 4,
     *                "comment": "hay hú",
     *                "reviewed": 1,
     *                "created_at": "2020-11-26T19:22:13.000000Z",
     *                "updated_at": "2020-11-30T07:49:18.000000Z"
     *            }
     *       ]
     *    }
     * }
     * @apiErrorExample  Error-Response
     *
     *HTTP/1.1 400 Bad Request
     *{
     *"success": false,
     *"message": "Thiếu id sản phẩm"
     *}
     *
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "data": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
    public function getDetailProduct(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response(['success' => false, 'message' => 'Thiếu id sản phẩm'], 400);
        }
        $product_detail = Product::find($id);
        $listReview = Review::whereHas('order_item', function ($query) use ($id) {
            $query->where(['product_id' => $id, 'reviewed' => 1]);
        })->get();
        $product_detail->views++;
        $product_detail->save();
        return response(['success' => true, 'data' => ['product_detail' => $product_detail, 'listReview' => $listReview]]);
    }

    /**
     * @api {POST} /api/product/add Thêm mới sản phẩm
     * @apiName Add
     * @apiGroup Product
     *
     * @apiHeader {String} Content-Type application/json
     * @apiSampleRequest https://qhshop.xyz/api/product/add
     * @apiParam (Body) {Array} images Ảnh sản phẩm (base64) (bắt buộc)
     * @apiParam (Body) {String} name Tên sản phẩm.
     * @apiParam (Body) {String} description Mô tả sản phẩm (bắt buộc).
     * @apiParam (Body) {String} origin Xuất xứ (bắt buộc).
     * @apiParam (Body) {String} brand Thương hiệu.
     * @apiParam (Body) {int} category_id id danh mục (bắt buộc).
     * @apiParam (Body) {int} price Giá bán (bắt buộc).
     * @apiParam (Body) {int} promotion_price Giá khuyến mại (nhỏ hơn giá bán)
     * @apiParam (Body) {int} quantity_in_stock Số lượng trong kho(bắt buộc).
     * @apiBody {Array} [images]
     * @apiBody {String} [name]
     * @apiBody {String} [description]
     * @apiBody {String} [origin]
     * @apiBody {String} [brand]
     * @apiBody {Number} [category_id]
     * @apiBody {Number} [price]
     * @apiBody {Number} [promotion_price]
     * @apiBody {Number} [quantity_in_stock]
     * @apiExample Curl
     *  curl --location --request POST "https://qhshop.xyz/api/product/add" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *      -d '{
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
     *
     *       }'
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'POST',
     *   url: 'https://qhshop.xyz/api/product/add',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     *   data: {
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
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
     * $request->setRequestUrl('https://qhshop.xyz/api/product/add');
     * $request->setRequestMethod('POST');
     * $body = new http\Message\Body;
     * $body->append('{
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
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
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10,
     * }
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("POST", "/api/product/add", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Tạo mới sản phẩm thành công"
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
     *"message": [...]
     *}
     *
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
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
            ],
            [
                'required' => 'Là bắt buộc',
                'max' => 'Trường trên tối đa :max ký tự',
                'lt' => 'Giá ưu đãi phải nhỏ hơn giá thường',
                'mimes' => 'Ảnh định dạng jpeg,jpg,png'
            ]
        );
        $this->imageService->checkTypeAndSizeImage($request->images);
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

            $images = $request->images;
            foreach ($images as $index => $image) {
                # code...
                $imageName = $this->imageService->saveImgBase64($image);
                // $image->move('images/product', $imageName);
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
    /**
     * @api {PUT} /api/product/edit Sửa mới sản phẩm
     * @apiName Edit
     * @apiGroup Product
     *
     * @apiHeader {String} Content-Type application/json
     * @apiSampleRequest https://qhshop.xyz/api/product/edit
     * @apiParam (Body) {int} id Id sản phẩm cần sửa (bắt buộc)
     * @apiParam (Body) {Array} images Ảnh sản phẩm (base64) (bắt buộc)
     * @apiParam (Body) {String} name Tên sản phẩm.
     * @apiParam (Body) {String} description Mô tả sản phẩm (bắt buộc).
     * @apiParam (Body) {String} origin Xuất xứ (bắt buộc).
     * @apiParam (Body) {String} brand Thương hiệu.
     * @apiParam (Body) {int} category_id Id danh mục (bắt buộc).
     * @apiParam (Body) {int} price Giá bán (bắt buộc).
     * @apiParam (Body) {int} promotion_price Giá khuyến mại (nhỏ hơn giá bán)
     * @apiParam (Body) {int} quantity_in_stock Số lượng trong kho(bắt buộc).
     * @apiBody {Array} [images]
     * @apiBody {String} [name]
     * @apiBody {String} [description]
     * @apiBody {String} [origin]
     * @apiBody {String} [brand]
     * @apiBody {Number} [category_id]
     * @apiBody {Number} [price]
     * @apiBody {Number} [promotion_price]
     * @apiBody {Number} [quantity_in_stock]
     * @apiExample Curl
     *  curl --location --request PUT "https://qhshop.xyz/api/product/edit" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *      -d '{
     *              "id": 22,
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
     *
     *       }'
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'PUT',
     *   url: 'https://qhshop.xyz/api/product/edit',
     *   headers: {
     *      'Content-Type': 'application/json',
     *      'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *   },
     *   data: {
     *              "id": 22,
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
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
     * $request->setRequestUrl('https://qhshop.xyz/api/product/edit');
     * $request->setRequestMethod('PUT');
     * $body = new http\Message\Body;
     * $body->append('{
     *              "id": 22,
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10
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
     *              "id": 22,
     *              "images": [...],
     *              "name": "iphone",
     *              "description" : "iphone",
     *              "origin" : "US",
     *              "brand" : "Apple",
     *              "category_id": 9,
     *              "price": 1000000,
     *              "promotion_price": 999000,
     *              "quantity_in_stock" : 10,
     * }
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("PUT", "/api/product/edit", payload, headers);
     * res = conn.getresponse()
     * data = res.read()
     * print(data.decode("utf-8"))
     *
     * @apiSuccessExample Success-Response
     * HTTP/1.1 200 OK
     * {
     *      "success": true,
     *      "message": "Tạo mới sản phẩm thành công"
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
     *"message": [...]
     *}
     *
     *HTTP/1.1 404 Not Found
     *{
     *"success": false,
     *"message": "Không tìm thấy sản phẩm"
     *}
     * 
     * HTTP/1.1 500 Internal Server Error
     *{
     * "success": false,
     * "message": "Có lỗi xảy ra. Vui lòng thử lại sau!"
     *}
     */
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
        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }
        $category = Category::find($request->category_id);
        if (empty($category)) {
            return response()->json(['success' => false, 'message' => 'Danh mục không tồn tại'], 404);
        }
        $product->name = $request->name;
        $product->brand = $request->brand;
        $product->origin = $request->origin;
        $product->price = $request->price;
        $product->promotion_price = $request->promotion_price;
        $product->description = $request->description;
        $product->enabled = $request->enabled ?? 1;
        $product->quantity_in_stock = $request->quantity_in_stock;
        #save product vào csdl
        $product->save();

        if ($request->images) {
            #xóa ảnh cũ
            $oldImages = Image::where('product_id', $id)->get();

            foreach ($oldImages as $oldImage) {
                if (file_exists($oldImage->uri)) {

                    unlink($oldImage->uri);
                }
            }

            Image::where('product_id', $id)->delete();

            #thêm ảnh mới
            $images = $request->images;
            foreach ($images as $index => $image) {
                # code...
                $imageName = $this->imageService->saveImgBase64($image);
                $dbImage = new Image;
                $dbImage->name = $imageName;
                $dbImage->product_id = $product->id;
                $dbImage->uri = "storage/uploads/products/" . $imageName;
                $dbImage->save();
            }
        }
        return response(['success' => true, 'message' => 'Sửa sản phẩm thành công']);
        // DB::beginTransaction();
        // try {

        // } catch (\Throwable $th) {
        //     dd($th);
        //     DB::rollBack();
        //     TelegramService::sendMessage(htmlentities($th->getMessage()));
        //     return response()->json([
        //         'success' => false,
        //         'message' => htmlentities($th->getMessage()),
        //     ], 500);
        // }
    }

    /**
     * @api {DELETE} /api/product/delete Xóa sản phẩm
     * @apiName Xóa sản phẩm
     * @apiGroup Product
     *
     * @apiParam (Body) {int} id Id sản phẩm cần xóa (bắt buộc)
     * @apiHeader {String} Content-Type application/json
     * @apiHeader {String} Authorization Bearer Token
     * @apiSampleRequest https://qhshop.xyz/api/product/delete
     * @apiBody {String} [id="6"]
     * @apiExample Curl
     *  curl --location --request DELETE "https://qhshop.xyz/api/product/delete" \
     *      -H  'Content-Type: application/json' \
     *      -H  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY' \
     *      -d '{
     *              "id": 6
     *       }'
     *
     * @apiExample Node.js
     * const axios = require('axios');
     * try {
     * const response = await axios({
     *   method: 'DELETE',
     *   url: 'https://qhshop.xyz/api/product/delete',
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
     * $request->setRequestUrl('https://qhshop.xyz/api/product/delete');
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
     * payload = {
     *      "id": 6
     * }
     * headers = {
     *  'Content-type': 'application/json',
     *  'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9sb2dpbiIsImlhdCI6MTYzODk4MDU4OSwiZXhwIjoxNjM4OTg0MTg5LCJuYmYiOjE2Mzg5ODA1ODksImp0aSI6IldJQTdkMEw0cmsxSjN4SkgiLCJzdWIiOjE4LCJwcnYiOiJjOGVlMWZjODllNzc1ZWM0YzczODY2N2U1YmUxN2E1OTBiNmQ0MGZjIn0.thk05uHVDUdbVOBZm0qy9bJWy0UL4JbNkO5lAz1LiuY'
     *}
     * conn.request("DELETE", "/api/product/delete", payload, headers);
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
     *
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
        if (empty($product)) {
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
                if (file_exists($oldImage->uri))
                    unlink($oldImage->uri);
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
