<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 8/5/14
 * Time: 9:05 PM
 */

class PostOpt {
    public $total_found;



    public function comments_name($c){
        if($c == 1) return 'комментарий';
        if($c > 1 && $c < 5) return 'комментария';
        if($c > 4) return 'комментариев';
        return '';
    }

    public function rcount_name($c){
        if(VF::app()->lang_id == 1){
            if($c > 9 && $c < 21) return 'рецептов';
            $c = substr($c, -1, 1);
            if($c == 1) return 'рецепт';
            if($c > 1 && $c < 5) return 'рецепта';
            if($c > 4 || $c == 0) return 'рецептов';
            return '';
        }else if(VF::app()->lang_id == 2){
            if($c == 0) return '';
            if($c == 1) return 'recipe';
            if($c > 1) return 'recipes';

            return '';
        }


    }

    public function date_name($date){
        $t = strtotime($date);
        if(date('d.m.Y') == date('d.m.Y',$t)) return 'сегодня в '.date('H:i');
        if(date('d.m.Y',strtotime('-1 Day')) == date('d.m.Y',$t)) return 'вчера в '.date('H:i');
        $months = array(
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря',
        );
        $y = (date('Y') == date('Y', $t))?'':date(' Y', $t);
        return date('d ',$t).$months[(int)date('m',$t)-1].$y.' в '.date('H:i');
    }


    public function generateLink($advanced, $replace = array(), $flag = ''){
        if($flag == 'w'){
            $a = array_keys($replace);
            $key = $a[0];
            $val = $replace[$key];

            if($advanced[$key] == 0) $aa = array();
            else $aa = explode(",",$advanced[$key]);

            $aa[] = $val;
            $advanced[$key] = implode(",", $aa);
        }
        else if($flag == 'd'){
            $a = array_keys($replace);
            $key = $a[0];
            $val = $replace[$key];
            $aa = explode(",",$advanced[$key]);
            foreach($aa as $k=>$v) if($v == $val) unset($aa[$k]);
            if(empty($aa)) $advanced[$key] = "0";
            else $advanced[$key] = implode(",", $aa);


        }else{
            foreach($replace as $k=>$v) $advanced[$k] = $v;
        }
        $link = '/index/?';
        foreach($advanced as $name=>$val){
            $link .= 'search['.$name.']='.$val.'&';
        }
        $link = substr($link, 0, -1);
        return $link;
    }

    function getCategories(){
        return VF::app()->database->sql("SELECT id, name,image_web FROM categories WHERE lang_id = ".VF::app()->lang_id)->queryAll();
    }


    function getCountries(){
        return VF::app()->database->sql("SELECT id,name,sort FROM countries ORDER BY sort DESC, name")->queryAll();
    }

    function getNotes($recipe_id, $user_id = 0){
        if($user_id == 0) $user_id = VF::app()->user->getId();
        return VF::app()->database->sql("SELECT notes FROM user_notes WHERE user_id = '$user_id' and recipe_id = '$recipe_id'")->queryScalar();
    }

    function checkUserLike($id, $user_id = 0){
        if(VF::app()->user->isAuth() || $user_id > 0){
            if($user_id == 0) $user_id = VF::app()->user->getId();
            //$user_like = VF::app()->cache->get("user-like-".$user_id."-$id");
            if(empty($user_like)){
                $user_like = VF::app()->database->sql("SELECT COUNT(*) FROM recipes_likes WHERE recipe_id = '$id' and user_id = '$user_id'")->queryScalar();
                VF::app()->cache->set("user-like-".$user_id."-$id", $user_like);
            }
            return $user_like;
        }
        return 0;
    }

    function getCategoryName($category_id){
        $name = VF::app()->cache->get("category-name-$category_id");
        if(empty($name)){
            $name = VF::app()->database->sql("SELECT name FROM categories WHERE id = ".$category_id)->queryScalar();
            VF::app()->cache->set("category-name-$category_id", $name);
        }

        return $name;
    }

    function getCountryName($country_id){
        $name = VF::app()->cache->get("country-name-$country_id");
        if(empty($name)){
            $name = VF::app()->database->sql("SELECT name FROM countries WHERE id = ".$country_id)->queryScalar();
            VF::app()->cache->set("country-name-$country_id", $name);
        }

        return $name;
    }

