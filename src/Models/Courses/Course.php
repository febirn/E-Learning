<?php

namespace App\Models\Courses;

class Course extends \App\Models\BaseModel
{
    protected $table = "courses";
    protected $column = ['id', 'user_id', 'title', 'title_slug', 'type', 'url_source_code', 'create_at', 'update_at', 'deleted'];
    protected $check = ["title_slug"];

    public function add(array $data)
    {
        $data = [
            'user_id'           =>  $data['user_id'],
            'title'             =>  $data['title'],
            'title_slug'        =>  preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['title'])),
            'url_source_code'   =>  $data['url_source_code'],
        ];

        return $this->checkOrCreate($data);
    }

    public function getCourse($slug)
    {
        $qb = $this->getBuilder();

        $course = $this->find('title_slug', $slug)->fetch();

        $categories = $qb->select('c.name as category')
             ->from('categories', 'c')
             ->innerJoin('c', 'course_category', 'cc', 'c.id = cc.category_id')
             ->innerJoin('cc', 'courses', 'crs', 'cc.course_id = crs.id')
             ->where('cc.course_id = :id')
             ->setParameter(':id', $course['id'])
             ->execute()
             ->fetchAll();

        foreach ($categories as $key => $value) {
            $category[] = $value['category'];
        }
        
        $course['category'] = $category;

        return $course;
    }

    public function edit($data, $slug)
    {
        $data = [
            'title'         => $data['title'],
            'title_slug'    => preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['title'])),
        ];

        $find = $this->find('title_slug', $slug)->fetch();

        if ($find['title'] == $data['title']) {
            unset($data['title']);
            unset($data['title_slug']);
        }

        return $this->checkOrUpdate($data, 'title_slug', $slug);
    }
}



?>