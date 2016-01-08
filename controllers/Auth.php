<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 9/21/14
 * Time: 2:07 PM
 */

class AuthController extends VController{
    private $vk_id = 4559371;
    private $vk_secret = 'GReNOL4tJhkO1zrgykvp';
    private $vk_redirect_url = 'http://resepte.net/auth/vk/?a=1&m=';

    public function actionVK(){
        if(isset($_GET['m']) && $_GET['m'] == 1){
            $this->vk_redirect_url .= 1;
        }else{
            $this->vk_redirect_url .= 0;
        }

        if(isset($_GET['a']) && !empty($_REQUEST['code'])){
            $code = $_REQUEST['code'];
            $token = json_decode(file_get_contents("https://oauth.vk.com/access_token?client_id=".$this->vk_id."&client_secret=".$this->vk_secret."&code=$code&redirect_uri=".urlencode($this->vk_redirect_url)));
            $user_id = $token->user_id;
            $token = $token->access_token;

            $id = VF::app()->database->sql("SELECT id FROM users WHERE social_network = 1 and social_user_id = '$user_id'")->queryScalar();

            if($id == 0){
                $user_info = json_decode(file_get_contents("https://api.vk.com/method/getProfiles?uids=$user_id&fields=first_name,last_name,sex,photo_medium,photo_big&access_token=$token"));
                $user_info = $user_info->response[0];
                VF::app()->database->insert('users', array(
                    'first_name' => $user_info->first_name,
                    'last_name' => $user_info->last_name,
                    'sex' => $user_info->sex,
                    'avatar' => $user_info->photo_medium,
                    'avatar_big' => $user_info->photo_big,
                    'social_network' => 1,
                    'social_user_id' => $user_id
                ));

                $id = VF::app()->database->getLastId();
            }

            $this->setSession($id);

        }else{
            $url = "https://oauth.vk.com/authorize?client_id=".$this->vk_id."&redirect_uri=".urlencode($this->vk_redirect_url)."&response_type=code";
            $this->redirect($url);
        }
    }



    public function actionFacebook(){
        require __ROOT__.'classes/user/facebook.php';  // Include facebook SDK file
        $facebook = new Facebook(array(
            'appId'  => '319157091579613',   // Facebook App ID
            'secret' => '7647ed626e8a8e928bbcf64629f50f97',  // Facebook App Secret
            'cookie' => true,
        ));
        $user = $facebook->getUser();
        if($user){
            $id = VF::app()->database->sql("SELECT id FROM users WHERE social_network = 2 and social_user_id = '$user'")->queryScalar();
            if($id == 0){
                $user_profile = $facebook->api('/me?fields=id,picture.width(100).height(100),first_name,last_name,gender');
                $sex = ($user_profile['gender'] == 'male')?2:1;
                VF::app()->database->insert('users', array(
                    'first_name' => $user_profile['first_name'],
                    'last_name' => $user_profile['last_name'],
                    'sex' => $sex,
                    'avatar' => $user_profile['picture']['data']['url'],
                    'social_network' => 2,
                    'social_user_id' => $user
                ));

                $id = VF::app()->database->getLastId();
            }
            $this->setSession($id);
        }else{
            $loginUrl = $facebook->getLoginUrl(array(
                'scope'		=> 'public_profile', // Permissions to request from the user
            ));

            $this->redirect($loginUrl);
        }
    }



    public function setSession($id){
        $_SESSION['user_id'] = $id;
        echo 'Success';
        if(isset($_GET['m']) && $_GET['m'] == 1){
            echo '<script type="text/javascript">window.onload = function(){android.set_settings('.$id.');}</script>';
        }else{
            echo '<script type="text/javascript">window.opener.auth_callback('.$id.'); window.close();</script>';
        }
    }

    public function actionLogOut(){
        unset($_SESSION['user_id']);
    }


} 