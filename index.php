<?php

define('project_status',true);


class project_status
{
    public $storage;

    public function __construct()
    {
        $json = file_get_contents('storage.json');
        $this->storage(json_decode($json));
    }


    public function write_storage()
    {
        $string = json_encode($this->storage());
        file_put_storage('storage.json',$string);
        return;
    }

    public function title($title = null)
    {
        if(is_null($title)) {
            return $this->storage->title;
        } else {
            $this->storage->title = $title;
            return $this;
        }
    }

    public function last_updated($date = null)
    {
        if(is_null($date)){
            return $this->storage->last_updated;
        } else {
            $this->storage->last_updated;
            return $this;
        }
    }

    public function storage($storage = null)
    {
        if(is_null($storage)) {
            return $this->storage;
        } else {
            $this->storage = $storage;
            return $this;
        }
    }

    public function get_all_projects()
    {
        return $this->storage->projects;
    }
}


$project_status = new project_status;


include('view.php');