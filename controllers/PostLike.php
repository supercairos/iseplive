<?php

class PostLike_Controller extends Controller {

    public function add($params) {

        $attachment_id = isset($_POST['attachment']) && ctype_digit($_POST['attachment']) ? (int) $_POST['attachment'] : null;
        
        $this->setView('add.php');
        if (!isset(User_Model::$auth_data))
            throw new Exception('You must be logged in');
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to post a comment');
        try {
            $id = $this->model->add((int) $params['post_id'], (int) User_Model::$auth_data['id'], $attachment_id);
            if (is_numeric($id) && !is_null($id))
                $this->set(array('success' => true));
            else
                $this->set(array('success' => false));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return true;
    }

    public function delete($params) {

        $this->setView('add.php');
        if (!isset(User_Model::$auth_data))
            throw new Exception('You must be logged in');
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to post a comment');
        try {
            $id = $this->model->delete($params['post_id'], (int) User_Model::$auth_data['id']);
            $this->set(array('success' => true));
        } catch (Exception $e) {
            //echo $e->getMessage();
            $this->set(array('success' => true));
        }
        return true;
    }

}

?>
