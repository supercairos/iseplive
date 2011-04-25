<?php

class PostLike_Controller extends Controller {

    public function add($params) {

        $attachment_id = (isset($_POST['attachment']) && ctype_digit($_POST['attachment']) && $_POST['attachment'] > 0) ? (int) $_POST['attachment'] : null;
        
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

        $attachment_id = isset($_POST['attachment']) && ctype_digit($_POST['attachment']) && ($_POST['attachment'] > 0) ? 'attachment_id = '.(int) $_POST['attachment'] : 'attachment_id IS NULL';
        
        $this->setView('delete.php');
        if (!isset(User_Model::$auth_data))
            throw new Exception('You must be logged in');
        if (!isset(User_Model::$auth_data['student_number']))
            throw new Exception('You must be a student to delete your likes.');
        try {
            $id = $this->model->delete($params['post_id'], (int) User_Model::$auth_data['id'], $attachment_id);
            $this->set(array('success' => true));
        } catch (Exception $e) {
            //echo $e->getMessage();
            $this->set(array('success' => true));
        }
        return true;
    }

}

?>
