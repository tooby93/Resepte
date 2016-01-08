<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 8/25/14
 * Time: 9:19 PM
 */

class RecipeController extends VController {
    public function actionIndex($id = 0){
        if($id > 0){
            $recipe = VF::app()->database->sql("SELECT r.id,r.title, r.instructions, r.image_url, r.likes, r.comments, r.url, r.prep_time, r.cook_time, r.total_time, r.lang_id,
            c.name as category_name, cc.name as country_name, r.category_id, r.country_id
            FROM recipes as r
            LEFT JOIN categories as c ON c.id = r.category_id
            LEFT JOIN countries as cc ON cc.id = r.country_id
            WHERE r.id = '$id'")->queryRow();
            VF::app()->lang_id = $recipe['lang_id'];
            if(empty($recipe)){
                new ClassesException(404);
            }

            $recipe['ingredients'] = VF::app()->database->sql("SELECT i.id,name,append FROM ingredients as i INNER JOIN recipes2ingredients as ri ON ri.ingredient_id = i.id and ri.recipe_id = ".$recipe['id'])->queryAll();
            $recipe['nutrition'] = VF::app()->database->sql("SELECT i.id,name,value,percent FROM nutritions as i INNER JOIN recipes2nutritions as ri ON ri.nutrition_id = i.id and ri.recipe_id = ".$recipe['id'])->queryAll();
            $recipe['photos'] = VF::app()->database->sql("SELECT url,url_preview FROM recipes_photos as i WHERE i.recipe_id = ".$recipe['id']."")->queryAll();


            $related = VF::app()->database->sql("SELECT r.id,r.title,r.image_url, r.likes, r.comments FROM recipes as r
            WHERE r.category_id = ".$recipe['category_id']." ORDER BY RAND() LIMIT 5 ")->queryAll();


            $meta_desc = mb_substr(trim(strip_tags($recipe['instructions'])), 0, 300, 'UTF-8')."...";


            $user_like = VF::app()->postopt->checkUserLike($id);
            $arr = array('recipe' => $recipe, 'user_like' => $user_like, 'meta'=> array(
                'title' => $recipe['title'],
                'url' => 'http://recipes.tooby.ru/recipes/'.$recipe['id'].'/',
                'image' => $recipe['image_url'],
                'description' => $meta_desc,
                'type' => 'article'

            ),'related' => $related);


            $this->render('index', $arr);



        }else{
            new ClassesException(404);
        }
    }

    public function actionUpdateComments(){
        if(isset($_POST['post_id']) && (int)$_POST['post_id'] > 0){
            $count = 0;
            $post_id = (int)$_POST['post_id'];
            $comments = json_decode(file_get_contents("http://api.vk.com/method/widgets.getComments?widget_api_id=4522689&page_id=post_".$post_id));
            if(!empty($comments)){
                $count += $comments->response->count;
            }

            $comments = json_decode(file_get_contents("https://graph.facebook.com/comments/?ids=http://resepte.net/recipe/".$post_id."/"),true);

            if(!empty($comments)){
                $count += count($comments['http://resepte.net/recipe/'.$_POST['post_id'].'/']['comments']['data']);
            }



            if($count > 0){
                VF::app()->database->update('recipes', array(
                    'comments' => $count
                ), 'id = ' . $post_id);
            }
        }
    }


    public function actionSuggests(){
        if(isset($_GET['q']) && !empty($_GET['q'])){
            $q = mysql_real_escape_string($_GET["q"]);
            $sql = '';
            if(isset($_GET['cat_id'])) $sql = ' and category_id = 0';
            $lang = "and lang_id = ".VF::app()->lang_id;
            $arr = VF::app()->database->sql("SELECT id,name FROM ingredients WHERE name like '%$q%' $lang $sql LIMIT 50")->queryAll();
            $json_response = json_encode($arr);

            if($_GET["callback"]) {
                $json_response = $_GET["callback"] . "(" . $json_response . ")";
            }

            echo $json_response;
        }
    }

    public function actionLike(){
        if(isset($_POST['recipe_id']) && ((int)$_POST['recipe_id'] > 0) && VF::app()->user->isAuth()){
            $recipe_id = (int)$_POST['recipe_id'];
            $user_id = VF::app()->user->getId();

            $check = VF::app()->database->sql("SELECT COUNT(*) FROM recipes_likes WHERE user_id = '$user_id' and recipe_id = '$recipe_id'")->queryScalar();
            if($check > 0){
                echo __("Вы уже голосовали за этот рецепт.");
            }else{
                VF::app()->database->insert('recipes_likes', array(
                    'user_id' => $user_id,
                    'recipe_id' => $recipe_id
                ), true);

                VF::app()->database->update('recipes', array(
                    'likes' => 'likes+1'
                ), 'id = '.$recipe_id);
                echo 'OK';
            }
        }else{
            echo 'Error';
        }
    }


    public function actionUnLike(){
        if(isset($_POST['recipe_id']) && ((int)$_POST['recipe_id'] > 0) && VF::app()->user->isAuth()){
            $recipe_id = (int)$_POST['recipe_id'];
            $user_id = VF::app()->user->getId();

            $check = VF::app()->database->sql("SELECT id FROM recipes_likes WHERE user_id = '$user_id' and recipe_id = '$recipe_id'")->queryScalar();
            if($check > 0){
                VF::app()->database->delete('recipes_likes', 'id = '.$check);
                VF::app()->database->update('recipes', array(
                    'likes' => 'likes -1'
                ), 'id = '.$recipe_id);
                echo 'OK';
            }else{
                echo 'Error';
            }
        }else{
            echo 'Error';
        }
    }


    public function actionTest(){
        $names = VF::app()->database->sql("SELECT id,name FROM `ingredients` WHERE id > 1000")->queryAll();

        foreach($names as $name){
            $data = simplexml_load_string(file_get_contents("http://export.yandex.ru/inflect.xml?name=".urlencode($name['name'])));
            $name_sk = $data->inflection[4];

            VF::app()->database->query("UPDATE `ingredients` SET name_sk = '$name_sk' WHERE id = ".$name['id']);
        }

    }


} 