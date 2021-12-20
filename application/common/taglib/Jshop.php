<?php

/**
 * jshop-https://www.jihainet.com
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-08
 * Time: 19:51
 */

namespace app\common\taglib;

use app\common\model\GoodsCat;
use think\template\TagLib;
use app\common\model\Goods;
use app\common\model\Products;
use app\common\model\User;

class Jshop extends TagLib
{

    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'image' => [
            'attr'  => 'id,name,style,width,height,type,value,single',
            'close' => 0
        ],
        'uploadImage' => [
            'attr'  => 'id,name,style,width,height,type,value',
            'close' => 0
        ],
        'input_radio' => [],
        'area' => [
            'attr' => 'name,value,class,style',
            'close' => 0
        ],
        //品牌列表
        'sellerbrands' => [
            'attr' => 'name,value,num',
            'close' => 0
        ],
        //商品列表
        'sellergoods' => [
            'attr' => 'name,value,num,key',
            'close' => 0
        ],
        //货品列表
        'products' => [
            'attr' => 'name,value,num,key',
            'close' => 0
        ],
        //商品分类
        'goodscat' => [
            'attr' => 'id,name,value',
            'close' => 0
        ],
        //公告列表
        'noticelist' => [
            'attr' => 'id,name,value',
            'close' => 0
        ],
        //团购秒杀列表
        'group' => [
            'attr' => 'name,value,num,key',
            'close' => 0
        ],
        //拼团列表
        'pintuan' => [
            'attr' => 'id,name,value',
            'close' => 0
        ],
        //用户列表
        'selectuser'  =>  [
            'attr'  =>  'id,name,value,grade,num,key',
            'close' => 0
        ]
    ];

    /**
     * 图片上传标签
     * @param $tag
     * @return string
     * User: wjima
     * Email:1457529125@qq.com
     * Date: 2018-01-08 20:09
     */
    public function tagImage($tag)
    {
        $id       = !empty($tag['id']) ? $tag['id'] : '_editor';
        $name     = !empty($tag['name']) ? $tag['name'] : '';
        $style    = !empty($tag['style']) ? $tag['style'] : '';
        $value    = !empty($tag['value']) ? $tag['value'] : _sImage(getSetting('shop_default_image')); //todo 默认图片
        $type     = !empty($tag['type']) ? $tag['type'] : '';
        $num      = !empty($tag['num']) ? $tag['num'] : 1;
        $width    = !empty($tag['width']) ? $tag['width'] : '90px';
        $height   = !empty($tag['height']) ? $tag['height'] : '90px';
        $single   = !empty($tag['single']) ? $tag['single'] : 'false'; //是否单图上传

        $str_name = '';
        if ($num > 1) {
            $str_name = $name . '[]';
        } else {
            $str_name = $name;
        }
        if (isset($tag['value']) && !empty($tag['value'])) {
            $value = $this->autoBuildVar($value);
            $parseStr = '
            <button type="button" class="layui-btn" id="upload_img_' . $id . '" onclick="upImag' . $id . 'e()">上传图片</button>
            <div class="layui-upload-list">
                <img class="layui-upload-img"  src=' . "'" . '<?php echo _sImage(' . $value . ')?>' . "'" . ' id="image_src_' . $id . '" style="width:' . $width . ';height:' . $height . ';" >
                <p id="upload_text_' . $id . '"></p>
            </div>
            <input class="layui-upload-file" type="hidden" name="' . $str_name . '"  id="image_value_' . $id . '" value="' . "<?php echo " . $value . "?>" . '">
            <textarea id="edit_' . $id . '" style="display: none;"></textarea>
            <script>
            var maxIndex = getMaxZIndex();
            var _edito' . $id . 'r = UE.getEditor("edit_' . $id . '",{
                initialFrameWidth:800,
                initialFrameHeight:300,
                zIndex:maxIndex+1,
                 single:' . $single . '
            });
            _edito' . $id . 'r.ready(function (){
                //_edito' . $id . 'r.setDisabled();
                _edito' . $id . 'r.hide();
                //侦听图片上传
                _edito' . $id . 'r.addListener(\'beforeInsertImage\',function(t,arg){
                        $("#image_value_' . $id . '").attr("value",arg[0].image_id);
                        $("#image_src_' . $id . '").attr("src",arg[0].src);
                });
            });
            //上传dialog
            function upImag' . $id . 'e(){
                var myImag' . $id . 'e = _edito' . $id . 'r.getDialog("insertimage");
                myImag' . $id . 'e.open();
            }
</script>
            ';
        } else {
            $parseStr = '
            <button type="button" class="layui-btn" id="upload_img_' . $id . '" onclick="upImag' . $id . 'e()">上传图片</button>
            <div class="layui-upload-list">
                <img class="layui-upload-img"  src="' . $value . '" id="image_src_' . $id . '" style="width:' . $width . ';height:' . $height . ';" >
                <p id="upload_text_' . $id . '"></p>
            </div>
            <input class="layui-upload-file" type="hidden" name="' . $str_name . '"  id="image_value_' . $id . '" value="">
            <textarea id="edit_' . $id . '" style="display: none;"></textarea>
            <script>
            var _edito' . $id . 'r = UE.getEditor("edit_' . $id . '",{
                initialFrameWidth:800,
                initialFrameHeight:300,
                zIndex:19891026,
                single:' . $single . '
            });
            _edito' . $id . 'r.ready(function (){
                //_edito' . $id . 'r.setDisabled();
                _edito' . $id . 'r.hide();
                //侦听图片上传
                _edito' . $id . 'r.addListener(\'beforeInsertImage\',function(t,arg){
                        $("#image_value_' . $id . '").attr("value",arg[0].image_id);
                        //将图片地址赋给img的src,实现预览
                        $("#image_src_' . $id . '").attr("src",arg[0].src);
                });
            });
            //上传dialog
            function upImag' . $id . 'e(){
                var myImag' . $id . 'e = _edito' . $id . 'r.getDialog("insertimage");
                myImag' . $id . 'e.open();
            }
