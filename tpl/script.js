var tmp = {auth_func: null};

function auth(callback){
    if(g_user_id == 0){
        tmp.auth_func = null;
        var a = $('.auth-modal');
        $(a).modal();

        $(a).find('.sn.vk').unbind('click').bind('click', function(){
            window.open('/auth/vk/','VK', "width=700, height=500");
        });

        $(a).find('.sn.fb').unbind('click').bind('click', function(){
            window.open('/auth/facebook/','FB', "width=700, height=500");
        });

        if(callback != undefined){
            tmp.auth_func = callback;
        }
    }
}

function auth_callback(user_id){
    g_user_id = user_id;


    if(tmp.auth_func != null){
        tmp.auth_func(user_id);
    }

    $('.auth-modal').modal('hide');
    var a = $('.search-form .user');

    $.getJSON('/api/userInfo/?id='+user_id, function(data){
        $(a).html('<img src="'+data.avatar+'" class="avatar"><div class="name">'+ data.first_name+' '+data.last_name+'</div>').attr('onclick','user(); return false');
    });

}

function sBarLoadOn(c){
    $(c).addClass('load').append('<div class="sm-load"></div>');
}

function sBarLoadOff(c){
    $(c).removeClass('load').find('.sm-load').remove();

}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

$(document).ready(function(){
    $('.chosen').tokenInput("/recipe/suggests/", {onAdd: oIngredient, onDelete: oIngredient});

});

function oIngredient(){
    _search_object.ingredient_id = $('.chosen').val();
    updateSearch();
}


function addCommentCallback(num, last_comment, date, sign){
    $.post('/recipe/updateComments/', {post_id: current_post_id});
}

function like_recipe(recipe_id, t){
    if(g_user_id == 0) auth(function(){ like_recipe(recipe_id, t);});
    else{
        $.post('/recipe/like/', {recipe_id: recipe_id}, function(data){
            if(data != 'OK'){
                alert(data);

            }else{
                if(t != undefined){
                    var c = parseInt($(t).attr('data-count'))+1;
                    $(t).find('.c').html(number_format(c));
                    $(t).attr('data-count', c);
                    $(t).removeClass('btn-default').addClass('btn-success').attr('onclick', 'unlike_recipe('+recipe_id+', this); return false;');
                }
            }
        });
    }
}

function unlike_recipe(recipe_id, t){
    if(g_user_id > 0){
        $.post('/recipe/unlike/', {recipe_id: recipe_id}, function(data){
            if(data != 'OK'){
                alert(data);
            }else{
                if(t != undefined){
                    var c = parseInt($(t).attr('data-count'))-1;
                    $(t).find('.c').html(number_format(c));
                    $(t).attr('data-count', c);
                    $(t).removeClass('btn-success').addClass('btn-default').attr('onclick', 'like_recipe('+recipe_id+', this); return false;');;
                }
            }
        });
    }
}

function oCategory(id, t){
    _o(id, t, 'category_id');
}

function oCountry(id, t){
    _o(id, t, 'country_id');
}

function _o(id, t, name){
    if (typeof _search_object != 'undefined'){
        if(name == 'category_id') _search_object.category_id = id;
        if(name == 'country_id') _search_object.country_id = id;
        updateSearch();
        if(id == 0){
            $(t).parent().parent().find('a').removeClass('active');
            $(t).parent().parent().parent().find('button').hide();
        }else{
            $(t).parent().parent().find('a').removeClass('active');
            $(t).parent().parent().parent().find('button').hide();
            $(t).parent().find('button').show();
            $(t).addClass('active');
        }

    }else{
        location.href= '/index/?search['+name+']='+id;
    }
}

function oPage(p){
    _search_object.page = p;
    updateSearch();
}

function user(){
    $('.search-form .dropdown-menu').toggle();
}

function logOut(){
    $.get('/auth/logout/', function(){
        location.href=location.href;
    });
}

function oQuerySearch(){
    var q = $('.search-form input[type=text]').val();
    if (typeof _search_object != 'undefined'){
        _search_object.query = q;
        updateSearch();
    }else{
        location.href= '/index/?search[query]='+q;
    }
}



function updateSearch(){
    var url = '/index/?';

    $.each(_search_object, function(key,value){
        url += 'search['+key+']='+value+'&';
    });

    url = url.substr(0,url.length-1)+'&ajax=1';

    history.replaceState({}, '', url);
    $("html, body").animate({ scrollTop: "0" });


    $('.ajax-loader').show();
    $('.recipes-list').css('opacity', '0.2');

    $.getJSON(url, function(data){
       $('.recipes-list').html(data.data).css('opacity', '1');
        $('.ajax-loader').hide();
        $('.search-form .count').html(data.count);
        document.title = data.title;



    });
}


function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                .toFixed(prec);
        };

    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}
