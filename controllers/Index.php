<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 8/4/14
 * Time: 5:12 PM
 */


class IndexController extends VController{
    public function actionIndex(){



        $advanced = array(
            'category_id' => 0,
            'country_id' => 0,
            'ingredient_id' => 0,
            'page' => 0,
            'query' => ''
        );

        $holiday = VF::app()->database->sql("SELECT name, background_url,link FROM holidays WHERE link = '".mysql_real_escape_string(VF::app()->_route[1])."'")->queryRow();

        if(!empty($_GET['search'])){
            foreach($_GET['search'] as $key => $val){
                if($key == 'category_id' || $key == 'country_id' || $key == 'page' || $key == 'favorites')
                    $advanced[$key] = (int)$val;

                if($key == 'ingredient_id'){
                    $ings = explode(",", $val);
                    $ings_new = array();
                    foreach($ings as $k=>$v) $ings_new[] = (int)$v;
                    $advanced[$key] = implode(",", $ings_new);
                }

                if($key == 'query'){
                    $advanced[$key] = trim(strip_tags(addslashes($val)));
                }
            }
        }

        if(!empty($advanced['ingredient_id'])){
            $ing_filters = VF::app()->database->sql("SELECT id,name,name_sk FROM ingredients WHERE id in (".$advanced['ingredient_id'].")")->queryAll();

        }



        $recipes = VF::app()->postopt->getRecipes($advanced);

        //$recipes = VF::app()->database->sql("SELECT r.id,r.title,r.image_url, r.likes,r.comments FROM recipes as r $join WHERE 1 $add_sql ORDER BY r.likes DESC LIMIT $limit,11")->queryAll();
        foreach($recipes as $i=>$r){
            $recipes[$i]['ingredients'] = VF::app()->database->sql("SELECT i.id,name FROM ingredients as i INNER JOIN recipes2ingredients as ri ON ri.ingredient_id = i.id and ri.recipe_id = ".$r['id'])->queryAll();
        }
        //$ingredients = VF::app()->database->sql("SELECT id,name FROM ingredients ORDER BY name LIMIT 300")->queryAll();

        $g_title = $this->gTitle($advanced, $ing_filters);
        $arr = array('recipes' => $recipes, 'ing_filters' => $ing_filters, 'advanced' => $advanced, 'holiday' => $holiday, 'g_title' => $g_title);

        if(isset($_GET['ajax']) && VF::app()->isAjax()){
            $p_name = VF::app()->config("general")->project_name;
            $g_title = (!empty($g_title))?"$g_title - $p_name":$p_name;
            echo json_encode(array(
                    'data' => $this->renderPartial('index', $arr, true),
                    'count' => number_format(VF::app()->postopt->total_found).' '.VF::app()->postopt->rcount_name(VF::app()->postopt->total_found),
                    'title' => $g_title
                )
            );
        }else{
            $this->render('index', $arr);
        }
    }


    function gTitle($advanced, $ing_filters){
        $title = '';
        if(!empty($ing_filters)){
            $title = 'Рецепты с ';
            foreach($ing_filters as $i){
                $t = (!empty($i['name_sk']))?$i['name_sk']:$i['name'];
                $title .= mb_strtolower($t, 'UTF-8').', ';

            }
            $title = substr($title, 0, -2);
        }

        if($advanced['category_id'] > 0){
            $title = (!empty($title))?$title." - ":$title;
            $title .= VF::app()->postopt->getCategoryName($advanced['category_id']);
        }

        if($advanced['country_id'] > 0){
            $title = (!empty($title))?$title." - ":$title;
            $title .= VF::app()->postopt->getCountryName($advanced['country_id']);
        }

        return $title;
    }


    public function actionR($id){
        if((int)$id > 0){
            $url = VF::app()->database->sql("SELECT url FROM banners WHERE id = '$id'")->queryScalar();
            if(!empty($url)){
                VF::app()->database->query("UPDATE banners SET clicks=clicks+1 WHERE id = ".(int)$id);
                $this->redirect($url);
            }else{
                $this->redirect('/');
            }

        }else{
            $this->redirect('/');
        }
    }

    public function actionAndroid(){
        $r = (isset($_GET['r']))?urldecode($_GET['r']):'/';
        $r .= (preg_match('/\?/is', $r))?'&na=1':'?na=1';
        VF::app()->database->query("UPDATE banners SET views=views+1 WHERE id = 3");
        $this->renderPartial('android', array('r' => $r));
    }
} 