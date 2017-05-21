<?php

namespace App\Models\Courses;

class CourseContent extends \App\Models\BaseModel
{
    protected $table = "course_content";
    protected $column = ['id', 'title', 'title_slug','course_id', 'url_video', 'deleted'];

    public function add($courseId, $dataVideo)
    {
        foreach ($dataVideo as $key => $value) {
            $data = [
                'course_id'     => $courseId,
                'title'         => $value[0],
                'title_slug'    => preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($value[0])),
                'url_video'     => $value[1],
            ];

            $addCourseContent = $this->create($data);
        }
        
        return $addCourseContent;
    }

    public function edit($data, $course_content_id, $video = null)
    {
        $edit = [
            'title'       =>  $data['title'],
            'title_slug'  =>  preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($data['title'])),
            'url_video'   =>  $video,
        ];

        $find = $this->find('id', $course_content_id)->withoutDelete()->fetch();
        
        if ($find['title'] == $edit['title']) {
            unset($edit['title']);
            unset($edit['title_slug']);
        }

        if ($video == null) {
            unset($edit['url_video']);
        }

        return $this->update($edit, 'id', $find['id']);
    }
}

?>