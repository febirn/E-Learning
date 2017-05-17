<?php

namespace App\Models\Courses;

class CourseContent extends \App\Models\BaseModel
{
    protected $table = "course_content";
    protected $column = ['id', 'title', 'title_slug','course_id', 'url_video', 'deleted'];
    protected $check = ['title_slug'];

    public function add($courseId, $dataVideo)
    {
        foreach ($dataVideo as $key => $value) {
            $data = [
                'course_id'     => $courseId,
                'title'         => $value[0],
                'title_slug'    => preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($value[0])),
                'url_video'     => $value[1],
            ];

            $addCourseContent = $this->checkOrCreate($data);
        }
        
        return $addCourseContent;
    }
}

?>