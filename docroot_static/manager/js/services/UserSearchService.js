var ManagerUserSearchService = (function(){
    return {
        initUserSearch: function () {
            ManagerUserSearchService.showSearchForm();
        },
        showSearchForm: function () {
            $('.jsSearchGroup').hide();
            var form = '#searchGroup' + $('#searchType').val();
            $(form).show();
        },
        activateUserListButton: function (target) {
            $('.jsUserList').removeClass('active');
            $(target).addClass('active');
        },
        showUser: function (target) {
            var user = '#userAccount' + $(target).data('userId');
            $(user).show();
        }
    };
})();


$(document).ready(function() {

    //load
    ManagerUserSearchService.initUserSearch();

    $('#searchType').change(function(){
        ManagerUserSearchService.showSearchForm();
    });

    $('.jsUserList').click(function() {
        ManagerUserSearchService.activateUserListButton(this);
        ManagerUserSearchService.showUser(this);
    });

    $('.jsConfirmAlert').click(function(event) {
        event.preventDefault();
        if (confirm($(this).data('message'))) {
            $(this).closest('form').submit();
        }
    });
});