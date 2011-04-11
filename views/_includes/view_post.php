
<div id="post-<?php echo $post['id']; ?>" class="post">

    <?php
    if (isset($post['group_id']) && $post['official'] == '1') {
        $post_user_url = Config::URL_ROOT . Routes::getPage('group', array('group' => $post['group_url']));
        $post_user_name = $post['group_name'];
    } else {
        $post_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $post['username']));
        $post_user_name = isset($post['firstname']) ? $post['firstname'] . ' ' . $post['lastname'] : $post['username'];
    }

    if (isset($post['avatar_url'])) {
        ?>
        <a href="<?php echo $post_user_url; ?>" class="avatar"><img src="<?php echo $post['avatar_url']; ?>" alt="" /></a>
        <?php
    }


// Post delete button
    if (($is_logged && $username == $post['username'])
            || $is_admin
            || (isset($post['group_id']) && isset($groups_auth)
            && isset($groups_auth[(int) $post['group_id']])) && $groups_auth[(int) $post['group_id']]['admin']) {
        ?>
        <a href="<?php echo Config::URL_ROOT . Routes::getPage('post_delete', array('id' => $post['id'])); ?>" class="post-delete">x</a>
        <?php
    }
    ?>

    <div class="post-message">
        <a href="<?php echo $post_user_url; ?>" class="post-username"><?php echo htmlspecialchars($post_user_name); ?></a>
        <?php echo Text::inHTML($post['message']); ?>

        <?php
// Event
        if (isset($post['event'])) {
            ?>
            <div class="event">
                <img src="<?php echo Config::URL_STATIC; ?>images/icons/event.png" alt="" class="icon" /> <strong><?php echo htmlspecialchars($post['event']['title']); ?></strong><br />
                <?php echo Date::event(strtotime($post['event']['date_start']), strtotime($post['event']['date_end'])); ?>
            </div>
            <?php
        }



