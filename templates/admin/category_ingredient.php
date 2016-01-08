<div class="recipe">
    <?if($success == 1):?>
        <div class="alert alert-success">
            Success!
        </div>

    <?endif;?>
    Ingredients with category (lang_id = 1): <?=$count_with;?>; without: <?=$count_without;?>
    <form method="post">
        Category Id:<br>
        <input name="category_id" class="form-control"><br>
        Ingredients:<br>
        <input name="search" placeholder="Start write name of ingredient..." class="form-control">
        <a href="#" onclick="$('input[type=checkbox]').attr('checked', 'checked');">Select all</a>
        <hr>
        <div class="list">

        </div>
        <br><br>

        <input type="submit" class="btn btn-default" value="Submit">
    </form>

    <br><br>


    <script type="text/javascript">
        $(document).ready(function(){
            setTimeout(function(){
                $('input[name="search"]').bind('keydown', function(){
                    $('.list').html('');
                    var t = $(this);
                    setTimeout(function(){
                        if($(t).val().length > 1){
                            $.getJSON('/recipe/suggests/?q='+$(t).val()+'&cat_id=0', function(data){
                                $.each(data, function(i,item){
                                    $('.list').append('<input type="checkbox" name="ing[]" value="'+item.id+'"> '+item.name+'<br>');
                                });
                            });
                        }
                    }, 100);


                });
            }, 100);

        });
    </script>
</div>
