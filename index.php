<?php
session_start();

define('PROJECT_STATUS',true);
define('USERNAME', 'abfan1127');
define('PASSWORD', 'password');

class project_status
{
    public $storage;
    public $action;
    public $loggedin;
    public $msgs;

    public function __construct()
    {
        $this->msgs = array();

        /** these are from flash_msgs */
        if(array_key_exists('msgs', $_SESSION)) {
            $this->msgs = $_SESSION['msgs'];
        } else {
            $this->msgs = array();
        }

        if(array_key_exists('loggedin', $_SESSION)) {
            $this->loggedin = $_SESSION['loggedin'];
        } else {
            $this->loggedin = false;
        }
        /** clean out the flash messages */
        $_SESSION['msgs'] = array();


        $json = file_get_contents('storage.json');
        $this->storage(json_decode($json));
        foreach($this->get_all_projects() as $gid => $group) {
            foreach($group->projects as $key => $project){
                $group->projects[$key]->id = $gid . '_' . $key;
            }
        }

        /**
         *  actions not requiring to be logged in
         */
        if(array_key_exists('action', $_GET)) {
            switch ($_GET['action']) {
                case 'login': 
                    $this->login();
                    $this->redirect();
                    break;
                case 'logout':
                    $this->logout();
                    $this->redirect();
                    break;
                case 'update_title':
                case 'update_group_title': 
                case 'update_notes':
                case 'update_h1title':
                case 'update_percent':
                    break;
                default:
                    $this->gen_json(array('response' => false, 'data' => 'invalid action.'));

            }
        }

        if($this->loggedin()) {
            if(array_key_exists('action',$_GET)) {
                switch($_GET['action']) {
                    case 'update_title': 
                        $this->update_title();
                        return;
                        break;
                    case 'update_group_title': 
                        $this->update_group_title();
                        return;
                        break;
                    case 'update_notes':
                        $this->update_notes();
                        return;
                        break;
                    case 'update_h1title':
                        $this->update_h1title();
                        return;
                        break;
                    case 'update_percent':
                        $this->update_percent();
                        return;
                        break;
                }
            }
        }
    }



    public function update_h1title()
    {
        if(!array_key_exists('h1title', $_POST))  {
            $this->gen_json(array('response' => false, 'data' => 'H1 title must be posted.'));
            return;
        }

        $this->h1_title($_POST['h1title'])->write_storage();
        $this->gen_json(array('response' => true, 'data' => 'h1 title was saved.'));
    }

    public function update_group_title()
    {
        if(!array_key_exists('gid',$_POST) || 
           !array_key_exists('title', $_POST))  {
            $this->gen_json(array('response' => false, 'data' => 'gid and title must be posted.'));
            return;
        }

        $all_projects = $this->get_all_projects();

        if(!array_key_exists($_POST['gid'],$all_projects)) {
            $this->gen_json(array('response' => false, 'data' => 'gid not found.'));
            return;
        }

        $group = $all_projects[$_POST['gid']];

        $group->group_title = $_POST['title'];

        $this->set_all_projects($all_projects)->write_storage();
        

        $this->gen_json(array('response' => true, 'data' => 'project groups title was saved.'));
    }

    public function update_title()
    {
        if(!array_key_exists('gid',$_POST) || 
           !array_key_exists('pid', $_POST) || 
           !array_key_exists('title', $_POST))  {
            $this->gen_json(array('response' => false, 'data' => 'gid, pid, and title must be posted.'));
            return;
        }

        $all_projects = $this->get_all_projects();

        if(!array_key_exists($_POST['gid'],$all_projects)) {
            $this->gen_json(array('response' => false, 'data' => 'gid not found.'));
            return;
        }

        $group = $all_projects[$_POST['gid']];
        if(!array_key_exists($_POST['pid'],$group->projects)){
            $this->gen_json(array('response' => false, 'data' => 'pid not found.'));
            return;
        }

        $group->projects[$_POST['pid']]->title = $_POST['title'];

        $this->set_all_projects($all_projects)->write_storage();
        

        $this->gen_json(array('response' => true, 'data' => 'project title was saved.'));
    }