// Survey
        if (isset($post['survey'])) {
            ?>
            <form action="<?php echo Config::URL_ROOT . Routes::getPage('survey_vote', array('id' => $post['survey']['id'])); ?>" class="survey" method="post">
                <img src="<?php echo Config::URL_STATIC; ?>images/icons/survey.png" alt="" class="icon" /> <strong><?php echo htmlspecialchars($post['survey']['question']); ?></strong><br />
                <ul>
                    <?php
                    $ended = strtotime($post['survey']['date_end']) < time();
                    $total_votes = 0;
                    $voting = array();
                    foreach ($post['survey']['answers'] as &$answer) {
                        $total_votes += (int) $answer['nb_votes'];
                        $answer['votes'] = $answer['votes'] == '' ? array() : json_decode($answer['votes'], true);
                        $voting = array_unique(array_merge($voting, $answer['votes']));
                    }
                    $nb_voting = count($voting);

                    foreach ($post['survey']['answers'] as &$answer) {
                        // Results
                        if ($post['survey']['multiple'] == '1')
                            $perc = $nb_voting == 0 ? 0 : ((int) $answer['nb_votes']) / $nb_voting;
                        else
                            $perc = $total_votes == 0 ? 0 : ((int) $answer['nb_votes']) / $total_votes;
                        $perc_s = round(100 * $perc);
                        /*
                         * 	Graph of colors
                         * 	|\  /\  /    <-- blue, then red
                         * 	| \/  \/
                         * 	| /\  /\
                         * 	|/  \/  \    <-- green
                         * 	------------
                         */
                        if ($perc < 0.5) {
                            $red = '00';
                            $green = str_pad(dechex(255 * $perc * 2), 2, '0', STR_PAD_LEFT);
                            $blue = str_pad(dechex(255 * (1 - $perc * 2)), 2, '0', STR_PAD_LEFT);
                        } else {
                            $red = str_pad(dechex(255 * ($perc - 0.5) * 2), 2, '0', STR_PAD_LEFT);
                            $green = str_pad(dechex(255 * (1 - ($perc - 0.5) * 2)), 2, '0', STR_PAD_LEFT);
                            $blue = '00';
                        }
                        ?>
                        <li class="survey-answer-result">
                            <?php echo htmlspecialchars($answer['answer']); ?><br />
                            <div class="answer-bar">
                                <div style="width: <?php echo $perc_s; ?>%; background-color: #<?php echo $red . $green . $blue; ?>;">
                                    &nbsp;<?php echo $perc_s; ?>%
                                </div>
                            </div>
                            <?php echo __('POST_SURVEY_NB_VOTES', array('votes' => $answer['nb_votes'])); ?>
                        </li>
                        <?php
                        // Form to votes
                        if ($is_student && !$ended) {
                            ?>
                            <li class="survey-answer-vote">
                                <label>
                                    <input type="<?php echo $post['survey']['multiple'] == '1' ? 'checkbox' : 'radio'; ?>" name="answer<?php echo $post['survey']['multiple'] == '1' ? '-' . $answer['id'] : ''; ?>" value="<?php echo $answer['id']; ?>"<?php if (in_array($username, $answer['votes']))
                    echo ' checked="checked"'; ?> />
                                           <?php echo htmlspecialchars($answer['answer']); ?>
                                </label>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>

                <?php
                if ($is_student && !$ended) {
                    ?>
                    <div class="survey-choice-vote">
                        <input type="submit" value="<?php echo __('POST_SURVEY_SUBMIT'); ?>" />
                                                                		- <a href="javascript:;"><?php echo __('POST_SURVEY_SHOW_RESULTS'); ?></a>
                    </div>
                    <div class="survey-choice-results">
                        <a href="javascript:;"><?php echo __('POST_SURVEY_SHOW_VOTE'); ?></a>
                    </div>
                    <?php
                }
                ?>
                <?php echo __('POST_SURVEY_ENDING_DATE') . ' ' . Date::dateHour(strtotime($post['survey']['date_end'])); ?>
            </form>
            <?php
        }



// Attachments
        if (!isset($post['attachments']))
            $post['attachments'] = array();
        $nb_photos = 0;

        foreach ($post['attachments'] as $attachment) {
            switch ($attachment['ext']) {



                // Photo
                // see: http://flash-mp3-player.net/players/maxi/
                case 'jpg':
                case 'gif':
                case 'png':
                    if ($nb_photos == 0) {
                        ?>
                        <div class="photos">
                            <?php
                        }
                        ?>
                        <a href="<?php echo Config::URL_ROOT . Routes::getPage('post', array('id' => $post['id'])) . '#photo-' . $attachment['id']; ?>"><img src="<?php echo $attachment['thumb']; ?>" alt="" /></a>
                        <?php
                        $nb_photos++;
                        if (!isset($one_post) && $nb_photos == Config::PHOTOS_PER_POST && Config::PHOTOS_PER_POST < $post['attachments_nb_photos']) {
                            ?>
                            <a href="<?php echo Config::URL_ROOT . Routes::getPage('post', array('id' => $post['id'])); ?>" class="photos-more"><?php echo __('POST_LINK_PHOTOS', array('nb' => $post['attachments_nb_photos'])); ?></a>
                        </div>
                        <?php
                    } else if ($nb_photos == $post['attachments_nb_photos']) {
                        ?>
                    </div>
                    <?php
                }
                break;




            // Video
            // see: http://flv-player.net/players/maxi/
            case 'flv':
                ?>
                <object data="<?php echo Config::URL_STATIC; ?>players/player_flv_maxi.swf" width="480" height="360" type="application/x-shockwave-flash" class="video">
                    <param name="movie" value="<?php echo Config::URL_STATIC; ?>players/player_flv_maxi.swf" />
                    <param name="allowfullscreen" value="true" />
                    <param name="wmode" value="transparent" />
                    <param name="base" value="." />
                    <param name="flashvars" value="flv=<?php echo urlencode($attachment['url']); ?>&amp;startimage=<?php echo urlencode($attachment['thumb']); ?>&amp;margin=0&amp;showvolume=1&amp;showtime=1&amp;showtime=autohide&amp;showloading=always&amp;showfullscreen=1&amp;showmouse=always&amp;showiconplay=1&amp;iconplaycolor=7F82CA&amp;iconplaybgcolor=E4E5FF&amp;iconplaybgalpha=80&amp;playercolor=E4E5FF&amp;bgcolor1=E4E5FF&amp;bgcolor2=D4D6FF&amp;slidercolor1=#3F3D6A&amp;slidercolor2=242153&amp;sliderovercolor=15123C&amp;buttoncolor=242153&amp;buttonovercolor=15123C&amp;textcolor=242153" />
                </object>
                <?php
                break;




            // Audio
            case 'mp3':
                ?>
                <object data="<?php echo Config::URL_STATIC; ?>players/player_mp3_maxi.swf" width="300" height="20" type="application/x-shockwave-flash" class="audio">
                    <param name="movie" value="<?php echo Config::URL_STATIC; ?>players/player_mp3_maxi.swf" />
                    <param name="wmode" value="transparent" />
                    <param name="flashvars" value="mp3=<?php echo urlencode($attachment['url']); ?>&amp;width=300&amp;showstop=0&amp;showinfo=0&amp;showvolume=1&amp;showloading=autohide&amp;volumewidth=40&amp;bgcolor1=E4E5FF&amp;bgcolor2=D4D6FF&amp;slidercolor1=#3F3D6A&amp;slidercolor2=242153&amp;sliderovercolor=15123C&amp;buttoncolor=242153&amp;buttonovercolor=15123C&amp;textcolor=242153"/>
                </object>
                <?php
                break;


            // Document
            default:
                ?>
                <div class="attachment">
                    <a href="<?php echo $attachment['url']; ?>"><img src="<?php echo Mimetype::getIcon($attachment['ext']); ?>" alt="" class="icon" /> <?php echo htmlspecialchars($attachment['name']); ?></a>
                </div>
            <?php
        }
    }


// Si on affiche uniquement ce post, on prépare l'affichage des photos en grand
    if (isset($one_post) && $post['attachments_nb_photos'] != 0) {
        ?>
        <div id="attachment-photo" class="hidden">
            <a href="javascript:;" id="attachment-photo-prev"><?php echo __('POST_PHOTO_PREV'); ?></a>
            <a href="javascript:;" id="attachment-photo-next"><?php echo __('POST_PHOTO_NEXT'); ?></a>
            <a href="javascript:;" id="attachment-photo-album"><?php echo __('POST_PHOTOS_ALBUM'); ?></a>
        </div>
        <?php
    }
    ?>

    <div class="post-info">
        <?php echo Date::easy((int) $post['time']); ?>
        <?php
        if (isset($post['group_id']) && $post['official'] != '1') {
            ?>
            &#183; <a href="<?php echo Config::URL_ROOT . Routes::getPage('group', array('group' => $post['group_url'])); ?>"><?php echo htmlspecialchars($post['group_name']); ?></a>
            <?php
        }
        if ($is_student) {
            ?>
            &#183; <a href="javascript:;" onclick="Comment.write(<?php echo $post['id']; ?>);"><?php echo __('POST_COMMENT_LINK'); ?></a>
            <!-- Like Links -->
            <?php
            $has_liked = (empty($post['likes']['users'])) ? false : in_array(User_Model::$auth_data['id'], $post['likes']['users'], true);
            if (!$has_liked) {
                ?>
                &#183; <a href="javascript:;" onclick="Like.initPostLike(<?php echo $post['id'] ?>)" id="post-like-link-<?php echo $post['id'] ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                <a href="javascript:;" onclick="Like.initPostUnlike(<?php echo $post['id'] ?>)" class="hidden" id="post-unlike-link-<?php echo $post['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
            <?php } else { ?>
                &#183; <a href="javascript:;" onclick="Like.initPostUnlike(<?php echo $post['id'] ?>)" id="post-unlike-link-<?php echo $post['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                <a href="javascript:;" onclick="Like.initPostLike(<?php echo $post['id'] ?>)" class="hidden" id="post-like-link-<?php echo $post['id'] ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                <?php
            }
        }
        if ($post['private'] == '1') {
            ?>
            <br /><?php echo __('POST_PRIVATE'); ?>
            <?php
        }
        ?>
    </div>

    <?php if (isset($post['likes'])) { ?>
        <div id="post-like-<?php echo $post['id'] ?>" class="post-comment post-like" style="min-height: 16px;">
            <?php
            $name = array();
            // On Range des utilisateur pour pouvoir mieux les afficher.
            foreach ($post['likes']['data'] as $like) {
                $like_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $like['username']));
                if ($like['username'] != User_Model::$auth_data['username'])
                    $name[] = '<a href="' . $like_user_url . '" class="post-comment-username">' . htmlspecialchars($like['firstname'] . ' ' . $like['lastname']) . '</a>';
            }
            // On compte combient ils sont
            $last = count($name) - 1;
            // On fait de belle phrase !
            if ($last > 0) {
                $name[$last - 1] = $name[$last - 1] . ' ' . __('POST_LIKE_LASTSEP') . ' ' . $name[$last];
                unset($name[$last]);
            }
            if (!$has_liked) {
                if ($last == 0) {
                    ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class="hidden"><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_LASTSEP') ?></span>
                    <?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_SING_1'); ?>
                <?php } else if ($last < Config::LIKE_DISPLAYED) { ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class="hidden"><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_SEPARATOR') ?></span>
                    <?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_PLURAL_1'); ?>
                <?php } else { ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class="hidden"><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_SEPARATOR') ?></span>
                    <span id="like-show-short-<?php echo $post['id'] ?>"><?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', array_slice($name, 0, Config::LIKE_DISPLAYED - 1)) . ' ' . __('POST_LIKE_LASTSEP') ?>
                        <a href="javascript:;"  onclick="Like.showAll(<?php echo $post['id']; ?>)"><?php echo ((($last - 1) == 1) ? __('POST_LIKE_OTHER_SING') : ($last - 1) . ' ' . __('POST_LIKE_OTHER_PLURAL')); ?></a> <?php echo __('POST_LIKE_END_PLURAL_1') ?></span>
                    <?php
                    echo '<span class="hidden" id="like-show-all-' . $post['id'] . '">' . implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_PLURAL_1') . '</span>';
                }
            } else {
                if ($last == -1) {
                    ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class=""><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_END_SING_2') ?></span>
                <?php } else if ($last == 0) { ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class=""><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_LASTSEP') ?></span>
                    <?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_PLURAL_2');
                } else if ($last < Config::LIKE_DISPLAYED) { ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class=""><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_SEPARATOR') ?></span>
                    <?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_PLURAL_2');
                } else { ?>
                    <span id="new-like-container-<?php echo $post['id'] ?>" class=""><?php echo __('POST_LIKE_USER') ?><?php echo __('POST_LIKE_SEPARATOR') ?></span>
                    <span id="like-show-short-<?php echo $post['id'] ?>"><?php echo implode(__('POST_LIKE_SEPARATOR') . ' ', array_slice($name, 0, Config::LIKE_DISPLAYED - 1)) . ' ' . __('POST_LIKE_LASTSEP') ?>
                        <a href="javascript:;"  onclick="Like.showAll(<?php echo $post['id']; ?>)"><?php echo ((($last) == 1) ? __('POST_LIKE_OTHER_SING') : ($last) . ' ' . __('POST_LIKE_OTHER_PLURAL')); ?></a>
                        <?php echo __('POST_LIKE_END_PLURAL_2') ?></span>
                    <?php
                    echo '<span class="hidden" id="like-show-all-' . $post['id'] . '">' . implode(__('POST_LIKE_SEPARATOR') . ' ', $name) . ' ' . __('POST_LIKE_END_PLURAL_2') . '</span>';
                }
            }
            ?>
        </div>
    <?php } else { ?>
        <div id="post-like-<?php echo $post['id'] ?>"class="post-comment post-like hidden" style="min-height: 16px;">
            <span id="new-like-container-<?php echo $post['id']; ?>"><?php echo __('POST_LIKE_USER') ?> <?php echo __('POST_LIKE_END_SING_2') ?></span>
        </div>
    <?php } ?>

    <!--  COMMENTS  -->
    <div class="post-comments">
        <?php
        if (!isset($post['comments']) || !$is_logged)
            $post['comments'] = array();
        $nb_comments = count($post['comments']);
        $n = 0;
        $comment_hidden = false;
        $comments_at_the_beginning = floor(Config::COMMENTS_PER_POST / 2);
        $comments_at_the_end = Config::COMMENTS_PER_POST - $comments_at_the_beginning;
        foreach ($post['comments'] as $comment) {
            $n++;
            if ($nb_comments > Config::COMMENTS_PER_POST && !isset($one_post)) {
                if ($n == $comments_at_the_beginning + 1) {
                    $comment_hidden = true;
                    ?>
                    <div id="post-<?php echo $post['id']; ?>-comment-show-all" class="post-comment">
                        <a href="javascript:;" onclick="Comment.showAll(<?php echo $post['id']; ?>)"><?php echo __('POST_COMMENT_SHOW_ALL', array('nb' => $nb_comments)); ?></a>
                    </div>
                    <?php
                } else if ($n == $nb_comments - $comments_at_the_end + 1) {
                    $comment_hidden = false;
                }
            }
            ?>
            <div id="post-comment-<?php echo $comment['id']; ?>" class="post-comment<?php
        if ($comment_hidden)
            echo ' hidden';
        if (isset($one_post))
            echo ' post-comment-attachment' . (isset($comment['attachment_id']) ? $comment['attachment_id'] . ' hidden' : '0');
            ?>">
                     <?php
                     $comment_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $comment['username']));
                     ?>
                <a href="<?php echo $comment_user_url; ?>" class="avatar"><img src="<?php echo $comment['avatar_url']; ?>" alt="" /></a>
                <?php
