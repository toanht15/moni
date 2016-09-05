$(document).ready(function(){
    $('#submit').click(function(){
        $(window).unbind('beforeunload');
        document.attractForm.action = $(this).data('action');
        document.attractForm.submit();
    });

    $('#editButton').click(function(){
        Brandco.helper.edit_cp($(this));
    });

    $('#submitDraft').click(function(){
        $(window).unbind('beforeunload');
        document.attractForm.action = $(this).data('action');
        document.attractForm.submit();
    });

    $(':input[type="checkbox"]').each(function(){
        $(this).change(function(){
            $(window).unbind('beforeunload');
            Brandco.helper.set_reload_alert(Brandco.message.reloadMessage);
        });
    });

    Brandco.admin.adminCpInit();
});
