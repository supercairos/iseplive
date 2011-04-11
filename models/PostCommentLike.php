<?php

class PostCommentLike_Model extends Model {

    public static function clearCache() {
        if ($cache_list = Cache::read('posts-cachelist')) {
            foreach ($cache_list as $cache_entry)
                Cache::delete($cache_entry);
            Cache::delete('posts-cachelist');
        }
    }

    public function add($post_id, $user_id, $comment_id, $attachment_id=null) {

        if (isset($attachment_id)) {
            $attachment = DB::createQuery('attachments')->select($attachment_id);
            if (!isset($attachment[0]))
                throw new Exception('Attachment not found!');
        }else {
            $attachment_id = null;
        }

        $already = $this->createQuery()
                        ->where(array(
                            'post_id' => $post_id,
                            'user_id' => $user_id,
                            'comment_id' => $comment_id,
                            'attachement_id' => $attachment_id
                        ))->select();

        if (!empty($already))
            throw new Exception('Already Liked !');
        $id = $this->createQuery()
                        ->set(array(
                            'post_id' => $post_id,
                            'user_id' => $user_id,
                            'comment_id' => $comment_id,
                            'attachement_id' => $attachment_id
                        ))->insert();
        self::clearCache();
        return $id;
    }

    public function delete($post_id, $user_id, $comment_id) {

        $id = $this->createQuery()
                        ->where(array(
                            'post_id' => $post_id,
                            'user_id' => $user_id,
                            'comment_id' => $comment_id
                        ))->delete();
        self::clearCache();
        return $id;
    }

    public function get($id) {
        $likes = $this->createQuery()->select($id);
        if (!isset($likes[0]))
            throw new Exception('Like not found');
        return $likes[0];
    }

}
?>
