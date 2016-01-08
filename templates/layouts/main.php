<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">

    <?$title = $this->getTitle();
    $p_name = VF::app()->config("general")->project_name;
    if(VF::app()->lang_id == 1 && empty($title))$p_name= $p_name.' - Лучшие кулинарные рецепты со всего мира';
    if(VF::app()->lang_id == 2 && empty($title))$p_name= $p_name.' - The best recipes from around the world';
    ?>

    <meta name="description" content="<?=$p_name;?>">

    <title><?=(empty($title))?$p_name:$title.' - '.$p_name;?></title>
    <?if(!empty($meta)):?>
        <meta property="og:title" content="<?=$meta['title'];?>" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="<?=$meta['url'];?>" />
        <meta property="og:image" content="<?=$meta['image'];?>" />
        <meta property="og:description" content="<?=$meta['description'];?>" />
        <meta property="og:type" content="<?=$meta['type'];?>" />

    <?endif;?>

    <?if(!empty($holiday)):?>
        <div class="bg-bottom" style="background: url('<?=$holiday['background_url'];?>');"></div>
    <?endif;?>

    <!-- Bootstrap core CSS -->
    <link href="http://yastatic.net/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="/tpl/css/token-input-mac.mcss" rel="stylesheet">
    <link href="/tpl/css/style.mcss" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="/tpl/js/jquery.min.mjs"></script>
    <script type="text/javascript" src="/tpl/js/bootstrap.min.mjs"></script>
    <script type="text/javascript" src="/tpl/jquery.tokeninput.mjs"></script>
    <script type="text/javascript" src="/tpl/script.mjs"></script>
</head>
    <body class="<?if(!empty($holiday)):?>holiday-<?=$holiday['link'];?><?endif;?>">


    <div class="container ">
        <div class="row header">
            <div class="col-md-3">
                <a href="/">
                    <img src="/tpl/images/logo.png" class="logo">
                </a>
            </div>
            <div class="col-md-8 search-form">
                <form class="form-inline" role="form" action="/index/">

                    <div class="input-group">
                        <input type="text" class="form-control" name="search[query]" placeholder="<?=__('Поиск рецепта');?>" value="<?=$advanced['query'];?>">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="submit"><?=__('Найти');?></button>
                          </span>
                    </div>
                    <!--
                <div class="form-group">
                    <div class="input">
                        <input type="text" class="form-control input-lg " name="search[query]" placeholder="<?=__('Поиск рецепта');?>" value="<?=$advanced['query'];?>">
                        <?if(!empty(VF::app()->postopt->total_found)):?>
                            <div class="count">
                                <?=(!empty(VF::app()->postopt->total_found))?number_format(VF::app()->postopt->total_found):'';?>
                                <?=VF::app()->postopt->rcount_name(VF::app()->postopt->total_found);?>
                            </div>
                        <?endif;?>
                    </div>

                    <input type="submit" class="btn btn-default btn-lg" value="">

                </div>-->
                </form>

            </div>

            <div class="clearfix"></div>
        </div>


<!--
                            <div class="btn-group">

                                <div href="#" class="btn btn-default btn-lg user" onclick="<?=(VF::app()->user->isAuth())?'user':'auth';?>(); return false;">
                                    <?if(VF::app()->user->isAuth()):?>
                                        <?$user_info = VF::app()->user->getUserInfo();?>
                                        <img src="<?=$user_info['avatar'];?>">
                                        <div class="name">
                                            <?=$user_info['first_name'].' '.$user_info['last_name'];?>
                                        </div>
                                    <?else:?>
                                        <div class="name">
                                            <?=__('Войти');?>
                                        </div>
                                    <?endif;?>

                                </div>
                                <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                                    <li><a href="/index/?search[favorites]=1"><?=__('Избранное');?></a></li>
                                    <li><a href="/shoppingList/"><?=__('Список покупок');?></a></li>
                                    <li role="presentation" class="divider"></li>
                                    <li><a href="#" onclick="logOut(); return false;"><?=__('Выйти');?></a></li>
                                </ul>

                            </div>
