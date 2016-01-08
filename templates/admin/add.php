<div class="recipe">
    <?if($new_id > 0):?>
        <div class="alert alert-success">
            Success! <a href="/recipe/<?=$new_id;?>" target="_blank">Page</a>
        </div>

    <?endif;?>

    <form method="post" enctype="multipart/form-data">
    Title:<br>
    <input type="text" class="form-control" name="title"><br>
        Image:<br>
        <input type="file" class="form-control" name="image"><br>
    Language:<br>
    <select name="lang_id" class="form-control">
        <option value="1">Russian</option>
        <option value="2">English</option>
    </select><br>
    Category:<Br>
    <select name="category_id" class="form-control">
        <option value="0">Not selected</option>
        <?foreach(VF::app()->database->sql("SELECT id,name FROM categories")->queryAll() as $cat):?>
            <option value="<?=$cat['id'];?>"><?=$cat['name'];?></option>
        <?endforeach;?>
    </select><br>
        Holiday:<Br>
        <select name="holiday_id" class="form-control">
            <option value="0">Not selected</option>
            <?foreach(VF::app()->database->sql("SELECT id,name FROM holidays")->queryAll() as $cat):?>
                <option value="<?=$cat['id'];?>"><?=$cat['name'];?></option>
            <?endforeach;?>
        </select><br>
    Instructions:<br>
    <textarea name="instructions" class="form-control" style="height: 200px;"></textarea><br>
    <table width="100%">
        <tr>
            <td style="padding: 5px">Prep time: <br>
            <input type="text" class="form-control" name="prep_time">
            </td>
            <td style="padding: 5px">Cook time: <br>
            <input type="text" class="form-control" name="cook_time">
            </td>
            <td style="padding: 5px">Total time: <br>
            <input type="text" class="form-control" name="total_time">
            </td>
        </tr>
    </table><br>
    Ingredients (by comma):<br>
        <a href="#" class="btn btn-success btn-sm" onclick="$('.ingredients table').append('<tr><td style=\'padding: 5px\'><input  name=\'ingredient[]\' class=\'form-control\'></td><td style=\'padding: 5px\' width=\'30%\'><input  name=\'ingredient_append[]\' class=\'form-control\'></td></tr>'); return false;">Add ingredient</a>
        <div class="ingredients">
            <table width="100%">
                <tr>
                    <td>Name</td>
                    <td>Append</td>
                </tr>
                <tr><td style="padding: 5px"><input  name="ingredient[]" value="" class="form-control"></td><td style="padding: 5px" width="30%"><input  name="ingredient_append[]" value="" class="form-control"></td></tr>
            </table>
        </div>
    <br><br>
    <input type="submit" value="Submit" class="btn btn-default">
    </form>
</div>