    public function update_percent()
    {
        if(!array_key_exists('gid',$_POST) || 
           !array_key_exists('pid', $_POST) || 
           !array_key_exists('percent', $_POST))  {
            $this->gen_json(array('response' => false, 'data' => 'gid, pid, and percent must be posted.'));
            return;
        }

        $all_projects = $this->get_all_projects();

        if(!array_key_exists($_POST['gid'],$all_projects)) {
            $this->gen_json(array('response' => false, 'data' => 'gid not found.'));
            return;
        }

        $group = $all_projects[$_POST['gid']];
        if(!array_key_exists($_POST['pid'],$group->projects)){
            $this->gen_json(array('response' => false, 'data' => 'pid not found.'));
            return;
        }

        $precent_raw = preg_replace('/[^0-9]/','',$_POST['percent']);
        $precent = round($precent_raw / 10) * 10;

        $group->projects[$_POST['pid']]->complete = (string) $precent;

        $this->set_all_projects($all_projects)->write_storage();
        

        $this->gen_json(array('response' => true, 'data' => 'project precentage was saved.'));
    }

    public function update_notes()
    {
        if(!array_key_exists('gid',$_POST) || 
           !array_key_exists('pid', $_POST) || 
           !array_key_exists('notes', $_POST))  {
            $this->gen_json(array('response' => false, 'data' => 'gid, pid, and notes must be posted.'));
            return;
        }

        $all_projects = $this->get_all_projects();

        if(!array_key_exists($_POST['gid'],$all_projects)) {
            $this->gen_json(array('response' => false, 'data' => 'gid not found.'));
            return;
        }

        $group = $all_projects[$_POST['gid']];
        if(!array_key_exists($_POST['pid'],$group->projects)){
            $this->gen_json(array('response' => false, 'data' => 'pid not found.'));
            return;
        }

        $group->projects[$_POST['pid']]->notes = $_POST['notes'];

        $this->set_all_projects($all_projects)->write_storage();
        

        $this->gen_json(array('response' => true, 'data' => 'project notes was saved.'));
    }
    
    private function gen_json($data)
    {
        

        /*if($data['response']) {
            header("HTTP/1.0 400 " + $data['data']);
            header("Content-Type: text/javascript; charset=utf-8");
            echo json_encode($data['data']);
            exit;
        } else {*/
            header("HTTP/1.0 200 OK");
            header("Content-Type: text/javascript; charset=utf-8");
            echo json_encode($data);
            exit;
        //}
    }

    public function login()
    {
        if(array_key_exists('username',$_POST) && array_key_exists('password',$_POST)) {
            if(($_POST['username'] == USERNAME) && ($_POST['password'] == PASSWORD)) {
                $_SESSION['loggedin'] = true;
                $this->loggedin(true);
                $this->add_flash_msg('success','You successfullly logged in.');
                return true;
            }
        }

        $_SESSION['loggedin'] = false;
        $this->loggedin(false);
        $this->add_flash_msg('error','login failed. try again.');
        return false;
    }

    public function logout()
    {
        $_SESSION['loggedin'] = false;
        $this->loggedin(false);
        $this->add_flash_msg('info','You are successfully logged out.');
        return;
    }

    public function add_flash_msg($level, $msg)
    {
        $_SESSION['msgs'][] = array('level' => $level, 'msg' => $msg);
    }

    public function add_msg($level, $msg)
    {
        $this->msgs[] = array('level' => $level, 'msg' => $msg);
    }

    public function get_msgs()
    {
        return $this->msgs;
    }

    /**
     *  provides a simple redirect function
     *  stolen from CodeIgniter framework
     */
    public function redirect($uri = null, $method = 'location', $http_response_code = 302)
    {
        if(is_null($uri)) {
            $uris = explode('?',$_SERVER['REQUEST_URI']);
            $uri = $uris[0];
        }
        switch($method)
        {
            case 'refresh'  : header("Refresh:0;url=".$uri);
                break;
            default         : header("Location: ".$uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }

    public function write_storage()
    {
        $storage = $this->storage();
        $storage->last_updated = date('m/d/Y h:i:s T');
        $string = json_encode($storage);
        file_put_contents('storage.json',$string);
        return;
    }

    public function loggedin($loggedin = null)
    {
        if(is_null($loggedin)) {
            return $this->loggedin;
        } else {
            $this->loggedin = $loggedin;
            return $this;
        }
    }

    public function h1_title($h1 = null)
    {
        if(is_null($h1)) {
            return $this->storage->h1;
        } else {
            $this->storage->h1 = $h1;
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

    public function set_all_projects($projects)
    {
        $this->storage->projects = $projects;
        return $this;
    }
}


$project_status = new project_status;

include('view.php');