</script>
            ';
        }
        return $parseStr;
    }

    /**
     * 地区（省市区）标签
     * @param $tag
     * @return string
     */
    public function tagArea($tag)
    {
        $parse = '<input type="hidden" name="' . $tag['name'] . '"';
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
            $js_val = "id=<?php echo (" . $tag['value'] . ");?>";
            $parse .= ' value="<?php echo (' . $tag['value'] . ');?>"';
        } else {
            $js_val = "id=0";
        }
        $parse .= ' />';

        if (isset($tag['class'])) {
            $class = ' class=\'' . $tag['class'] . '\'';
        } else {
            $class = "";
        }
        if (isset($tag['style'])) {
            $style = ' style=\'' . $tag['style'] . '\'';
        } else {
            $style = "";
        }
        $full     = !empty($tag['full']) ? $tag['full'] : '1';      //传1或者2，如果是1就说明是省市区必须得输入到最后一个节点才有值，否则就是任意点都有值

        if (config('?rename_manage') && config('rename_manage')) {
            $areaUrl = '/Api/common/area';
            $areaChildren = '/Api/common/areaChildren';
        } else {
            $areaUrl = url('/Api/common/area');
            $areaChildren = url('api/common/areaChildren');
        }

        $parse .= '
            <script>
                $(function(){
                    $.ajax({
                        type: "POST",
                        url: "' . $areaUrl . '",
                        data: "' . $js_val . '",
                        success:function(data) {
                            var str = "";
                            $.each(data, function(i,n){
                                str += "<select lay-ignore name=\'' . $tag['name'] . '_"+(i+1)+"\' dep=\'"+(i+1)+"\' ' . $class . $style . '>";
                                str += "<option value=\'\' >请选择</option>";
                                $.each(n.list,function(h,z){
                                    if(n.hasOwnProperty(\'info\') && n.info.id == z.id){
                                        str += "<option value=\'"+z.id+"\' selected=\'selected\'>"+z.name+"</option>";
                                    }else{
                                        str += "<option value=\'"+z.id+"\' >"+z.name+"</option>";
                                    }
                                });
                                str += "</select>";

                            });
                            $("input[name=\'' . $tag['name'] . '\']").after(str);
                            //以上数据输出完，以下绑定事件
                            $.each(data, function(i,n){
                                if(i<(data.length)){
                                    $("select[name=\'' . $tag['name'] . '_"+(i+1)+"\']").change(function(){
                                        change' . $tag['name'] . 'Area(i+1,data.length);
                                    });
                                }
                            });

                        }
                    });
                    function change' . $tag['name'] . 'Area(i,max_i){
                        //清除后面节点
                        for(var x = i+1;x<=6;x++){  //最多6层，足够了
                            $("select[name=\'' . $tag['name'] . '_"+x+"\']").remove();
                        }
                        var val = $("select[name=\'' . $tag['name'] . '_"+i+"\']").val();
                        if(val != ""){
                            //取子节点数据，然后显示下一级
                            $.ajax({
                                type: "POST",
                                url: "' . $areaChildren . '",
                                data: "id="+val,
                                success:function(data) {
                                    if(data.length > 0){
                                        var str = "";
                                        str += "<select lay-ignore name=\'' . $tag['name'] . '_"+(i+1)+"\' dep=\'"+(i+1)+"\' ' . $class . $style . '>";
                                        str += "<option value=\'\' >请选择</option>";
                                        $.each(data,function(h,z){
                                           str += "<option value=\'"+z.id+"\' >"+z.name+"</option>";
                                        });
                                        str += "</select>";
                                        $("select[name=\'' . $tag['name'] . '_"+i+"\']").after(str);
                                        //以上数据输出完，以下绑定事件
                                        $("select[name=\'' . $tag['name'] . '_"+(i+1)+"\']").change(function(){
                                            change' . $tag['name'] . 'Area(i+1,i+2);
                                        });

                                        //如果有返回值，就说明省市区没有选择到最终节点
                                        if( ' . $full . '== 1){
                                            $("input[name=\'' . $tag['name'] . '\']").val("");
                                        }else{
                                            $("input[name=\'' . $tag['name'] . '\']").val($("select[name=\'' . $tag['name'] . '_"+i+"\']").val());
                                        }
                                    }else{
                                        $("input[name=\'' . $tag['name'] . '\']").val($("select[name=\'' . $tag['name'] . '_"+i+"\']").val());
                                    }
                                }
                            });
                        }else{
                            if( ' . $full . ' == 1){
                                $("input[name=\'' . $tag['name'] . '\']").val("");
                            }else{
                                //第一级的元素就直接赋值为空就是了
                                if(i == 1){
                                    $("input[name=\'' . $tag['name'] . '\']").val("");
                                }else{
                                    i--;
                                    $("input[name=\'' . $tag['name'] . '\']").val($("select[name=\'' . $tag['name'] . '_"+ i +"\']").val());
                                }

                            }
                        }
                    }
                });
            </script>
        ';
        return $parse;
    }

    /**
     * 选择品牌标签
     * @param $tag
     * @return string
     */
    public function tagSellerbrands($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }

        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "b" . time() . rand(1, 4);

        $parse = '
            <div id="' . $time . '_box" class="select_seller_brands_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择品牌</a>
                </div>
                <?php
                    $list = model("Brand")->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="' . $time . '_list" class="sellect_seller_brands_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$v["name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';

        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var num_' . $time . ' = "' . $num . '";
                var list_json = {};
                <?php
                $list = model("Brand")->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("index/tagSelectBrands",array("type"=>"show","num"=>"' . $tag['num'] . '"));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "80vh"],
                                    title:"选择品牌",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                    
                                        //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                            layer.msg("最多只能选择"+num_' . $time . '+"个");
                                            return false;
                                        }


                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                            break
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }

    /**
     * 选择商品标签
     * @param $tag
     * @return string
     */
    public function tagSellergoods($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }


        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "g" . time() . rand(1, 4);

        //增加变量key，解决同时存在多个选择商品时的问题
        if (isset($tag['key']) && $tag['key']) {
            $tag['key']  = $this->autoBuildVar($tag['key']);
            $tag['key']  = "<?php echo (" . $tag['key'] . ");?>";
            $tag['name'] = $tag['name'] . '[' . $tag['key'] . ']';
            $time        = $time . '_' . $tag['key'];
        } else {
            $tag['key'] = "";
        }

        $parse = '
            <div id="' . $time . '_box" class="select_seller_goods_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择商品</a>
                </div>
                <?php
                    $goodsModel = new app\common\model\Goods();
                    $list = $goodsModel->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="' . $time . '_list" class="sellect_seller_goods_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$v["name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';

        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var num_' . $time . ' = "' . $num . '";
                var list_json = {};
                <?php
                    $userModel = new app\common\model\Goods();
                    $where[] = ["id","in",' . $tag['value'] . '];
                    $list = $userModel->where($where)->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("manage/index/tagSelectGoods",array("type"=>"show","num"=>"' . $tag['num'] .  '"));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "600px"],
                                    title:"选择商品",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                        //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                            layer.msg("最多只能选择"+num_' . $time . '+"个");
                                            return false;
                                        }


                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                        break;
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }

    /**
     * 选择货品标签
     * @param $tag
     * @return string
     */
    public function tagProducts($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }


        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "g" . time() . rand(1, 4);

        //增加变量key，解决同时存在多个选择货品时的问题
        if (isset($tag['key']) && $tag['key']) {
            $tag['key']  = $this->autoBuildVar($tag['key']);
            $tag['key']  = "<?php echo (" . $tag['key'] . ");?>";
            $tag['name'] = $tag['name'] . '[' . $tag['key'] . ']';
            $time        = $time . '_' . $tag['key'];
        } else {
            $tag['key'] = "";
        }

        $parse = '
            <div id="' . $time . '_box" class="select_seller_goods_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择货品</a>
                </div>
                <?php
                    $productsModel = new app\common\model\Products();
                    $list = $productsModel->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="' . $time . '_list" class="sellect_seller_goods_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$v["name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';

        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var num_' . $time . ' = "' . $num . '";
                var list_json = {};
                <?php
                    $productsModel = new app\common\model\Products();
                    $list = $productsModel->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("manage/index/tagSelectProducts",array("type"=>"show","num"=>"' . $tag['num'] .  '"));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "73vh"],
                                    title:"选择货品",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                        //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                            layer.msg("最多只能选择"+num_' . $time . '+"个");
                                            return false;
                                        }

                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            var spes_desc = "";
                                            if(ids[key].spes_desc){
                                                spes_desc = ids[key].spes_desc;
                                            }
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name +\' \' + spes_desc +\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").focus();
                                        $("#' . $time . '").val(the_val.slice(1));
                                        $("#' . $time . '").blur();
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                            break;
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }


    public function tagUploadImage()
    {
    }

    /**
     * 无限极商品分类  注意 该方法需要开启伪静态
     * @param $tag
     * @return string
     */
    public function tagGoodscat($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }
        $id            = !empty($tag['id']) ? $tag['id'] : '_goods_cat';
        $name          = !empty($tag['name']) ? $tag['name'] : $id;
        $value         = !empty($tag['value']) ? $tag['value'] : 0; //todo 默认值

        $goodsCatModel = new GoodsCat();
        $parseStr      = '<div id="' . $id . '"></div>';
        $selected      = ',selected: []';
        if ($value) {
            $selected = ',selected: [<?php echo ' . $value . ' ?>] ';
        }
        ///Api/Categories/getAllCat?backstage=1
        if (config('?rename_manage') && config('rename_manage')) {
            $catUrl = '/api.html?method=categories.getallcat&backstage=1';
        } else {
            $catUrl = url('/Api/index/index', 'method=categories.getallcat&backstage=1');
        }

        $parseStr .= '<script>
  layui.config({
    base : " __STATIC_LIB__layuiadmin/layui/"
  }).extend({
    selectN: "./layui_ext/select/selectN"
  }).use(["layer","form","jquery","selectN"],function(){
    $ = layui.jquery;
    var form = layui.form
    ,selectN = layui.selectN;

    goodscat' . $id . ' = function(){
            $.ajax({
            type:"get",
            url:"<?php echo "' . $catUrl . '";  ?>",
            data:"",
            success:function(e){
                var catData = e.data;
                var catIns' . $id . ' = selectN({
                  elem: "#' . $id . '"
                  ,name:"' . $name . '"
                  ' . $selected . '
                  ,search:[false,true]
                  ,last:true
                  ,field:{idName:\'id\',titleName:\'name\',statusName:\'status\',childName:\'child\'}
                  ,data: catData
                });
            }
        });
    }
goodscat' . $id . '();
});
</script>';
        return $parseStr;
    }




    /**
     * 选择公告标签
     * @param $tag
     * @return string
     */
    public function tagNoticelist($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }

        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "n" . time() . rand(1, 4);
        if ($tag['value']) {
            $parse = '
            <div id="' . $time . '_box" class="select_seller_notice_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择公告</a>
                </div>
                <?php
                    $list = model("Notice")->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="' . $time . '_list" class="sellect_seller_brands_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$v["name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';
        } else {
            $parse = '
            <div id="' . $time . '_box" class="select_seller_notice_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择公告</a>
                </div>
                 <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="" />
                <ul id="' . $time . '_list" class="sellect_seller_brands_list">
                </ul>
            </div>
        ';
        }
        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var num_' . $time . ' = "' . $num . '";
                var list_json = {};
                <?php
                   $list = model("Notice")->where("id","IN",' . $tag['value'] . ')->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("index/tagSelectNotice",array("type"=>"show","num"=>"' . $tag['num'] .  '));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "80vh"],
                                    title:"选择公告",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                        //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                            layer.msg("最多只能选择"+num_' . $time . '+"个");
                                            return false;
                                        }


                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].title+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                            break;
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }



    /**
     * 商户平台的选择商品标签，总后台不能用，总后台会做另外一个tab标签
     * @param $tag
     * @return string
     */
    public function tagGroup($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }


        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "g" . time() . rand(1, 4);

        //增加变量key，解决同时存在多个选择商品时的问题
        if (isset($tag['key']) && $tag['key']) {
            $tag['key']  = $this->autoBuildVar($tag['key']);
            $tag['key']  = "<?php echo (" . $tag['key'] . ");?>";
            $tag['name'] = $tag['name'] . '[' . $tag['key'] . ']';
            $time        = $time . '_' . $tag['key'];
        } else {
            $tag['key'] = "";
        }

        if ($tag['value']) {
            $parse = '
            <div id="' . $time . '_box" class="select_group_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择团购秒杀商品</a>
                </div>
                <?php
                    $where[] = ["id","in",' . $tag['value'] . '];
                    $where[] = ["type","in","3,4"];
                    $list = model("promotion")->where($where)->select()->toArray();
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="' . $time . '_list" class="select_grout_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$v["name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';
        } else {
            $parse = '
            <div id="' . $time . '_box" class="select_group_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择团购秒杀商品</a>
                </div>
                 <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="" />
                <ul id="' . $time . '_list" class="select_grout_list">
                </ul>
            </div>
        ';
        }

        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var num_' . $time . ' = "' . $num . '";
                var list_json = {};
                <?php
                    $where[] = ["id","in",' . $tag['value'] . '];
                    $where[] = ["type","in","3,4"];
                    $list = model("promotion")->where($where)->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("index/tagSelectGroup",array("type"=>"show","num"=>"' . $tag['num'] .  '));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "80vh"],
                                    title:"选择商品",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                       //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                            layer.msg("最多只能选择"+num_' . $time . '+"个");
                                            return false;
                                        }


                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                            break;
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }

    /**
     * 选择拼团商品
     * @param $tag
     * @return string
     */
    public function tagPintuan($tag)
    {
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }



        $time = "g" . time() . rand(1, 4);

        //增加变量key，解决同时存在多个选择商品时的问题
        if (isset($tag['key']) && $tag['key']) {
            $tag['key']  = $this->autoBuildVar($tag['key']);
            $tag['key']  = "<?php echo (" . $tag['key'] . ");?>";
            $tag['name'] = $tag['name'] . '[' . $tag['key'] . ']';
            $time        = $time . '_' . $tag['key'];
        } else {
            $tag['key'] = "";
        }

        $parse = '
            <div id="' . $time . '_box" class="pintuan_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择拼团商品</a>
                </div>
                <?php
                    $pintuanGoodsModel = new app\common\model\PintuanGoods();
                    $where[] = ["pg.goods_id", "in", ' . $tag['value'] . '];
                    $where[] = ["g.marketable","eq", 1];
                    $where[] = ["pr.status", "eq", 1];
                    $list = $pintuanGoodsModel
                        ->alias("pg")
                        ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
                        ->join("pintuan_rule pr","pg.rule_id = pr.id")
                        ->join("goods g", "g.id = pg.goods_id")
                        ->where($where)
                        ->select();
                    if(!$list->isEmpty()){
                        $list = $list->toArray();
                    }
                ?>
                <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="' . $tag['value'] . '" />
                <ul id="' . $time . '_list" class="pintuan_list">
                    <?php
                        foreach($list as $k => $v){
                            echo \'<li><span id="\'.$v["goods_id"].\'"  >×</span>\'.$v["goods_name"].\'</li>\';
                        }
                    ?>
                </ul>
            </div>
        ';

        $parse .= '
            <script>
                var obj_' . $time . '_ids = {};
                var list_json = {};
                <?php
                    $pintuanGoodsModel = new app\common\model\PintuanGoods();
                    $where[] = ["pg.goods_id", "in", ' . $tag['value'] . '];
                    $where[] = ["g.marketable","eq", 1];
                    $where[] = ["pr.status", "eq", 1];
                    $list = $pintuanGoodsModel
                        ->alias("pg")
                        ->field("pr.*,pg.goods_id,g.name as goods_name,g.image_id as goods_image_id")
                        ->join("pintuan_rule pr","pg.rule_id = pr.id")
                        ->join("goods g", "g.id = pg.goods_id")
                        ->where($where)
                        ->select()->toArray();
                    foreach($list as $k => $v){
                       ?>
                       list_json[<?php echo $v["id"];?>] = [];
                       <?php
    
                    }
               
                ?>
                var ' . $time . '_list = list_json;
                function ' . $time . '_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("manage/index/tagPintuan",array("type"=>"show"));  ?>?time=' . $time . '",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "80vh"],
                                    title:"选择拼团商品",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                        console.log(index);
                                        console.log(ids);
                                        $("#' . $time . '_list").empty();
                                        var the_val = "";
                                        ' . $time . '_list = {};
                                        for(var key in ids){
                                            ' . $time . '_list[key] = ids[key];
                                            $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].goods_name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#' . $time . '").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#' . $time . '_list").delegate("span","click",function(){
                    var ids_array = $("#' . $time . '").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            delete ' . $time . '_list[ids_array[i]];
                            ids_array.splice(i,1);
                            break;
                        }
                    }
                    $("#' . $time . '").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }

    /**
     * 选择用户
     * @param $tag
     * @return string
     */
    public function tagSelectuser($tag)
    {
        //绑定变量
        if (isset($tag['value'])) {
            $tag['value'] = $this->autoBuildVar($tag['value']);
        } else {
            $tag['value'] = "";
        }
        //选择的数量
        if (isset($tag['num'])) {
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        } else {
            $tag['num'] = 1;
            $num = "1";
        }
        $time = "u" . time() . rand(1, 4) . rand(1, 4);
        //增加变量key，解决同时存在多个选择用户时的问题

        if (isset($tag['key']) && $tag['key']) {
            $tag['key']  = $this->autoBuildVar($tag['key']);
            $tag['key']  = "<?php echo (" . $tag['key'] . ");?>";
            $tag['name'] = $tag['name'] . '[' . $tag['key'] . ']';
            $time        = $time . '_' . $tag['key'];
        } else {
            $tag['key'] = "";
        }
        //判断用户等级
        $where = [];
        if (isset($tag['grade'])) {
            $tag['grade'] = $this->autoBuildVar($tag['grade']);
            $grade = "<?php echo (" . $tag['grade'] . ");?>";
            $where[] = ['grade', 'in', $grade];
        } else {
            $grade = "";
        }
        $parse = '
        <div id="' . $time . '_box" class="select_userlist_box">
            <div>
                <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="' . $time . '_show();"><i class="iconfont icon-choose1"></i>选择用户</a>
            </div>
            <?php
                $userModel = new app\common\model\User();
                $where[] = ["id","in",' . $tag['value'] . '];
                $list = $userModel->where($where)->select()->toArray();
               
               
            ?>
            <input type="hidden" name="' . $tag['name'] . '" id="' . $time . '" value="<?php echo implode(",",array_column($list,"id")) ?>" />
            <ul id="' . $time . '_list" class="sellect_seller_goods_list">
                <?php
                    foreach($list as $k => $v){
                        $s = "";
                        if(!empty($v[\'username\'])) {
                            $s = $v["username"];
                        }
                        $s .= "(";
                         if(!empty($v[\'mobile\'])) {
                            $s .= $v["mobile"];
                        }
                         $s .= ")";
                        echo \'<li><span id="\'.$v["id"].\'"  >×</span>\'.$s.\'</li>\';
                    }
                ?>
            </ul>
        </div>
    ';

        $parse .= '
        <script>
            var obj_' . $time . '_ids = {};
            var num_' . $time . ' = "' . $num . '";
            var grade = "' . '' . $tag['grade'] . '";
            var list_json = {};
            <?php
                $userModel = new app\common\model\User();
                $where[] = ["id","in",' . $tag['value'] . '];
                $list = $userModel->where($where)->select()->toArray();
                foreach($list as $k => $v){
                   ?>
                   list_json[<?php echo $v["id"];?>] = [];
                   <?php

                }
           
            ?>
            var ' . $time . '_list = list_json;
            function ' . $time . '_show(){
                layui.use([\'form\', \'table\'], function(){
                    $.ajax({
                        type:"post",
                        url:"<?php echo url("manage/index/tagSelectUser",array("type"=>"show","num"=>"' . $tag['num'] .  '"));  ?>?grade="+grade+"&time=' . $time . '",
                        success:function(e){
                            layui.layer.open({
                                type: 1,
                                content: e,
                                area: ["800px", "80vh"],
                                title:"选择用户",
                                btn: ["完成","取消"],
                                yes: function(index, layero){
                                    //判断个数是否满足
                                    if(Object.getOwnPropertyNames(ids).length > num_' . $time . '){
                                        layer.msg("最多只能选择"+num_' . $time . '+"个");
                                        return false;
                                    }
                                  
                                    $("#' . $time . '_list").empty();
                                    var the_val = "";
                                    ' . $time . '_list = {};
                                    for(var key in ids){

                                        if(key){
                                        ' . $time . '_list[key] = ids[key];
                                        let str = "";
                                        if(ids[key].username != null) str+=ids[key].username
                                        str+="("
                                        if(ids[key].mobile != null) str+=ids[key].mobile
                                        str+=")"
                                        $("#' . $time . '_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+str+\'</li>\');
                                        the_val += "," + key;
                                        }
                                    }
                                    $("#' . $time . '").focus();
                                    $("#' . $time . '").val(the_val.slice(1));
                                    $("#' . $time . '").blur();
                                    layer.close(index);
                                }
                            });
                        }
                    });
                });
            }
            
            $("#' . $time . '_list").delegate("span","click",function(){
                var ids_array = $("#' . $time . '").val().split(",");
                for (var i=0;i<ids_array.length ;i++ )
                {
                    if(ids_array[i] == $(this).attr("id")){
                        delete ' . $time . '_list[ids_array[i]];
                        ids_array.splice(i,1);
                        break;
                    }
                }
                $("#' . $time . '").val(ids_array.join(","));
                $(this).parent().remove();
            });
        </script>
    ';
        return $parse;
    }
}
