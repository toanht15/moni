var StaticHtmlCategoriesService = (function () {
    return {
        new_num: 0,
        new_categoryId: function () {
            return 'new_' + this.new_num;
        },
        deleted_category: [],
        createCategoryTree: function (ul) {
            var data = {},
                order = 0;
            ul.children('li.categoryEntry').each(function () {
                var childUl = $(this).find('ul'),
                    currentId = $(this).data('category-id');
                data[currentId] = {};
                data[currentId]['name'] = $('#category_name_' + currentId).html();
                data[currentId]['directory'] = $('#directory_' + currentId).data('directory');
                data[currentId]['order'] = order++;
                if (childUl[0]) {
                    var childData = StaticHtmlCategoriesService.createCategoryTree(childUl.first());
                    if (!jQuery.isEmptyObject(childData)) {
                        data[currentId]['children'] = childData;
                    }
                }
            });
            return data;
        },
        reloadDirectory: function (ul) {
            ul.children('li.categoryEntry').each(function () {
                if ($(this).parents('li').parents('li')[0]) {
                    $(this).find('.iconBtnAdd').hide();
                } else {
                    $(this).find('.iconBtnAdd').show();
                }
                var directory = $('#directory_' + $(this).data('category-id'));
                if ($(this).closest('ul').closest('li')[0]) {
                    directory.html($('#directory_' + $(this).parents('li').data('category-id')).html() + $('#directory_' + $(this).data('category-id')).data('directory')+'/');
                } else {
                    directory.html('/'+directory.data('directory')+'/');
                }
                var childUl = $(this).find('ul');
                if (childUl[0]) {
                    StaticHtmlCategoriesService.reloadDirectory(childUl.first());
                }
            });
        },
        initCategoryAction: function () {
            $('.iconBtnAdd').unbind('click');
            $('.iconBtnAdd').click(function () {
                $('.categoryNew').hide(300);
                $('.attention1').remove();
                var li = $(this).closest('li');
                $('#category2_' + li.data('category-id')).slideToggle(300);
            });

            $('.jsCategoryCancel').unbind('click');
            $('.jsCategoryCancel').click(function () {
                $(this).closest('.categoryNew').slideToggle(300);
                $('.attention1').remove();
            });

            $('.iconBtnNonDisplay').unbind('click');
            $('.iconBtnNonDisplay').click(function () {
                var category_id = $(this).closest('li').data('category-id');
                $('#deleteMessage').html("【" + $('#category_name_' + category_id).html() + "】");
                $('#deleteCategoryButton').attr('data-category-id', category_id);
                $('.attention1').hide();
                Brandco.unit.openModal('#modal1');
            });

            $('.submitCategory').unbind('click');
            $('.submitCategory').click(function () {
                $('span[class="iconError1"]').remove();
                var category2 = $(this).closest('.categoryNew'),
                    name = category2.find('input[name="name"]').val(),
                    path = category2.find('input[name="directory"]').val(),
                    validation = true;

                $('a[id^="category_name_"]').each(function () {
                    if ($(this).html() == name) {
                        if (!$('.attention1')[0])
                            category2.append('<span class="iconError1">カテゴリー名が同一階層内で重複しています</span>');
                        validation = false;
                        return;
                    }
                });

                $('span[id^="directory_"]').each(function () {
                    if ($(this).data('directory') == path) {
                        if (!$('.attention1')[0])
                            category2.append('<span class="iconError1">ディレクトリー名が同一階層内で重複しています</span>');
                        validation = false;
                        return;
                    }
                });

                if (!validation) {
                    return;
                }

                var plusButton = '<a href="javascript:void(0)" class="iconBtnAdd">子カテゴリを追加する</a>';
                if ($(this).parents('li').parents('li').parents('li')[0]) {
                    plusButton = '<a href="javascript:void(0)" class="iconBtnAdd" style="display: none">子カテゴリを追加する</a>';
                }
                var html = '<li data-category-id="' + StaticHtmlCategoriesService.new_categoryId() + '" style="display: none" class="newCategory categoryEntry"><p class="category1">';
                html += '<span class="categoryMove">順番を入れ替える</span><a href="javascript:void(0)" id="category_name_' + StaticHtmlCategoriesService.new_categoryId() + '">' + name + '</a>';
                html += '<span class="directory" id="directory_' + StaticHtmlCategoriesService.new_categoryId() + '" data-directory="' + path + '">';
                if ($('#directory_' + $(this).parents('li').parents('li').data('category-id'))[0]) {
                    html += $('#directory_' + $(this).parents('li').parents('li').data('category-id')).html() + path + '/</span>';
                } else {
                    html += '/'+ path + '/</span>';
                }
                html += '<span class="categoryAction">' + plusButton + '<a href="javascript:void(0)" class="iconBtnNonDisplay jsOpenModal">カテゴリを削除する</a></span>';
                html += '<!-- /.category1 --></p><ul class="categoryList1">';

                html += '<li><p class="categoryNew" style="display: none" id="category2_' + StaticHtmlCategoriesService.new_categoryId() + '">';
                html += '<input type="text" name="name" placeholder="カテゴリ名"><input type="text" name="directory" placeholder="ディレクトリ名"><span class="btn3"><a href="javascript:void(0)" class="small1 submitCategory">追加する</a></span>';
                html += '<span class="categoryAction"><a href="javascript:void(0)" class="iconBtnDelete jsCategoryCancel">キャンセル</a></span>';

                html += '<!-- /.category2 --></p></li>';
                html += '</ul></li>';

                $(html).insertAfter($(this).closest('li'));
                $(this).closest('p').slideToggle(300);
                $('.newCategory').slideToggle(300, function () {
                    $(this).removeClass('newCategory');
                });
                $('.attention1').remove();
                StaticHtmlCategoriesService.new_num++;
                StaticHtmlCategoriesService.initCategoryAction();
            });
        }
    }
})();

