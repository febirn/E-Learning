<?php

namespace App\Models\Articles;

class ArticleCategory extends \App\Models\BaseModel
{
    protected $table = 'article_category';
    protected $column = ['id', 'article_id', 'category_id'];

    public function add($articleId, $categoriesId)
    {
        $data = [
            'article_id'    => $articleId,
        ];

        foreach ($categoriesId as $key => $value) {
            $data['category_id'] = $value;
            $this->create($data);
        }
    }

    public function edit($articleId, $categoriesId)
    {
        $data = [
            'article_id'    => $articleId,
        ];

        $find = $this->find('article_id', $articleId)->fetchAll();

        foreach ($find as $key => $value) {
            $categoryId[$value['category_id']] = $value['category_id'];
        }
        
        foreach ($categoriesId as $key => $value) {
            $editCategory[$value] = $value;
        }

        $diffA = array_diff($categoryId, $editCategory);
        $diffB = array_diff($editCategory, $categoryId);

        if ($diffA) {
            foreach ($diffA as $key => $value) {
                $this->hardDelete('category_id', $value);
            }
        }

        if ($diffB) {
            foreach ($diffB as $key => $value) {
                $data['category_id'] = $value;
                $this->create($data);
            }
        }


    }
}

?>