// Post delete button
                if (($is_logged && $username == $comment['username'])
                        || $is_admin
                        || (isset($post['group_id']) && isset($groups_auth)
                        && isset($groups_auth[(int) $post['group_id']])) && $groups_auth[(int) $post['group_id']]['admin']) {
                    ?>
                    <a href="<?php echo Config::URL_ROOT . Routes::getPage('post_comment_delete', array('id' => $comment['id'])); ?>" class="post-comment-delete">x</a>
                    <?php
                }
                ?>
                <div class="post-comment-message">
                    <a href="<?php echo $comment_user_url; ?>" class="post-comment-username"><?php echo htmlspecialchars($comment['firstname'] . ' ' . $comment['lastname']); ?></a>
                    <?php echo Text::inHTML($comment['message']); ?>
                    <div class="post-comment-info">
                        <?php echo Date::easy((int) $comment['time']); ?>
                        <?php
                        /* In Comment Likes */

                        $has_liked = (empty($comment['user_liked'])) ? false : in_array(User_Model::$auth_data['id'], $comment['user_liked'], true);
                        if (!$has_liked) {
                            ?>
                            &#183; <a href="javascript:;" onclick="Like.initPostComLike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" id="post-com-like-link-<?php echo $comment['id'] ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                            <a href="javascript:;" onclick="Like.initPostComUnlike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" class="hidden" id="post-com-unlike-link-<?php echo $comment['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                        <?php } else { ?>
                            &#183; <a href="javascript:;" onclick="Like.initPostComUnlike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" id="post-com-unlike-link-<?php echo $comment['id'] ?>"><?php echo __('POST_UNLIKE_LINK'); ?></a>
                            <a href="javascript:;" onclick="Like.initPostComLike(<?php echo $post['id'] ?>, <?php echo $comment['id'] ?>)" class="hidden" id="post-com-like-link-<?php echo $comment['id'] ?>"><?php echo __('POST_LIKE_LINK'); ?></a>
                        <?php } ?>
                        &#183; 
                        <?php
                        $nb = count($comment['user_liked']);
                        if ($nb == 0)
                            echo '<span id="post-com-like-new-'.$comment['id'].'" class="hidden">'.__('POST_LIKE_USER').' '.__('POST_LIKE_END_SING_2').'</span>';
                        else if ($nb == 1) {
                            $like_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $comment['like'][0]['username']));
                            if($comment['like'][0]['username'] ==  User_Model::$auth_data['username'])
                                $name = '<span id="post-com-like-new-'.$comment['id'].'">'.__('POST_LIKE_USER').' '.__('POST_LIKE_END_SING_2').'</span>';
                            else
                                $name = '<span id="post-com-like-new-'.$comment['id'].'" class="is-stranger"><a href="' . $like_user_url . '" class="post-comment-username">' . htmlspecialchars($comment['like'][0]['firstname'] . ' ' . $comment['like'][0]['lastname']) . '</a>'. ' ' . __('POST_LIKE_END_SING_1').'</span>';
                                $name .= '<span id="post-com-unlike-new-'.$comment['id'].'" class="hidden">2 '.__('POST_LIKE_END_PLURAL_1').'</span>';
                            echo $name;
                            unset($name);
                        } else {
                            echo '<a href="javascript:;" id="post-com-like-new-'.$comment['id'].'" class="has-value" onclick="Like.showAllCom('.$comment['id'].')"><span id="post-com-like-val-'.$comment['id'].'">'.$nb.'</span> '.__('POST_LIKE_OTHER_PLURAL').' '.__('POST_LIKE_END_PLURAL_1').'</a>';
                            $name = array();
                            foreach ($comment['like'] as $comment_like) {
                                if($comment['like'][0]['username'] ==  User_Model::$auth_data['username'])
                                        $name[] = __('POST_LIKE_USER').' '.__('POST_LIKE_END_SING_2');
                                else{
                                        $like_user_url = Config::URL_ROOT . Routes::getPage('student', array('username' => $comment_like['username']));
                                        $name[] = '<a href="' . $like_user_url . '" class="post-comment-username">' . htmlspecialchars($comment_like['firstname'] . ' ' . $comment_like['lastname']) . '</a>';
                                }
                            }
                            unset($like_user_url);
                            unset($comment_like);
                            ?>
                        <div id="post-com-like-all-<?php echo $comment['id']; ?>" class="hidden-like-box hidden"><?php echo implode('<br />', $name); ?></div>
                    <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            if ($is_student) {
                ?>
                <form action="<?php echo Config::URL_ROOT . Routes::getPage('post_comment', array('id' => $post['id'])); ?>" method="post" class="post-comment-write">
                    <span class="avatar hidden"><img src="<?php echo $avatar_url; ?>" alt="<?php echo $firstname . ' ' . $lastname; ?>" /></span>
                    <div class="post-comment-write-message hidden">
                        <textarea name="comment" rows="1" cols="50"></textarea>
                        <input type="submit" value="<?php echo __('POST_COMMENT_SUBMIT'); ?>" />
                    </div>
                    <div class="post-comment-write-placeholder" onmouseup="Comment.write(<?php echo $post['id']; ?>);"><?php echo __('POST_COMMENT_PLACEHOLDER'); ?></div>
                </form>
                <?php
            }
            ?>
    </div>
</div>

</div>