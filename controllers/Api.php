<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 9/4/14
 * Time: 8:00 PM
 */
require_once '/usr/share/nginx/www/azure/vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Blob\Models\CreateBlobOptions;

class ApiController extends VController{
    public function actionRecipesList(){
        $array = array();
        if(isset($_GET['version']) && $_GET['version'] == 2){
            $array['recipes'] = $this->getRecipes();
            if(isset($_GET['home']) && $_GET['home'] == 1)
                $array['home'] = $this->getHomeRecipes();
        }else{
            $array = $this->getRecipes();
        }

        echo json_encode($array);

    }

    private function getHomeRecipes(){
        $lang_id = VF::app()->lang_id;
        $arr = array();


        $date = date('Y-m-d');

        $holiday = VF::app()->database->sql("SELECT id,name,background_mobile_url FROM holidays WHERE (start_date <= '$date' and '$date' <= end_date) and lang_id = ".VF::app()->lang_id)->queryRow();

        if(!empty($holiday)){
            if(VF::app()->lang_id == 2) $holiday['name'] = 'Recipes for '.$holiday['name'];
            $arr[] = array(
                'title' => $holiday['name'],
                'image_url' => $holiday['background_mobile_url'],
                'id' => $holiday['id'],
                'type' => 'holiday',
            );
        }

        $recipes = VF::app()->database->sql("SELECT r.id,r.title,r.image_url, 'recipe' as type FROM recipes as r
        INNER JOIN tmp_recipes_top as t ON r.id = t.recipe_id and t.lang_id = $lang_id
        ")->queryAll();


        $arr = $arr+$recipes;


        return $arr;
    }

    private function getRecipes(){
        $limit = isset($_GET['limit'])?(int)$_GET['limit']:10;
        $limit++;
        $limit = (isset($_GET['p']) && (int)$_GET['p'] > 0)?((int)$_GET['p']*$limit).",$limit":$limit;
        $category = isset($_GET['category_id'])?" and category_id = ".(int)$_GET['category_id']:"";
        $holiday = isset($_GET['holiday_id'])?" and holiday_id = ".(int)$_GET['holiday_id']:"";
        $orderby = 'r.likes DESC';
        $join = "";
        $lang = "";
        if(isset($_GET['favorites']) && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0){
            $join = "INNER JOIN recipes_likes as l ON l.user_id = ".(int)$_GET['user_id'].' and l.recipe_id = r.id';
            $orderby = "l.added_time DESC";
        }else{
            $lang = " and r.lang_id = ".VF::app()->lang_id;
        }


        if(isset($_GET['query']) && !empty($_GET['query'])){
            $query = mysql_real_escape_string(trim(strip_tags($_GET['query'])));

            $ids = VF::app()->postopt->getRecipes(array('query' => $query, 'limit' => $limit, 'lang_id' => (int)$_GET['lang_id']));
            $ids_arr = array();
            foreach($ids as $id) $ids_arr[] = $id['id_attr'];


            $lang .= " and id in (".implode(",", $ids_arr).")";
        }

        $recipes = VF::app()->database->sql("SELECT r.id, r.title, r.image_url,r.instructions, r.likes, r.lang_id
        FROM recipes as r
        $join
        WHERE
        1 $lang $category $holiday ORDER BY $orderby LIMIT $limit")->queryAll();

        foreach($recipes as $i=>$item){
            if(isset($_GET['version']) && $_GET['version'] == 2){
                $instructions = '';
                $ing = VF::app()->database->sql("SELECT i.id,name,append FROM ingredients as i INNER JOIN recipes2ingredients as ri ON ri.ingredient_id = i.id and ri.recipe_id = ".$item['id']." LIMIT 5")->queryAll();
                foreach($ing as $ing_item){
                    if($item['lang_id'] == 2)  $instructions .= htmlspecialchars_decode($ing_item['append']." ".$ing_item['name'])."\n";
                    else $instructions .= htmlspecialchars_decode($ing_item['name']." ".$ing_item['append'])."\n";
                }
                $recipes[$i]['instructions'] = trim($instructions);

            }else{
                $recipes[$i]['instructions'] = trim(htmlspecialchars_decode(strip_tags($item['instructions'])));
                $recipes[$i]['instructions'] = str_replace(array("&nbsp;", "\n"), array(' ', ''),$recipes[$i]['instructions']);
            }

            $recipes[$i]['title'] = str_replace(array("&nbsp;", "\n"), array(' ', ''),htmlspecialchars_decode($recipes[$i]['title']));
        }
        return $recipes;
    }

    public function actionRecipeView(){
        if(isset($_GET['id']) && (int)$_GET['id'] > 0){
            $id = (int)$_GET['id'];

            $recipe_view = VF::app()->database->sql("SELECT title,instructions, prep_time, cook_time, total_time FROM recipes WHERE id = '$id'")->queryRow();
            $recipe_view['ingredients'] = VF::app()->database->sql("SELECT i.id,name,append as info FROM ingredients as i INNER JOIN recipes2ingredients as ri ON ri.ingredient_id = i.id and ri.recipe_id = ".$id)->queryAll();
            $recipe_view['photos'] = VF::app()->database->sql("SELECT url,url_preview FROM recipes_photos as i WHERE i.recipe_id = $id")->queryAll();
            $recipe_view['liked'] = (VF::app()->postopt->checkUserLike($id, (int)$_GET['user_id']) > 0)?1:0;
            $recipe_view['notes'] = VF::app()->postopt->getNotes($id, (int)$_GET['user_id']);

            $instructions = explode("<p>", $recipe_view['instructions']);
            unset($instructions[0]);
            $ins_array = array();
            foreach($instructions as $item){
                $b = explode("</b>", $item);
                $ins_array[] = array('pre' => trim(strip_tags($b[0])), 'text' => trim(strip_tags($b[1])));
            }

            $recipe_view['instructions'] = $ins_array;

            echo json_encode($recipe_view);
        }
    }

    public function actionRecipeCategories(){
        $categories = VF::app()->database->sql("SELECT id,name,image FROM categories WHERE lang_id = ".VF::app()->lang_id)->queryAll();
        echo json_encode($categories);
    }

    public function actionUserInfo(){
        $id = (int)$_GET['id'];
        $user_info = VF::app()->database->sql("SELECT first_name, last_name, avatar FROM users WHERE id = $id")->queryRow();
        echo json_encode($user_info);
    }

    public function actionGetShoppingList(){
        if(isset($_GET['user_id']) && (int)$_GET['user_id'] > 0){
            $user_id = (int)$_GET['user_id'];
            $shopping_list = VF::app()->database->sql("SELECT id,name,done FROM user_shopping_list WHERE user_id = '$user_id' ORDER BY done, date_create DESC")->queryAll();
            echo json_encode($shopping_list);
        }
    }

    public function actionUpdShoppingList(){
        if(isset($_GET['user_id']) && (int)$_GET['user_id'] > 0 && isset($_GET['id']) && (int)$_GET['id'] > 0){
            $user_id = (int)$_GET['user_id'];
            $id = (int)$_GET['id'];
            $done = (int)$_GET['done'];

            VF::app()->database->query("UPDATE user_shopping_list SET done = '$done' WHERE id = '$id' and user_id = '$user_id'");
        }
    }


    public function actionDelShoppingList(){
        if(isset($_GET['user_id']) && (int)$_GET['user_id'] > 0 && !empty($_GET['ids'])){
            $user_id = (int)$_GET['user_id'];
            $ids = trim(mysql_real_escape_string(strip_tags($_GET['ids'])));

            VF::app()->database->query("DELETE FROM user_shopping_list WHERE id in ($ids) and user_id = '$user_id'");
        }
    }


    public function actionAddShoppingList(){
        if(isset($_GET['user_id']) && (int)$_GET['user_id'] > 0 && !empty($_POST['name'])){
            $user_id = (int)$_GET['user_id'];
            $name = trim(mysql_real_escape_string(strip_tags($_POST['name'])));

            VF::app()->database->insert('user_shopping_list', array(
                'user_id' => $user_id,
                'name' => $name
            ));
        }
    }

    public function actionUploadPhoto(){
        file_put_contents("/var/log/nginx/test/1.txt", print_r($_REQUEST), FILE_APPEND);
        if(isset($_GET['recipe_id']) && (int)$_GET['recipe_id'] > 0 && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0 && !empty($_FILES['uploaded_file'])){
            $id = (int)$_GET['recipe_id'];
            $user_id = (int)$_GET['user_id'];

            $connectionString = "DefaultEndpointsProtocol=http;AccountName=trecipes;AccountKey=FoleK8mHGV5MaOvnJaZV6MFD7WadIEc12SL5hhwj7h949ysaXcp7VeTHimfQt6qxSwdQIEVkba0NQ/o58cgdXw==";
// Create blob REST proxy.
            $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

            $options = new CreateBlobOptions();
            $options->setBlobContentType("image/jpeg");
            $blob_name = md5(time()).".jpg";
            //system("convert ".$_FILES['uploaded_file']['tmp_name']." -resize 600x600 ".$_FILES['uploaded_file']['tmp_name']);


            $content = fopen($_FILES['uploaded_file']['tmp_name'], "r");

            $blobRestProxy->createBlockBlob("photos", $blob_name, $content, $options);

            VF::app()->database->query("INSERT INTO recipes_photos (recipe_id, user_id, url) VALUES ($id,$user_id,'http://s1.resepte.net/photos/$blob_name')");


        }
    }

    public function actionSetUnLike(){
        if(isset($_GET['recipe_id']) && (int)$_GET['recipe_id'] > 0 && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0){
            $id = (int)$_GET['recipe_id'];
            $user_id = (int)$_GET['user_id'];

            $check = VF::app()->database->sql("SELECT id FROM recipes_likes WHERE user_id = '$user_id' and recipe_id = '$id'")->queryScalar();
            if($check > 0){
                VF::app()->database->delete('recipes_likes', 'id = '.$check);
                VF::app()->database->update('recipes', array(
                    'likes' => 'likes -1'
                ), 'id = '.$id);
                echo 'OK';
            }else{
                echo 'Error';
            }
        }
    }


    public function actionSetLike(){
        if(isset($_GET['recipe_id']) && (int)$_GET['recipe_id'] > 0 && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0){
            $recipe_id = (int)$_GET['recipe_id'];
            $user_id = (int)$_GET['user_id'];

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

    public function actionSetNotes(){
        if(isset($_GET['recipe_id']) && (int)$_GET['recipe_id'] > 0 && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0){
            $recipe_id = (int)$_GET['recipe_id'];
            $user_id = (int)$_GET['user_id'];
            $notes = trim(strip_tags(mysql_real_escape_string($_POST['notes'])));
            var_dump($_REQUEST);

            VF::app()->database->query("INSERT INTO user_notes (user_id, recipe_id, notes) VALUES ('$user_id', '$recipe_id', '$notes') ON DUPLICATE KEY UPDATE notes = '$notes'");
        }else{
            echo 'Error';
        }
    }
}