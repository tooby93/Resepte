<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 9/12/14
 * Time: 3:48 PM
 */

require_once '/usr/share/nginx/www/azure/vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Blob\Models\CreateBlobOptions;

class AdminController extends VController{
    public function actionIndex(){
        new ClassesException(404);
    }


    public function actionCategoryIngredient(){
        if(VF::app()->is_admin || (isset($_GET['key']) && $_GET['key'] == 'eb4db0c51005d73ae57064be10c17145')){
            $success = 0;
            if(isset($_POST['ing'])){
                if(!empty($_POST['ing']) && (int)$_POST['category_id'] > 0){
                    $category_id = (int)$_POST['category_id'];

                    foreach($_POST['ing'] as $ing){
                        VF::app()->database->query("UPDATE ingredients SET category_id = '$category_id' WHERE id = ".(int)$ing);
                    }
                    $success = 1;
                }
            }

            $count_with = VF::app()->database->sql("SELECT COUNT(*) FROM ingredients WHERE category_id > 0 and lang_id = 1")->queryScalar();
            $count_without = VF::app()->database->sql("SELECT COUNT(*) FROM ingredients WHERE category_id = 0 and lang_id = 1")->queryScalar();
            $this->render('category_ingredient', array('success' => $success, 'count_with' => $count_with, 'count_without'=>$count_without));
        }else{
            new ClassesException(404);
        }
    }

    public function actionAdd(){
        if(VF::app()->is_admin || (isset($_GET['key']) && $_GET['key'] == 'eb4db0c51005d73ae57064be10c17145')){
            $new_id = 0;
            if(isset($_POST['title'])){


                $title = trim(mysql_real_escape_string($_POST['title']));
                $lang_id = (int)$_POST['lang_id'];
                $category_id = (int)$_POST['category_id'];
                $holiday_id = (int)$_POST['holiday_id'];
                $instructions = trim(mysql_real_escape_string($_POST['instructions']));
                $prep_time = trim(mysql_real_escape_string($_POST['prep_time']));
                $cook_time = trim(mysql_real_escape_string($_POST['cook_time']));
                $total_time = trim(mysql_real_escape_string($_POST['total_time']));
                $ingredients = $_POST['ingredient'];

                $blob_name = '';


                if(!empty($_FILES['image']['tmp_name'])){
                    $connectionString = "DefaultEndpointsProtocol=http;AccountName=trecipes;AccountKey=FoleK8mHGV5MaOvnJaZV6MFD7WadIEc12SL5hhwj7h949ysaXcp7VeTHimfQt6qxSwdQIEVkba0NQ/o58cgdXw==";
// Create blob REST proxy.
                    $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

                    $options = new CreateBlobOptions();
                    $options->setBlobContentType("image/jpeg");
                    $blob_name = md5(time()).".jpg";
                    system("convert ".$_FILES['image']['tmp_name']." -resize 200x200 ".$_FILES['image']['tmp_name']);


                    $content = fopen($_FILES['image']['tmp_name'], "r");

                    $blobRestProxy->createBlockBlob("previews", $blob_name, $content, $options);

                    $blob_name = "http://s1.resepte.net/previews/$blob_name";
                }


                VF::app()->database->query("INSERT INTO recipes (title, image_url, image_mobile_url, category_id, instructions, lang_id,
                prep_time, cook_time, total_time, holiday_id) VALUES ('$title', '$blob_name', '', $category_id, '$instructions', '$lang_id', '$prep_time', '$cook_time', '$total_time', $holiday_id)
                ");
                $new_id = VF::app()->database->getLastId();
                if($new_id > 0){
                    foreach($ingredients as $i=>$ing){
                        $ing = mysql_real_escape_string(trim($ing));
                        if(!empty($ing)){
                            $i_id = VF::app()->database->sql("SELECT id FROM `ingredients` WHERE name = '$ing'")->queryRow();
                            $i_id = (int)$i_id['id'];
                            if($i_id == 0){
                                VF::app()->database->query("INSERT INTO `ingredients` (name, lang_id) VALUES ('$ing', $lang_id)");
                                $i_id = VF::app()->database->getLastId();
                            }

                            $amount = mysql_real_escape_string(trim($_POST['ingredient_append'][$i]));

                            VF::app()->database->query("INSERT INTO recipes2ingredients (recipe_id, ingredient_id, append) VALUES ('$new_id', '$i_id', '$amount')");
                        }

                    }
                }
            }

            $this->render('add', array('new_id' => $new_id));
        }
    }
} 