-->




        <div class="row">

            <div class="col-md-12">
             <?=$render;?>
            </div>
            <div class="col-md-2" style="display: none">
                <?if(VF::app()->controller_id == 'admin'):?>
                    <div class="sidebar-module">
                        <div class="sidebar-module-name">Admin</div>
                        <div class="links">
                            <div>
                                <a href="/admin/CategoryIngredient/" <?=(VF::app()->action_id == 'CategoryIngredient')?'class="active"':'';?>>Ingredient Categories</a>
                            </div>
                        </div>
                    </div>
                <?else:?>
                <div class="sidebar-module">
                    <div class="sidebar-module-name"><?=__('Ингредиенты');?></div>
                    <form action="/index/">
                        <input class="chosen" name="search[ingredient_id]">
                    </form>
                </div>

                <div class="sidebar-module">
                    <div class="sidebar-module-name"><?=__('Категории');?></div>
                    <div class="links">
                        <?foreach(VF::app()->postopt->getCategories() as $item):?>
                            <div>
                                <a onclick="oCategory(<?=$item['id'];?>, this); return false;" class="<?=($advanced['category_id'] == $item['id'])?'active':'';?>" href="<?=VF::app()->postopt->generateLink($advanced, array('category_id' => $item['id']));?>"><?=$item['name'];?></a>
                                <button type="button" class="close" title="<?=__('Убрать фильтр');?>" onclick="oCategory(0, this)"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            </div>

                        <?endforeach;?>
                    </div>
                </div>

                <?if(VF::app()->lang_id == 1):?>
                <div class="sidebar-module countries">
                    <div class="sidebar-module-name">Кухни</div>
                    <div class="links">
                        <?foreach(VF::app()->postopt->getCountries() as $item):?>
                            <div class="<?if($item['sort'] == 0 && ($item['id'] != $advanced['country_id'])):?>h<?endif;?>">
                                <a onclick="oCountry(<?=$item['id'];?>, this); return false;" class="<?=($advanced['country_id'] == $item['id'])?'active':'';?>" href="<?=VF::app()->postopt->generateLink($advanced, array('country_id' => $item['id']));?>" ><?=$item['name'];?></a>
                                <button type="button" class="close" title="<?=__('Убрать фильтр');?>" onclick="oCountry(0, this)"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            </div>

                        <?endforeach;?>
                        <div class="more">
                            <a href="#" onclick="$('.sidebar-module.countries .links .h').fadeIn(100); $(this).hide(); return false;"><?=__('Показать еще');?></a>
                        </div>
                    </div>
                </div>
                <?endif;?>

                    <?$banner = VF::app()->postopt->getBanner();?>

                    <?if(!empty($banner)):?>
                        <a href="/r/<?=$banner['id'];?>" target="_blank">
                            <img src="<?=$banner['url_to_image'];?>" class="sidebar-module" style="padding: 0">
                        </a>

                        <?endif;?>

                <?endif;?>




            </div>


            </div>
    </div>
    <div class="footer">
        <div class="pull-right">
            <a href="/index/?lang_id=1" class="<?=(VF::app()->lang_id == 1)?'active':'';?>">Русский</a> &middot;
            <a href="/index/?lang_id=2" class="<?=(VF::app()->lang_id == 2)?'active':'';?>">English</a>
        </div>
        <?=$p_name;?> &copy; 2014

    </div>


    <?if(!VF::app()->user->isAuth()):?>
        <div class="modal fade auth-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title"><?=__('Войти на сайт');?></h4>
                    </div>
                    <div class="modal-body">
                        <p class="t"><?=__('Выберите социальную сеть для авторизации');?>:</p>
                        <div class="social_networks">
                            <div class="sn vk">
                                <div class="img"><img src="/tpl/images/sn/vk.png"></div>
                                <div class="text"><?=__('Вконтакте');?></div>
                            <div class="clearfix"></div>
                            </div>

                        <div class="sn fb">
                            <div class="img"><img src="/tpl/images/sn/fb.png"></div>
                            <div class="text">Facebook</div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    <?endif;?>

    </body>
<script type="text/javascript">
    var g_user_id = <?=(int)VF::app()->user->getId();?>;
</script>

<!-- Yandex.Metrika counter --><script type="text/javascript">(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter26367501 = new Ya.Metrika({id:26367501, webvisor:true, clickmap:true, trackLinks:true, accurateTrackBounce:true}); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");</script><noscript><div><img src="//mc.yandex.ru/watch/26367501" style="position:absolute; left:-9999px;" alt="" /></div></noscript><!-- /Yandex.Metrika counter -->

</html>