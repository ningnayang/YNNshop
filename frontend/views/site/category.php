<?php foreach($categorys as $key1=>$category):?>
    <div class="cat <?=$key1?'':'item1'?>">
        <h3><a href="<?=\yii\helpers\Url::to(['list/index','id'=>$category->id])?>"><?=$category->name?></a> <b></b></h3>
        <div class="cat_detail">
            <?php foreach($category->children as $key2=>$arr):?>
                <dl <?=$key2?'':'class="dl_1st"'?>>
                    <dt><a href="<?=\yii\helpers\Url::to(['list/index','id'=>$arr->id])?>"><?=$arr->name?></a></dt>
                    <dd>
                        <?php foreach($arr->children as $array):?>
                            <a href="<?=\yii\helpers\Url::to(['list/index','id'=>$array->id])?>"><?=$array->name?></a>
                        <?php endforeach;?>
                    </dd>
                </dl>
            <?php endforeach?>
        </div>
    </div>
<?php endforeach;?>