    function getRecipes($advanced){
        include(__ROOT__."sphinx/api.php");

        $cl = new SphinxClient();
        $cl->SetServer( "localhost", 9312 );

        $l = isset($advanced['limit'])?$advanced['limit']:10;

        $limit = (isset($advanced['page']))?(int)$advanced['page']*$l:0;
        $cl->setLimits($limit, ($l+1));


        $lang_id = 0;




        if($advanced['favorites'] == 1 && VF::app()->user->isAuth()){
            $ids_a = array();
            $ids = VF::app()->database->sql("SELECT recipe_id FROM recipes_likes WHERE user_id = ".VF::app()->user->getId())->queryAll();
            foreach($ids as $i) $ids_a[] = $i['recipe_id'];
            if(empty($ids_a)) return array();
            $cl->setFilter('id_attr', $ids_a);
        }else{
            if(isset($advanced['lang_id'])) $lang_id = $advanced['lang_id'];
            else $lang_id = VF::app()->lang_id;
        }

        if($advanced['country_id'] > 0){
            $cl->setFilter('country_id', array($advanced['country_id']));

        }

        if($advanced['category_id'] > 0){
            $cl->setFilter('category_id', array($advanced['category_id']));
            $lang_id = 0;
        }

        if($lang_id > 0){
            $cl->setFilter('lang_id', array($lang_id));
        }

        if(!empty($advanced['ingredient_id'])){
            $ids_a = array();
            $a = explode(",", $advanced['ingredient_id']);
            if(count($a) > 1){
                $sql = "SELECT recipe_id, count(Distinct ingredient_id)
                    FROM recipes2ingredients
                    WHERE ingredient_ID in (".$advanced['ingredient_id'].")
                    GROUP BY recipe_id
                    HAVING count(Distinct ingredient_id) = ".count($a);
            }else{
                $sql = "SELECT recipe_id FROM recipes2ingredients WHERE ingredient_id = ".$advanced['ingredient_id']." LIMIT 300";
            }

            $ids = VF::app()->database->sql($sql)->queryAll();
            foreach($ids as $i) $ids_a[] = $i['recipe_id'];
            $cl->setFilter('id_attr', $ids_a);
        }

        if(!empty($advanced['query'])){
            $results = $cl->Query($advanced['query']); // поисковый запрос

        }else{
            $cl->SetMatchMode( SPH_MATCH_FULLSCAN  ); // ищем хотя бы 1 слово из поисковой фразы
            $cl->setSortMode(SPH_SORT_ATTR_DESC, 'date_created');
            $results = $cl->Query('*'); // поисковый запрос
        }


        $this->total_found = $results['total_found'];

        $arr = array();
        if(!empty($results['matches'])){
            foreach($results['matches'] as $r){
                $r['attrs']['id'] = $r['attrs']['id_attr'];
                $arr[] = $r['attrs'];
            }
        }

        return $arr;
    }

    function getBanner(){
        $banner = VF::app()->database->sql("SELECT url, url_to_image, id FROM banners WHERE lang_id = ".VF::app()->lang_id." and hidden = 0 LIMIT 1")->queryRow();
        if(!empty($banner)){
            VF::app()->database->query("UPDATE banners SET views=views+1 WHERE id = ".$banner['id']);
        }
        return $banner;
    }


    function time($time){
        return str_replace(array('M', 'H'), array(' <span class="small">mins</span> ', ' <span class="small">hours</span> '), $time);
    }

    function img_circle($file_name = false, $param = 20){
        $err = true;
        $image = imagecreatefromjpeg($file_name);

        $width=200;
        $height=200;

        if($image){
            $err = false;
        }
        if(!$err){

            $x=$width ;
            $y=$height;
            $img2 = imagecreatetruecolor($x, $y);
            $bg = imagecolorallocate($img2, 255, 255, 255);
            imagefill($img2, 0, 0, $bg);
            $e = imagecolorallocate($img2, 0, 0, 0);
            $r = $x <= $y ? $x : $y;

            imagefilledellipse($img2, ($x/2), ($y/2), $r, $r, $e);
            imagecolortransparent($img2, $e);
            imagecopymerge($image, $img2, 0, 0, 0, 0, $x, $y, 100);

            imagecolortransparent($image, $bg);

            $W=200;
            $H=200;

            $img3=imagecreatetruecolor($W/2,$H/2);
            imagecolortransparent($img3, $bg);

            imagecopyresampled($img3,$image,0,0,0,0,$W/2,$H/2,$W,$H);

            imagepng($img3, $file_name);
            imagedestroy($img2);
            imagedestroy($img3);
            imagedestroy($image);
        }}
} 