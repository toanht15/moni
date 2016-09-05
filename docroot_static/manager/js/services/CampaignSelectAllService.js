
var checkboxes_status = document.getElementsByName('status[]');
    checkboxes_show = document.getElementsByName('show[]');
    checkboxes_module = document.getElementsByName('module[]');
    checkboxes_status_select_all = document.getElementsByName('checkAllStatus');
    checkboxes_show_select_all = document.getElementsByName('checkAllShow');
    checkboxes_module_select_all = document.getElementsByName('checkAllModule');

$(document).ready(function(){

    $(checkboxes_status_select_all).click(
        function() {
            $(this).closest('div').find(checkboxes_status).prop('checked', this.checked);
        }
    );

    $(checkboxes_show_select_all).click(
        function() {
            $(this).closest('div').find(checkboxes_show).prop('checked', this.checked);
        }
    );

    $(checkboxes_module_select_all).click(
        function() {
            $(this).closest('div').find(checkboxes_module).prop('checked', this.checked);
        }
    );

    $(checkboxes_status).click(
        function() {
            if ($(this).closest('div').find(checkboxes_status_select_all).prop('checked') == true && this.checked == false)
                $(this).closest('div').find(checkboxes_status_select_all).prop('checked', false);
            if (this.checked == true) {
                var flag = true;
                $(this).closest('div').find(checkboxes_status).each(
                    function() {
                        if (this.checked == false)
                            flag = false;
                    }
                );
                $(this).closest('div').find(checkboxes_status_select_all).prop('checked', flag);
            }
        }
    );

    $(checkboxes_show).click(
        function() {
            if ($(this).closest('div').find(checkboxes_show_select_all).prop('checked') == true && this.checked == false)
                $(this).closest('div').find(checkboxes_show_select_all).prop('checked', false);
            if (this.checked == true) {
                var flag = true;
                $(this).closest('div').find(checkboxes_show).each(
                    function() {
                        if (this.checked == false)
                            flag = false;
                    }
                );
                $(this).closest('div').find(checkboxes_show_select_all).prop('checked', flag);
            }
        }
    );

    $(checkboxes_module).click(
        function() {
            if ($(this).closest('div').find(checkboxes_module_select_all).prop('checked') == true && this.checked == false)
                $(this).closest('div').find(checkboxes_module_select_all).prop('checked', false);
            if (this.checked == true) {
                var flag = true;
                $(this).closest('div').find(checkboxes_module).each(
                    function() {
                        if (this.checked == false)
                            flag = false;
                    }
                );
                $(this).closest('div').find(checkboxes_module_select_all).prop('checked', flag);
            }
        }
    );
});