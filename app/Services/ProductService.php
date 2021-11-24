<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
   
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    
    public function search($data)
    {
        $query = $this->product;
        if (isset($data['select']) && count($data['select']) > 0) {
            $query = $query->select($data['select']);
        }
        if (isset($data['conditions']) && count($data['conditions']) > 0) {
            $conditions = $data['conditions'];
            foreach ($conditions as $condition) {
                $operation = isset($condition['operation']) ? $condition['operation'] : '=';
                switch ($operation) {
                    case 'like':
                        $query = $query->where($condition['key'], 'like', '%' . $condition['value'] . '%');
                        break;
                    case 'in':
                        $query = $query->whereIn($condition['key'], $condition['value']);
                        break;
                    case 'between':
                        $query = $query->whereBetween($condition['key'],[(int)$condition['value']['start'], (int)$condition['value']['end']]);
                        break;
                    case '=':
                        $query = $query->where($condition['key'], $condition['value']);
                        break;
                    default:
                        $query = $query->where($condition['key'], $operation, $condition['value']);
                }
            }
        }
        if (isset($data['sortBy']) && $data['sortBy'] != '') {
            $query = $query->orderBy($data['sortBy'], isset($data['sortOrder']) ? $data['sortOrder'] : 'DESC');
        }
        if($data['paginate'] == true){
            $data = $query->paginate(isset($data['limit']) ? (int)$data['limit'] : 30);
        }else{
            $data = $query->limit(isset($data['limit']) ? (int)$data['limit'] : 30)->get();
        }
        
        return $data;
    }
    public function create($data)
    {
        $product = $this->product;
        foreach ($data as $key => $value) {
            $product->$key = $value;
        }
        $product->save();
        return $product;
    }

    public function get($data)
    {
        $query = $this->product;
        if (isset($data['conditions']) && count($data['conditions']) > 0) {
            $conditions = $data['conditions'];
            foreach ($conditions as $condition) {
                $operation = isset($condition['operation']) ? $condition['operation'] : '=';
                switch ($operation) {
                    case 'like':
                        $query = $query->where($condition['key'], 'like', '%' . $condition['value'] . '%');
                        break;
                    case 'in':
                        $query = $query->whereIn($condition['key'], $condition['value']);
                        break;
                    default:
                        $query = $query->where($condition['key'], $operation, $condition['value']);
                }
            }
        }
        if (isset($data['sortBy']) && $data['sortBy'] != '') {
            $query = $query->orderBy($data['sortBy'], isset($data['sortOrder']) ? $data['sortOrder'] : 'DESC');
        }
        $data = $query->paginate(isset($data['limit']) ? (int)$data['limit'] : 30);
        return $data;
    }

    public function first($data)
    {
        $query = $this->product;
        foreach ($data as $key => $value) {
            if(is_array($value)){
                $query = $query->whereIn($key, $value);
            }else{
                $query = $query->where($key, $value);
            }
        }
        $data = $query->first();
        return $data;
    }

    public function edit($product , $data)
    {
        foreach ($data as $key => $value) {
            $product->$key = $value;
        }
        $product->save();
        return $product;
    }
}