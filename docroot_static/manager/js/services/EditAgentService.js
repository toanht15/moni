var EditAgentService = (function () {
    return {
        createSource: function (request, response) {
            var selectBox = $('#jsBrandSelectBox');
            var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");

            response(selectBox.children('option').map(function () {
                var text = $(this).text();
                if (this.value && ( !request.term || matcher.test(text) ))
                    return {
                        label: text,
                        value: text,
                        option: this
                    };
            }));
        },
        createAutoComplete: function () {
            var source = $.proxy(this, "createSource");

            $('.autoCompleteInput').autocomplete({
                delay: 0,
                minLength: 0,
                source: source,
                autoFocus: true
            });
        },
        showAllData: function () {
            if ($(".ui-autocomplete").is(":hidden")) {
                $('.autoCompleteInput').autocomplete("search", "");
            } else {
                $(".ui-autocomplete").hide();
            }
        },
        removeInvalidInput: function (event, ui) {
            if (ui.item) {
                return;
            }

            var value = $(this).val(),
                valueLowerCase = value.toLowerCase(),
                valid = false,
                selectBox = $('#jsBrandSelectBox');

            selectBox.children("option").each(function () {
                if ($(this).text().toLowerCase() === valueLowerCase) {
                    this.selected = valid = true;
                    return false;
                }
            });

            // Found a match, nothing to do
            if (valid) {
                return;
            }

            //remove invalid value input field
            $(this).val('');
        },
        addBrandRelation: function (brand_id, manager_id) {
            var csrf_token = $('[name=csrf_token]')[0].value;
            var data = {
                csrf_token: csrf_token,
                brand_id: brand_id,
                manager_id: manager_id,
                update_type: 1
            };
            var url = '/api/api_update_brand_agent.json';
            var param = {
                data: data,
                url: url,
                type: 'POST',
                success: function (response) {
                    if(response.result == 'ok') {
                        location.reload();
                    } else {
                        alert("エラーを発生されました！");
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        },
        deleteBrandRelation: function (target, brand_id, manager_id) {
            var csrf_token = $('[name=csrf_token]')[0].value;
            var data = {
                csrf_token: csrf_token,
                brand_id: brand_id,
                manager_id: manager_id,
                update_type: 2
            };
            var url = '/api/api_update_brand_agent.json';
            var param = {
                data: data,
                url: url,
                type: 'POST',
                success: function (response) {
                    if(response.result == 'ok') {
                        location.reload();
                    } else {
                        alert("エラーを発生されました！");
                    }
                }
            };
            Brandco.api.callAjaxWithParam(param, false, false);
        }
    }
})();

$(document).ready(function () {
    EditAgentService.createAutoComplete();

    $('.jShowAllData').on('click', function () {
        $('.autoCompleteInput').val('');
        EditAgentService.showAllData();
    });

    //set selected for select box when select autocomplete
    $('.autoCompleteInput').on('autocompleteselect', function (event, ui) {
        ui.item.option.selected = true;
        $('#jsBrandSelectBox').val(ui.item.option.value);
    });

    //Remove if input value not match with any select value
    $('.autoCompleteInput').on('autocompletechange', EditAgentService.removeInvalidInput);

    $('.jsAddBrand').on('click', function (event) {
        event.preventDefault();
        var brand_id = $('#jsBrandSelectBox option:selected').val();

        if (!brand_id || brand_id == 0) {
            alert("ブランドを選択してください。");
        } else {
            if (confirm($(this).data('message'))) {
                var manager_id = $('input[name="manager_id"]').attr('value');
                EditAgentService.addBrandRelation(brand_id, manager_id);
            }
        }
    });

    $('.jsDeleteBrand').on('click', function (event) {
        event.preventDefault();
        if (confirm($(this).data('message'))) {
            var brand_id = $(this).data('action');
            var manager_id = $('input[name="manager_id"]').attr('value');
            EditAgentService.deleteBrandRelation($(this), brand_id, manager_id);
        }
    });
});