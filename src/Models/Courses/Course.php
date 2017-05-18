<?php

namespace App\Models\Courses;

class Course extends \App\Models\BaseModel
{
    protected $table = "courses";
    protected $column = ['id', 'user_id', 'title', 'title_slug', 'type', 'url_source_code', 'create_at', 'update_at', 'deleted'];
    protected $check = ["title_slug"];


    public function getAllJoin($page, $limit)
    {
        $course = $this->getAll()->paginate($page, $limit);
 
        if (!$course) {
            return false;
        }

        foreach ($course['data'] as $keyCourse => $valueCourse) {
            $qb = $this->getBuilder();
            $categories = $qb->select('c.name as category')
               ->from('categories', 'c')
               ->innerJoin('c', 'course_category', 'cc', 'c.id = cc.category_id')
               ->innerJoin('cc', 'courses', 'csr', 'cc.course_id = csr.id')
               ->where('csr.id = :id AND csr.deleted = 0')
               ->setParameter(':id', $valueCourse['id'])
               ->execute()
               ->fetchAll();

            foreach ($categories as $keyCategory => $valueCategory) {
            $course['data'][$keyCourse]['category'][] = $valueCategory['category'];
            }
        }
        
        return $course;
    }

    public function getCourseByUserId($userId, int $page, int $limit)
    {
        $course = $this->find('user_id', $userId)->withoutDelete()->paginate($page, $limit);

        if (!$course) {
            return false;
        }

        foreach ($course['data'] as $keyCourse => $valueCourse) {
            $qb = $this->getBuilder();
            $categories = $qb->select('c.name as category')
               ->from('categories', 'c')
               ->innerJoin('c', 'course_category', 'cc', 'c.id = cc.category_id')
               ->innerJoin('cc', 'courses', 'csr', 'cc.course_id = csr.id')
               ->where('csr.id = :id AND csr.deleted = 0')
               ->setParameter(':id', $valueCourse['id'])
               ->execute()
               ->fetchAll();

            foreach ($categories as $keyCategory => $valueCategory) {
            $course['data'][$keyCourse]['category'][] = $valueCategory['category'];
            }
        }

        return $course;
    }

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

    public function getTrashByUserId($userId)
    {
        $course = $this->find('user_id', $userId)->withDelete()->fetchAll();
        
        if (!$course) {
            return false;
        }

        foreach ($course['data'] as $keyCourse => $valueCourse) {
            $qb = $this->getBuilder();
            $categories = $qb->select('c.name as category')
               ->from('categories', 'c')
               ->innerJoin('c', 'course_category', 'cc', 'c.id = cc.category_id')
               ->innerJoin('cc', 'courses', 'csr', 'cc.course_id = csr.id')
               ->where('csr.id = :id AND csr.deleted = 1')
               ->setParameter(':id', $valueCourse['id'])
               ->execute()
               ->fetchAll();

            foreach ($categories as $keyCategory => $valueCategory) {
                $course['data'][$keyCourse]['category'][] = $valueCategory['category'];
            }
        }
        
        return $course;
    }
}



?>