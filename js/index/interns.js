/**
 * Created by
 */
$(function(){
//    加载更多
    $('.more').on("click",function(){
        var current_page = parseInt($("#page").val())+1;
        var city = $("#txt_city").val();
        var job = $("#txt_job").val();
        var interns_url = $("#hide_value #interns_url").val();
        $(".more").html("加载中...");
        $.ajax({
            url:interns_url,
            type:'get',
            data:{page:current_page,city:city,keyword:job},
            dataType:'json',
            success:function(result){
                if(result.success){
                    var data = result.intern_list;
                    var more = $(".more");
                    for(var v in data){
                        var key ='<a href="'+data[v].url+'">'
                            +'<div class="u-item" data-internid="'+data[v].sid+'">'
                            +'<div class="margin10">'
                            +'<div class="item-title">'
                            +data[v].name
                            +'<span class="item-time">'+data[v].time+'</span>'
                            +'</div>'
                            +'<div class="item-meta">'
                            +'    <span class="color-stress">'+data[v].minsalary+'-'+data[v].maxsalary+'元/天</span> · '+data[v].city+' · ≥'+data[v].dayperweek+'天 · '+data[v].com_name
                            +'</div></div></div></a>';
                        more.before(key);
                    }
                    $("#page").val(result.current_page);
                    $(".more").html("加载更多");
                    if(result.isLast){
                        $(".more").html("没有更多了");
                        $(".more").addClass("nomore");
                        $(".more").off("click");
                        $(".nomore").removeClass("more");
                    }
                }else{
                    alert("加载失败！");
                }
            }
        });
    });
});