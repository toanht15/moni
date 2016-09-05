var DeleteAdministrator = (function(){
    return {
        setDeleteInfo : function(target){
            var name = target.closest('li').find('.adminUserName').html();
            var profile_image_url = target.closest('li').find('.adminUserImg').attr("src");
            var option_value = target.attr('data-option');
            $("#userName").html(name);
            $("#userImg").attr("src", profile_image_url);
            $('input[name="admin_uid"]').val(option_value);
            Brandco.unit.openModal("#modal1");
            return false;
        }
    }
})();
$(document).ready(function(){
    $('#submitButton').click(function(){
        document.frmDelete.submit();
    });

    $('.adminUserDelete').on("click", function () {
        DeleteAdministrator.setDeleteInfo($(this));
    });

    $('#confirmButton').click(function(){
        Brandco.unit.openModal('#modal2');
    });

    $('#sendMail').click(function(){
        document.frmMailForm.submit();
    });

});