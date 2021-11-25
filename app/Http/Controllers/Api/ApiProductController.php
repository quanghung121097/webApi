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
    }

    public function search(Request $request)
    {
        $data['page'] = $request->page ?? 1;
        $data['limit'] = $request->limit ?? 1;
        $data['select'] = $request->select ?? [];
        $data['sortBy'] =  $request->sortBy ?? 'created_at';
        $data['paginate'] =  $request->paginate == true ? true : false;
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
            return response(['success' => false, 'message' => 'Thiếu id sản phẩm']);
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
                // 'images' => 'mimes:jpeg,jpg,png'
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
        DB::beginTransaction();
        try {
            $listCategory = Category::all();
            $product = Product::find($id);
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
    public function delete(Request $request)
    {
        $id = $request->id;
        if (empty($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu id sản phẩm',
            ], 400);
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
