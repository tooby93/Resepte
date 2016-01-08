<h3><?=__('Список покупок');?></h3>
<div class="recipe shopping_list">
    <a href="#" class="btn btn-success btn-sm" onclick="$('.add-modal').modal(); return false;"><?=__('Добавить');?></a>
    <a href="#" class="btn btn-danger btn-sm remove_checked"><?=__('Удалить выбранные');?></a>
    <?if(!empty($list)):?>
        <table class="table table-striped table-shopping">
            <?foreach($list as $item):?>
            <tr>
                <td width="10">
                    <input type="checkbox" name="" <?=($item['done'] == 1)?'checked':'';?> data-id="<?=$item['id'];?>">
                </td>
                <td class="name <?=($item['done'] == 1)?'through':'';?>">
                    <?=$item['name'];?>
                </td>

            </tr>

            <?endforeach;?>
        </table>
    <?else:?>
        <div class="alert alert-warning">
            <?=__('Список пуст');?>
        </div>
    <?endif;?>
</div>

<div class="modal fade add-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"><?=__('Добавить');?></h4>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control"><br><br>
                <input type="button" class="btn btn-success" value="<?=__('Добавить');?>" onclick="addItem(); return false;">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('td input[type="checkbox"]').click(function(){
            updRemoveChecked();
            if($(this).prop('checked')){
                $(this).parent().parent().find('.name').addClass('through');
                $.get('/shoppingList/UpdShoppingList/?id='+$(this).attr('data-id')+'&done=1');
            }else{
                $(this).parent().parent().find('.name').removeClass('through');
                $.get('/shoppingList/UpdShoppingList/?id='+$(this).attr('data-id')+'&done=0');
            }
        });

        $('.remove_checked').click(function(){
            var ids = '';
            $.each($('td input[type="checkbox"]'), function(i, item){
                if($(item).prop('checked')){
                    ids = $(item).attr('data-id')+',';
                    $(item).parent().parent().fadeOut(300);
                }
            });

            ids = ids.substring(0, ids.length-1);
            if(ids != ''){
                $.get('/shoppingList/DelShoppingList/?ids='+ids);
            }

           updRemoveChecked();

            return false;
        });

        updRemoveChecked();
    });

    function updRemoveChecked(){
        var c = 0;
        $.each($('td input[type="checkbox"]'), function(i, item){
            if($(item).prop('checked')) c++;
        });

        if(c > 0){
            $('.remove_checked').show();

        }else{
            $('.remove_checked').hide();
        }
    }

    function addItem(){
        var name = $('.add-modal input[type="text"]').val();
        if(name != ''){
            $.post('/shoppingList/AddShoppingList/', {name: name}, function(){
               location.href=location.href;
            });
        }
    }
</script>

