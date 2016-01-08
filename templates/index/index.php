<?
if($advanced['favorites'] == 1){
    $this->setTitle("Избранное");
}else{
    $this->setTitle($g_title);

}
?>

<?if(empty($advanced['query'])):?>
<div class="row ">
    <div class="col-md-8">
            <?if($advanced['category_id'] > 0):?>
                <h3><?=VF::app()->postopt->getCategoryName($advanced['category_id']);?> <?=__('Рецепты');?></h3>
            <?else:?>
            <div class="row categories">

            <?$categories = VF::app()->postopt->getCategories();?>
                    <?foreach($categories as $i => $item):?>

                        <div class="col-md-2">
                            <a  href="<?=VF::app()->postopt->generateLink($advanced, array('category_id' => $item['id']));?>">
                            <img src="/tpl/images/categories_web/<?=$item['image_web'];?>">
                                <div>
                                    <?=$item['name'];?>
                                </div>
                            </a>
                        </div>
                        <?=(($i+1) == round(count($categories)/2))?'<div class="clearfix"></div>':'';?>
                    <?endforeach;?>
            </div>
            <?endif;?>
        </div>
    <div class="col-md-3">

    </div>

</div>
<?endif;?>

<div class="row">
    <div class="ajax-loader"><img src="/tpl/images/ajax-loader-big.gif"></div>
    <div class="recipes-list">
        <?if($advanced['favorites'] == 1):?>
            <h3><?=__('Избранное');?></h3>
        <?endif;?>

        <?if(!empty($recipes)):?>
        <?foreach($recipes as $i => $recipe):?>
                <?if($i == 10){break;}?>
            <div class="recipe">
                <div class="image">
                    <a href="/recipe/<?=$recipe['id'];?>/">
                        <?if(empty($recipe['image_url'])):?>
                            <img src="/tpl/images/raster.png" class="img-thumbnail">
                        <?else:?>
                        <img src="<?=$recipe['image_url'];?>" class="img-thumbnail">
                        <?endif;?>
                    </a>
                </div>
                <div class="desc">
                    <a href="/recipe/<?=$recipe['id'];?>/"><?=$recipe['title'];?></a>
                    <div class="ingredients">
                    <?=__('Ингредиенты');?>:
                        <div class="list">
                            <?foreach($recipe['ingredients'] as $in):?>
                                <a href="<?=VF::app()->postopt->generateLink($advanced, array('ingredient_id' => $in['id']), 'w');?>" onclick="$('.chosen').tokenInput('add', {id: <?=$in['id'];?>, name: '<?=$in['name'];?>'}); return false;">
                                    <label class="label label-default"><?=$in['name'];?></label>
                                </a>
                            <?endforeach;?>
                        </div>
                    </div>

                    <div class="btn-group">

                        <a onclick="like_recipe(<?=$recipe['id'];?>,1,this); return false;" data-count="<?=$recipe['likes'];?>" class="btn <?=(VF::app()->postopt->checkUserLike($recipe['id'])  == 0)?'btn-default':'btn-success disabled';?> btn-sm btn-like"><span class="glyphicon glyphicon-heart"></span> <?=number_format($recipe['likes']);?></a>
                        <a class="btn btn-default btn-sm" href="/recipe/<?=$recipe['id'];?>/"><span class="glyphicon glyphicon-comment"></span> <?=number_format($recipe['comments']);?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>


        <?endforeach;?>
            <ul class="pager">
                <?if(count($recipes) > 10):?>
                    <li><a href="<?=VF::app()->postopt->generateLink($advanced, array('page' => $advanced['page']+1));?>"><?=__('Назад');?></a></li>
                <?endif;?>
                <?if($advanced['page'] > 0):?>
                    <li><a href="<?=VF::app()->postopt->generateLink($advanced, array('page' => $advanced['page']-1));?>"><?=__('Вперед');?></a></li>
                <?endif;?>
            </ul>

        <?else:?>
            <div class="alert alert-info">
                <?if($advanced['favorites'] == 1):?>
                    <?=__('Список избранного пуст.');?>
                <?else:?>
                    <?=__('Рецепты не найдены. Попробуйте изменить настройки фильтра.');?>
                <?endif;?>
            </div>
        <?endif;?>
    </div>


</div>

<script type="text/javascript">
    var _search_object = {
        <?foreach($advanced as $key=>$val):?>
        <?=$key;?>:'<?=$val;?>',
        <?endforeach;?>

    };

    <?if(!empty($ing_filters)):?>
    <?foreach($ing_filters as $item):?>
    $(document).ready(function(){
        $('.chosen').tokenInput("clear");
        $('.chosen').tokenInput("add", {id: <?=$item['id'];?>, name: "<?=$item['name'];?>"});
    });
    <?endforeach;?>
    <?endif;?>
</script>