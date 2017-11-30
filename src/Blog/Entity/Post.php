<?php
namespace App\Blog\Entity;

class Post
{


    public $id;


    public $title;


    public $slug;


    public $header;


    public $content;


    public $created_at;


    public $updated_at;


    public $category_name;


    public $image;


    public function __construct()
    {
        if ($this->created_at) {
            $this->created_at = new \DateTime($this->created_at);
        }
        if ($this->updated_at) {
            $this->updated_at = new \DateTime($this->updated_at);
        }
    }


    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }
}
