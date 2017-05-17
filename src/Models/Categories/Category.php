<?php

namespace App\Models\Categories;

class Category extends \App\Models\BaseModel
{
    protected $table = 'categories';
    protected $column = ['id', 'name', 'slug'];
    public function add(array $data)
    {
        foreach ($data as $key => $value) {
            $find = $this->find('name', ucwords($value))->fetch();
            if (!$find && !in_array($value, $find)) {
                $create['name'] = ucwords($value);
                $create['slug'] = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($value));
                $category[$value] = $this->create($create);
            } elseif ($find) {
                $category[$value] = $find['id'];
            }
                
        }
        
        return $category;
    }
}
?>