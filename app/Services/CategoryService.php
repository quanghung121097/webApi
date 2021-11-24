<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
   
    private $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    

    public function create($data)
    {
        $category = $this->category;
        foreach ($data as $key => $value) {
            $category->$key = $value;
        }
        $category->save();
        return $category;
    }

    public function get($data)
    {
        $query = $this->category;
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
        $query = $this->category;
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

    public function edit($category , $data)
    {
        foreach ($data as $key => $value) {
            $category->$key = $value;
        }
        $category->save();
        return $category;
    }
}