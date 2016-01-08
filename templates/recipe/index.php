<?$this->setTitle($recipe['title']);?>
<link rel="stylesheet" href="/tpl/fancybox/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="/tpl/fancybox/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript">
    $(document).ready(function(){
       $('.fancybox').fancybox();
        $.each($('.s-recipe-step-link'), function(i,item){
           $(this).attr('href', $(this).find('img').attr('src').replace(/http:\/\/img.*?.rl0.ru\/eda\/c172x172\//, 'http://')).fancybox();
        });
    });
</script>

<div class="row">
    <div class="col-md-9">
        <div class="recipe view">
            <div class="desc">
                <div class="btn-group pull-right">

                    <a href="#" data-count="<?=$recipe['likes'];?>" onclick="<?=($user_like  == 0)?'like_recipe':'unlike_recipe';?>(<?=$recipe['id'];?>,this); return false;" class="btn <?=($user_like  == 0)?'btn-default':'btn-success';?> btn-sm btn-like"><span class="glyphicon glyphicon-heart"></span> <span class="c"><?=number_format($recipe['likes']);?></span></a>
                    <a href="#comments" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-comment"></span> <?=number_format($recipe['comments']);?></a>
                </div>
                <?=$recipe['title'];?>
                <div class="small_title"><a href="/index/?search[category_id]=<?=$recipe['category_id'];?>"><?=$recipe['category_name'];?></a> <?if(!empty($recipe['country_name'])):?> &middot; <a href="/index/?search[country_id]=<?=$recipe['country_id'];?>"><?=$recipe['country_name'];?></a><?endif;?></div>
                <div class="clearfix"></div>

                <div class="ingredients">
                    <div class="list">
                        <table class="table table-striped">
                            <?foreach($recipe['ingredients'] as $in):?>
                                <tr>
                                    <td><?=ucfirst($in['name']);?></td>
                                    <td><?=$in['append'];?></td>
                                </tr>
                            <?endforeach;?>
                        </table>
                    </div>
                </div>
                <?if(!empty($recipe['prep_time']) || !empty($recipe['cook_time']) || !empty($recipe['total_time'])):?>
                    <table class="time" width="100%">
                        <tr>
                            <?if(!empty($recipe['prep_time'])):?>
                                <td>
                                    <div class="name">PREP</div>
                                    <?=VF::app()->youtubeTime($recipe['prep_time']);?>
                                </td>
                            <?endif;?>
                            <?if(!empty($recipe['cook_time'])):?>
                                <td>
                                    <div class="name">COOK</div>
                                    <?=VF::app()->youtubeTime($recipe['cook_time']);?>
                                </td>
                            <?endif;?>
                            <?if(!empty($recipe['total_time'])):?>
                                <td class="last">
                                    <div class="name">READY IN</div>
                                    <?=VF::app()->youtubeTime($recipe['total_time']);?>
                                </td>
                            <?endif;?>
                        </tr>

                    </table>
                <?endif;?>

                <div class="instructions">
                    <b><?=__('Инструкция');?></b><br>
                    <div class="t">
                        <?=$recipe['instructions'];?>
                    </div>

                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>

                <?if(!empty($recipe['nutrition'])):?>
                    <div class="nutrition">
                        <b>Nutrition</b><br>
                        <table class="table">
                            <?foreach($recipe['nutrition'] as $item):?>
                                <tr>
                                    <td width="50%"><?=$item['name'];?></td>
                                    <td width="13%"><?=$item['value'];?></td>
                                    <td><div class="progress">
                                            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="<?=$item['percent'];?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$item['percent'];?>%">
                                            </div>
                                        </div>
                                        <span class="percent"><?=$item['percent'];?>% </span>
                                    </td>
                                </tr>
                            <?endforeach;?>
                        </table>
                        <div class="small">* Percent Daily Values are based on a 2,000 calorie diet.</div>
                    </div>


                <?endif;?>

                <div class="share">
                    <b><?=__('Понравился рецепт?');?></b>
                    <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
                    <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="facebook,twitter,gplus,vkontakte,odnoklassniki" data-yashareTheme="counter"></div>
                </div>

                <?if(!empty($recipe['image_url']) && count($recipe['photos']) == 0):?>
                    <?$recipe['photos'][] = array('url' => $recipe['image_url']);?>
                <?endif;?>
                <?if(count($recipe['photos']) > 0):?>
                    <div class="photos">
                        <?foreach($recipe['photos'] as $i=>$item):?>
                            <a class="fancybox <?=($i > 8)?'hide':'';?>" rel="gallery1" href="<?=$item['url'];?>" title="<?=$i+1;?>/<?=count($recipe['photos']);?>">
                                <img src="<?=(!empty($item['url_preview']))?$item['url_preview']:$item['url'];?>" class="img-thumbnail" alt="" />
                            </a>
                        <?endforeach;?>
                    </div>
                <?endif;?>

                <div class="clearfix"></div>


                <?if(!empty($recipe['url'])):?>
                    <div class="source">
                        <?$host = parse_url($recipe['url']);?>
                        <noindex>
                            <?=__('Источник');?>: <a href="http://<?=$host['host'];?>" rel="nofollow" target="_blank"><?=$host['host'];?></a>
                        </noindex>
                    </div>
                <?endif;?>
            </div>
            <div class="clearfix"></div>

            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>

        <div class="recipe view">
            <a name="comments"></a>
            <div class="comments">
                <?if(VF::app()->lang_id == 1):?>

                    <!-- Put this script tag to the <head> of your page -->
                    <script type="text/javascript" src="//vk.com/js/api/openapi.js?115"></script>

                    <script type="text/javascript">
                        VK.init({apiId: 4522689, onlyWidgets: true});
                        var current_post_id = <?=$recipe['id'];?>
                    </script>

                    <!-- Put this div tag to the place, where the Comments block will be -->
                    <div id="vk_comments"></div>
                    <script type="text/javascript">
                        VK.Widgets.Comments("vk_comments", {limit: 10, width: "660", attach: "*", onChange: addCommentCallback}, "post_<?=$recipe['id'];?>");
                    </script>

                <?endif;?>

                <?if(VF::app()->lang_id == 2):?>
                    <div id="fb-root"></div>
                    <script>
                        window.fbAsyncInit = function() {
                            FB.init({
                                appId      : '319157091579613',
                                xfbml      : true,
                                version    : 'v2.0'
                            });

                            FB.Event.subscribe('comment.create', function(response) {
                                $.post('/recipe/updateComments/', {post_id: <?=$recipe['id'];?>});
                            });

                            FB.Event.subscribe('comment.remove', function(response) {
                                $.post('/recipe/updateComments/', {post_id: <?=$recipe['id'];?>});
                            });
                        };

                        (function(d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) return;
                            js = d.createElement(s); js.id = id;
                            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=319157091579613&version=v2.0";
                            fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));



                    </script>

                    <div class="fb-comments" data-href="http://resepte.net/recipe/<?=$recipe['id'];?>/" data-width="100%" data-numposts="5" data-colorscheme="light"></div>

                <?endif;?>
            </div>
        </div>
    </div>
    <div class="col-md-3 related">
        <?if(!empty($related)):?>
            <?foreach($related as $item):?>
            <div class="recipe">
                <div class="image">
                    <a href="/recipe/<?=$item['id'];?>/">
                        <img src="<?=$item['image_url'];?>" >
                    </a>
                </div>
                <div class="desc">
                    <a href="/recipe/<?=$item['id'];?>/">
                        <?=$item['title'];?>
                    </a>

                    <br>
                    <div class="btn-group">

                        <a href="/recipe/<?=$recipe['id'];?>/" class="btn btn-default btn-sm btn-like"><span class="glyphicon glyphicon-heart"></span> <?=number_format($item['likes']);?></a>
                        <a class="btn btn-default btn-sm" href="/recipe/<?=$recipe['id'];?>/"><span class="glyphicon glyphicon-comment"></span> <?=number_format($item['comments']);?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <?endforeach;?>
        <?endif;?>
    </div>
</div>