$(document).ready(function () {

    $('#deleteCategoryButton').click(function () {
        Brandco.unit.closeModal(1);
        if (!isNaN($(this).attr('data-category-id'))) {
            StaticHtmlCategoriesService.deleted_category.push($(this).attr('data-category-id'));
        }
        $('li[data-category-id="' + $(this).attr('data-category-id') + '"]').hide(300, function () {
            $(this).remove();
        });
    });

    $('.categoriesTree').nestedSortable({
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: .6,
        placeholder: 'categoryHolder',
        errorClass: 'categoryError',
        revert: 250,
        handle: 'span[class="categoryMove"]',
        listType: 'ul',
        maxLevels: 4,
        items: 'li',
        tolerance: "pointer",
        toleranceElement: '> p',
        update: function (event, ui) {
            StaticHtmlCategoriesService.reloadDirectory($('.categoriesTree'));
        },
        sort: function (event, ui) {
            if ($('.categoryError')[0]) {
                $('.categoryError').html('これより下には移動できません');
            } else {
                $('.categoryHolder').html('');
            }
        }
    });

    $('.save_all').click(function () {
        var csrf_token = document.getElementsByName("csrf_token")[0].value,
            data = StaticHtmlCategoriesService.createCategoryTree($('.categoriesTree')),
            category_navi_top_display_flg = $('#category_navi_top_display_flg_1:checked').val(),
            param = {
                data: {'categories_data': data, 'deleted_categories': StaticHtmlCategoriesService.deleted_category, 'csrf_token': csrf_token, 'category_navi_top_display_flg' : category_navi_top_display_flg},
                url: 'admin-blog/update_categories.json',
                success: function (data) {
                    if (!data) {
                        return;
                    }
                    if (data.result == 'ng') {
                        $('#errorHeader').html(data.errors.message);
                        $('#errorMessage').html(data.errors.content);
                        Brandco.unit.openModal('#modal2');
                    } else {
                        document.location.href = data.result;
                    }
                }
            };
        Brandco.api.callAjaxWithParam(param);
    });

    $('.jsCategoryNew').click(function(){
        $('.liNewCategory').remove();
        var html = '<li class="liNewCategory" style="display: none"><p class="categoryNew" id="category2_0">';
            html += '<input type="text" name="name" placeholder="カテゴリ名">';
            html += '<input type="text" name="directory" placeholder="ディレクトリ名">';
            html += '<span class="btn3"><a href="javascript:void(0)" class="small1 submitCategory">追加する</a></span>';
            html += '<span class="categoryAction"><a href="javascript:void(0)" class="iconBtnDelete jsCategoryCancel">キャンセル</a></span>';
            html += '<!-- /.categoryNew --></p></li>';
        $(html).prependTo($('.categoriesTree'));
        $('.liNewCategory').show(300);
        StaticHtmlCategoriesService.initCategoryAction();

    });

    StaticHtmlCategoriesService.initCategoryAction();
    StaticHtmlCategoriesService.reloadDirectory($('.categoriesTree'));
});
