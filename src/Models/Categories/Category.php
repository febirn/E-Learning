<?php

namespace App\Models\Categories;

class Category extends \App\Models\BaseModel
{
    protected $table = 'categories';
    protected $column = ['id', 'name'];

    public function add(array $data)
    {
        foreach ($data as $key => $value) {
            $find = $this->find('name', ucwords($value))->fetch();
            if (!$find) {
                $create['name'] = ucwords($value);
                $category[$value] = $this->create($create);
            } elseif ($find) {
                $category[$value] = $find['id'];
            }
                
        }
        
        return $category;
    }

}

?>