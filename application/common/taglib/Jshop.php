<?php
/**
 * Created by PhpStorm.
 * User: wjima
 * Email:1457529125@qq.com
 * Date: 2018-01-08
 * Time: 19:51
 */
namespace app\common\taglib;

use think\template\TagLib;


class Jshop extends TagLib
{

    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'image' => [
            'attr'  => 'id,name,style,width,height,type,value',
            'close' => 0
        ],
        'uploadImage' => [
            'attr'  => 'id,name,style,width,height,type,value',
            'close' => 0
        ],
        'input_radio'=>[],
        'area' => [
            'attr' => 'name,value,class,style',
            'close' => 0
        ],
        //此标签增加了权限判断，只供商户端（seller）使用
        'sellerbrands' => [
            'attr' => 'name,value,num',
            'close' => 0
        ],
        //此标签增加了权限判断，只供商户端（seller）使用
        'sellergoods' => [
            'attr' => 'name,value,num',
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
        $value    = !empty($tag['value']) ? $tag['value'] : config('jshop.default_image'); //todo 默认图片
        $type     = !empty($tag['type']) ? $tag['type'] : '';
        $num      = !empty($tag['num']) ? $tag['num'] : 1;
        $width    = !empty($tag['width']) ? $tag['width'] : '90px';
        $height   = !empty($tag['height']) ? $tag['height'] : '90px';
        $str_name = '';
        if($num > 1) {
            $str_name = $name . '[]';
        }else {
            $str_name = $name;
        }
        if(isset($tag['value']) && !empty($tag['value'])) {
            $value = $this->autoBuildVar($value);
            $parseStr = '
            <button type="button" class="layui-btn" id="upload_img_' . $id . '" onclick="upImag'.$id.'e()">上传图片</button>
            <div class="layui-upload-list">
                <img class="layui-upload-img"  src=' . "'" . '<?php echo _sImage(' . $value . ')?>' . "'" . ' id="image_src_' . $id . '" style="width:' . $width . ';height:' . $height . ';" >
                <p id="upload_text_' . $id . '"></p>
            </div>
            <input class="layui-upload-file" type="hidden" name="' . $str_name . '"  id="image_value_' . $id . '" value="' . "<?php echo " . $value . "?>" . '">
            <textarea id="edit_'.$id.'" style="display: none;"></textarea>
            <script>
            var _edito'.$id.'r = UE.getEditor("edit_'.$id.'",{
                initialFrameWidth:800,
                initialFrameHeight:300,
            });
            _edito'.$id.'r.ready(function (){
                //_edito'.$id.'r.setDisabled();
                _edito'.$id.'r.hide();
                //侦听图片上传
                _edito'.$id.'r.addListener(\'beforeInsertImage\',function(t,arg){
                        $("#image_value_'.$id.'").attr("value",arg[0].image_id);
                        $("#image_src_'.$id.'").attr("src",arg[0].src);
                });
            });
            //上传dialog
            function upImag'.$id.'e(){
                var myImag'.$id.'e = _edito'.$id.'r.getDialog("insertimage");
                myImag'.$id.'e.open();
            }
</script>
            ';
        }else {
            $parseStr = '
            <button type="button" class="layui-btn" id="upload_img_' . $id . '" onclick="upImag'.$id.'e()">上传图片</button>
            <div class="layui-upload-list">
                <img class="layui-upload-img"  src="' . $value . '" id="image_src_' . $id . '" style="width:' . $width . ';height:' . $height . ';" >
                <p id="upload_text_' . $id . '"></p>
            </div>
            <input class="layui-upload-file" type="hidden" name="' . $str_name . '"  id="image_value_' . $id . '" value="">
            <textarea id="edit_'.$id.'" style="display: none;"></textarea>
            <script>
            var _edito'.$id.'r = UE.getEditor("edit_'.$id.'",{
                initialFrameWidth:800,
                initialFrameHeight:300,
            });
            _edito'.$id.'r.ready(function (){
                //_edito'.$id.'r.setDisabled();
                _edito'.$id.'r.hide();
                //侦听图片上传
                _edito'.$id.'r.addListener(\'beforeInsertImage\',function(t,arg){
                        $("#image_value_'.$id.'").attr("value",arg[0].image_id);
                        //将图片地址赋给img的src,实现预览
                        $("#image_src_'.$id.'").attr("src",arg[0].src);
                });
            });
            //上传dialog
            function upImag'.$id.'e(){
                var myImag'.$id.'e = _edito'.$id.'r.getDialog("insertimage");
                myImag'.$id.'e.open();
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
        $parse = '<input type="hidden" name="'.$tag['name'].'"';
        if(isset($tag['value'])){
            $tag['value'] = $this->autoBuildVar($tag['value']);
            $js_val = "id=<?php echo (" . $tag['value'] . ");?>";
            $parse .= ' value="<?php echo (' . $tag['value'] . ');?>"';
        }else{
            $js_val = "id=0";
        }
        $parse .= ' />';

        if(isset($tag['class'])){
            $class = ' class=\''.$tag['class'].'\'';
        }else {
            $class = "";
        }
        if(isset($tag['style'])){
            $style = ' style=\''.$tag['style'].'\'';
        }else {
            $style = "";
        }
        $full     = !empty($tag['full']) ? $tag['full'] : '1';      //传1或者2，如果是1就说明是省市区必须得输入到最后一个节点才有值，否则就是任意点都有值
        
        $parse .= '
            <script>
                $(function(){
                    $.ajax({
                        type: "POST",
                        url: "'.url('api/common/area').'",
                        data: "'.$js_val.'",
                        success:function(data) {
                            var str = "";
                            $.each(data, function(i,n){
                                str += "<select lay-ignore name=\''.$tag['name'].'_"+(i+1)+"\' dep=\'"+(i+1)+"\' '.$class.$style.'>";
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
                            $("input[name=\''.$tag['name'].'\']").after(str);
                            //以上数据输出完，以下绑定事件
                            $.each(data, function(i,n){
                                if(i<(data.length)){
                                    $("select[name=\''.$tag['name'].'_"+(i+1)+"\']").change(function(){
                                        change'.$tag['name'].'Area(i+1,data.length);
                                    });
                                }
                            });

                        }
                    });
                    function change'.$tag['name'].'Area(i,max_i){
                        //清除后面节点
                        for(var x = i+1;x<=6;x++){  //最多6层，足够了
                            $("select[name=\''.$tag['name'].'_"+x+"\']").remove();
                        }
                        var val = $("select[name=\''.$tag['name'].'_"+i+"\']").val();
                        if(val != ""){
                            //取子节点数据，然后显示下一级
                            $.ajax({
                                type: "POST",
                                url: "'.url('api/common/areaChildren').'",
                                data: "id="+val,
                                success:function(data) {
                                    if(data.length > 0){
                                        var str = "";
                                        str += "<select lay-ignore name=\''.$tag['name'].'_"+(i+1)+"\' dep=\'"+(i+1)+"\' '.$class.$style.'>";
                                        str += "<option value=\'\' >请选择</option>";
                                        $.each(data,function(h,z){
                                           str += "<option value=\'"+z.id+"\' >"+z.name+"</option>";
                                        });
                                        str += "</select>";
                                        $("select[name=\''.$tag['name'].'_"+i+"\']").after(str);
                                        //以上数据输出完，以下绑定事件
                                        $("select[name=\''.$tag['name'].'_"+(i+1)+"\']").change(function(){
                                            change'.$tag['name'].'Area(i+1,i+2);
                                        });

                                        //如果有返回值，就说明省市区没有选择到最终节点
                                        if( '.$full.'== 1){
                                            $("input[name=\''.$tag['name'].'\']").val("");
                                        }else{
                                            $("input[name=\''.$tag['name'].'\']").val($("select[name=\''.$tag['name'].'_"+i+"\']").val());
                                        }
                                    }else{
                                        $("input[name=\''.$tag['name'].'\']").val($("select[name=\''.$tag['name'].'_"+i+"\']").val());
                                    }
                                }
                            });
                        }else{
                            if( '.$full.' == 1){
                                $("input[name=\''.$tag['name'].'\']").val("");
                            }else{
                                //第一级的元素就直接赋值为空就是了
                                if(i == 1){
                                    $("input[name=\''.$tag['name'].'\']").val("");
                                }else{
                                    i--;
                                    $("input[name=\''.$tag['name'].'\']").val($("select[name=\''.$tag['name'].'_"+ i +"\']").val());
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
     * 商户平台的选择品牌标签，总后台不能用，总后台会做另外一个tab标签
     * @param $tag
     * @return string
     */
    public function tagSellerbrands($tag)
    {
        if(isset($tag['value'])){
            $tag['value'] = $this->autoBuildVar($tag['value']);
        }else{
            $tag['value'] = "";
        }

        if(isset($tag['num'])){
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        }else{
            $num = "1";
        }
        $time = "b".time().rand(1,4);

        $parse = '
            <div id="'.$time.'_box" class="select_seller_brands_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="'.$time.'_show();"><i class="iconfont icon-choose1"></i>选择品牌</a>
                </div>
                <?php
                    $list = model("Brand")->where("id","IN",'. $tag['value'] .')->select()->toArray();
                ?>
                <input type="hidden" name="'.$tag['name'].'" id="'.$time.'" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="'.$time.'_list" class="sellect_seller_brands_list">
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
                var obj_'.$time.'_ids = {};
                var num_'.$time.' = "'.$num.'";
                function '.$time.'_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("index/tagSelectBrands",array("type"=>"show"));  ?>",
                            data:"",
                            success:function(e){
                                layui.layer.open({
                                    type: 1,
                                    content: e,
                                    area: ["800px", "600px"],
                                    title:"选择品牌",
                                    btn: ["完成","取消"],
                                    yes: function(index, layero){
                                        //判断个数是否满足
                                        if(Object.getOwnPropertyNames(ids).length > num_'.$time.'){
                                            layer.msg("最多只能选择"+num_'.$time.'+"个");
                                            return false;
                                        }


                                        $("#'.$time.'_list").empty();
                                        var the_val = "";
                                        for(var key in ids){
                                            $("#'.$time.'_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#'.$time.'").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#'.$time.'_list").delegate("span","click",function(){
                    var ids_array = $("#'.$time.'").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            ids_array.splice(i,1);
                        }
                    }
                    $("#'.$time.'").val(ids_array.join(","));
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
    public function tagSellergoods($tag)
    {
        if(isset($tag['value'])){
            $tag['value'] = $this->autoBuildVar($tag['value']);
        }else{
            $tag['value'] = "";
        }

        if(isset($tag['num'])){
            $tag['num'] = $this->autoBuildVar($tag['num']);
            $num = "<?php echo (" . $tag['num'] . ");?>";
        }else{
            $num = "1";
        }
        $time = "g".time().rand(1,4);

        $parse = '
            <div id="'.$time.'_box" class="select_seller_goods_box">
                <div>
                    <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-sm" onclick="'.$time.'_show();"><i class="iconfont icon-choose1"></i>选择商品</a>
                </div>
                <?php
                    $list = model("Goods")->where("id","IN",'. $tag['value'] .')->select()->toArray();
                ?>
                <input type="hidden" name="'.$tag['name'].'" id="'.$time.'" value="<?php echo implode(",",array_column($list,"id")) ?>" />
                <ul id="'.$time.'_list" class="sellect_seller_goods_list">
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
                var obj_'.$time.'_ids = {};
                var num_'.$time.' = "'.$num.'";
                function '.$time.'_show(){
                    layui.use([\'form\', \'table\'], function(){
                        $.ajax({
                            type:"get",
                            url:"<?php echo url("index/tagSelectGoods",array("type"=>"show"));  ?>",
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
                                        if(Object.getOwnPropertyNames(ids).length > num_'.$time.'){
                                            layer.msg("最多只能选择"+num_'.$time.'+"个");
                                            return false;
                                        }


                                        $("#'.$time.'_list").empty();
                                        var the_val = "";
                                        for(var key in ids){
                                            $("#'.$time.'_list").append(\'<li><span id="\'+key+\'"  >×</span>\'+ids[key].name+\'</li>\');
                                            the_val += "," + key;
                                        }
                                        $("#'.$time.'").val(the_val.slice(1));
                                        layer.close(index);
                                    }
                                });
                            }
                        });
                    });
                }
                $("#'.$time.'_list").delegate("span","click",function(){
                    var ids_array = $("#'.$time.'").val().split(",");
                    for (var i=0;i<ids_array.length ;i++ )
                    {
                        if(ids_array[i] == $(this).attr("id")){
                            ids_array.splice(i,1);
                        }
                    }
                    $("#'.$time.'").val(ids_array.join(","));
                    $(this).parent().remove();
                });
            </script>
        ';
        return $parse;
    }

    public function tagUploadImage(){

    }
}
