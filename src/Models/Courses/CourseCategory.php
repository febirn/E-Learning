<?php

namespace App\Models\Courses;

class CourseCategory extends \App\Models\BaseModel
{
    protected $table = "course_category";
    protected $column = ['id', 'course_id', 'category_id'];

    public function add($courseId, $categoryId)
    {
        $data = [
            'course_id' => $courseId,
        ];

        foreach ($categoryId as $key => $value) {
            $data['category_id'] = $value;
            $this->create($data);
        }
    }

    public function edit($courseId, $categoriesId)
    {
        $data = [
            'course_id' => $courseId,
        ];

        $find = $this->find('course_id', $courseId)->fetchAll();

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
                $qb = $this->getBuilder();
                $qb->delete($this->table)
                    ->where('category_id = :category_id')
                    ->andWhere('course_id = :course_id')
                    ->setParameter(':category_id', $value)
                    ->setParameter(':course_id', $courseId)
                    ->execute();
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