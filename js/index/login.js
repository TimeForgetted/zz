/**
 * Created .
 */
$(function(){
    $("#loginBtn").click(function(){
        if($("#username").val() == ''){
            $("#username-tip").css("display", "inline");
        }
        else if($("#password").val().length < 6){
            $("#password-tip").css("display", "inline");
        }
        else {
            var username = $("#username").val();
            var password = hex_md5($("#password").val());
            var is_remember = $("#checkbox").attr("checked");
            var action_url = $("#loginForm").attr("data-url");
            $.ajax({
                url:action_url,
                type:"post",
                data:{
                    username: username,
                    password: password,
                    is_remember : is_remember
                },
                dataType:"json",
                success:function(data){
                    if(data.success){
                        window.location.href = document.referrer;
                    }
                    else {
                        $("#login-tip").css("display", "inline");
                        $("#password").val("");
                    }
                }
            });
        }
    });
    $("#username").focus(function(){
        $("#username-tip").addClass("dn");
        $("#login-tip").addClass("dn");
    });
    $("#password").focus(function(){
        $("#password-tip").addClass("dn");
        $("#login-tip").addClass("dn");
    });
});