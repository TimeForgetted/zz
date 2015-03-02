/**
 * Created by 
 */
$(function(){
    $(document).click(function(e){
        $(".div_manage").addClass("dn");
    });
    // 弹出管理按钮框
    $("header .btn-login").click(function(e){
        e.stopPropagation();
        var obj = $(this);
        var id = obj.attr("id");
        if(id == "btn_manage"){
            var isHide = $(".div_manage").hasClass("dn");
            if(isHide)
                $(".div_manage").removeClass("dn");
            else
                $(".div_manage").addClass("dn");
        }
    });
    // 管理框中按钮事件
    $(".div_manage .u-item").click(function(){
        var obj = $(this);
        var id = obj.attr("id");
        switch(id){
            case "logout":logout(obj);break;
        }
    });
});
//登出函数
function logout(obj){
    var url = obj.attr("data-url");
    $.ajax({
        url:url,
        type:"post",
        dataType:"json",
        success:function(result){
            if(result.success){
                $("header #btn_login").removeClass("dn");
                $("header #btn_manage").addClass("dn");
                $("header .div_manage").addClass("dn");
            }else{
                alert(result.msg);
            }
        }
    });
}