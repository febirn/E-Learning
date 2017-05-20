<?php

namespace App\Models\Courses;

class Course extends \App\Models\BaseModel
{
    protected $table = "courses";
    protected $column = ['id', 'user_id', 'title', 'title_slug', 'type', 'url_source_code', 'create_at', 'update_at', 'deleted'];
    protected $check = ["title_slug"];


    public function getAllJoin($page, $limit)
    {
        $course = $this->getAll()->descSort()->paginate($page, $limit);
 
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
            'type'              =>  $data['type'],
            'url_source_code'   =>  $data['url_source_code'],
        ];

        return $this->checkOrCreate($data);
    }

    public function edit($data, $slug)
    {
        $edit = [
            'title'             =>  $data['title'],
            'title_slug'        =>  preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['title'])),
            'type'              =>  $data['type'],
            'url_source_code'   =>  $data['url_source_code'],
        ];

        $find = $this->find('title_slug', $slug)->withoutDelete()->fetch();

        if ($find['title'] == $edit['title']) {
            unset($edit['title']);
            unset($edit['title_slug']);
        }

        return $this->checkOrUpdate($edit, 'id', $find['id']);
    }

    public function getCourse($slug)
    {
        $qb = $this->getBuilder();

        $course = $this->find('title_slug', $slug)->fetch();

        if (!$course) {
            return false;
        }

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


    public function showByCategory($category, int $page, int $limit)
    {
        $qbCourse = $this->getBuilder();

        $this->query = $qbCourse->select('c.*', 'u.username')
                        ->from($this->table, 'c')
                        ->innerJoin('c', 'course_category', 'cc', 'cc.course_id = c.id')
                        ->innerJoin('c', 'users', 'u', 'c.user_id = u.id')
                        ->innerJoin('cc', 'categories', 'ctg', 'ctg.id = cc.category_id')
                        ->where('ctg.name = :category')
                        ->andWhere('c.deleted = 0')
                        ->setParameter(':category', $category);

        $course = $this->paginate($page, $limit);

        if (!$course) {
            return false;
        }

        foreach ($course['data'] as $keyCourse => $valueCourse) {
            $qb = $this->getBuilder();

            $categories = $qb->select('c.name as category')
               ->from('categories', 'c')
               ->innerJoin('c', 'course_category', 'ac', 'c.id = ac.category_id')
               ->innerJoin('ac', 'courses', 'a', 'ac.course_id = a.id')
               ->where('a.id = :id AND a.deleted = 0')
               ->setParameter(':id', $valueCourse['id'])
               ->execute()
               ->fetchAll();

            foreach ($categories as $keyCategory => $valueCategory) {
                $course['data'][$keyCourse]['category'][] = $valueCategory['category'];
            }
        }
        
        return $course;
    }

    public function search($search, int $page, int $limit)
    {
        $qbCourse = $this->getBuilder();
        $this->query = $qbCourse->select('c.*', 'u.username')
                        ->from($this->table, 'c')
                        ->innerJoin('c', 'users', 'u', 'c.user_id = u.id')
                        ->where("c.title LIKE " . "'" . "%$search%" ."'")
                        ->andWhere('c.deleted = 0');

        $course = $this->paginate($page, $limit);

        if (!$course) {
            return false;
        }

        foreach ($course['data'] as $keyCourse => $valueCourse) {
            $qb = $this->getBuilder();
            $categories = $qb->select('c.name as category')
               ->from('categories', 'c')
               ->innerJoin('c', 'course_category', 'ac', 'c.id = ac.category_id')
               ->innerJoin('ac', 'courses', 'a', 'ac.course_id = a.id')
               ->where('a.id = :id AND a.deleted = 0')
               ->setParameter(':id', $valueCourse['id'])
               ->execute()
               ->fetchAll();

            foreach ($categories as $keyCategory => $valueCategory) {
                $course['data'][$keyCourse]['category'][] = $valueCategory['category'];
            }
        }
        
        return $course;
    }

    public function getCourseBySlug($slug)
    {
        $qb = $this->getBuilder();

        $course = $qb->select('crsu.*', 'u.username', 'u.name')
            ->from('courses', 'crsu')
            ->innerJoin('crsu', 'users', 'u', 'crsu.user_id = u.id')
            ->where('crsu.title_slug = :slug')
            ->setParameter(':slug', $slug)
            ->execute()
            ->fetch();

        if (!$course) {
            return false;
        }

        $categories = $qb->select('DISTINCT c.name as category')
            ->from('categories', 'c')
            ->innerJoin('c', 'course_category', 'cc', 'c.id = cc.category_id')
            ->innerJoin('cc', 'courses', 'crs', 'cc.course_id = crs.id')
            ->where('cc.course_id = :id')
            ->setParameter(':id', $course['id'])
            ->execute()
            ->fetchAll();

        foreach ($categories as $categoryKey => $categoryValue) {
            $category[] = $categoryValue['category'];
        }

        $videos = $qb->select('DISTINCT csrctn.title', 'csrctn.url_video')
            ->from('course_content', 'csrctn')
            ->innerJoin('csrctn', 'courses', 'csr', 'csrctn.course_id = csr.id')
            ->where('csrctn.course_id = :id')
            ->setParameter(':id', $course['id'])
            ->execute()
            ->fetchAll();
        
        $course['category'] = $category;
        $course['video'] = $videos;

        return $course;
    }
}



?>