var LinkEntriesService = (function(){
    return {
        deleteAreaClick : function(tag){
            var url = tag.getAttribute('data-url'),
                callback = tag.getAttribute('data-callback');
            Brandco.helper.deleteEntry(tag, url, callback);
        },
        prioritizeChange : function(tag){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                data = tag.data('entry'),
                editUrl = tag.data('editurl'),
                priorityUrl = tag.data('prioritizeurl');
            data.csrf_token = csrf_token;
            var delete_data = 'entryId='+data.entryId+'&service_prefix='+data.service_prefix+'&csrf_token='+data.csrf_token;
            if(tag.val() == 'delete'){
                $("#delete_area").attr({"data-entry" : delete_data});
               Brandco.unit.openModal('.modal2');
            }else if(tag.val() == "fixed"){
                var li = document.getElementById('li'+data.entryId);
                if(li.className == 'contFixed'){
                    li.className = '';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示</option><option value="edit">編集</option><option value="delete">削除</option>';
                    document.getElementById('switch'+data.entryId).setAttribute("data-priority",'0');
                }else{
                    li.className = 'contFixed';
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">優先表示を解除</option><option value="edit">編集</option><option value="delete">削除</option>';
                    document.getElementById('switch'+data.entryId).setAttribute("data-priority",'1');

                }
                Brandco.api.callAjax(data, priorityUrl, null, null);
            }else{
                if(tag.val() != 'default')
                    window.location = editUrl;
            }
            return false;
        },
        switchClick : function(tag){
            var csrf_token = document.getElementsByName("csrf_token")[0].value,
                hiddenUrl = tag.data('hiddenurl'),
                data = tag.data('entry'),
                priority = Brandco.helper.getPriority(data.entryId),
                tsw = document.getElementById('switch'+data.entryId).className;
            if(priority == 0 || (priority == 1 && tsw == 'switch off')){
                var sw = document.getElementById('switch'+data.entryId);
                text = "優先表示";
                if(priority == 1)
                    text = "優先表示を解除";
                if (sw.className == 'switch off'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="fixed">'+text+'</option><option value="edit">編集</option><option value="delete">削除</option>';
                }else if(sw.className == 'switch on'){
                    document.getElementById('select'+data.entryId).innerHTML = '<option value="default">操作</option><option value="edit">編集</option><option value="delete">削除</option>';
                }
                data.csrf_token = csrf_token;
                Brandco.api.callAjax(data, hiddenUrl, null, null);
            }else{
                if((priority == 1 && tsw == 'switch on'))
                    document.getElementById('switch'+data.entryId).className = 'switch off';
            }
            return false;
        }
    }
})();


$(document).ready(function () {
    $('#delete_area').click(function(){
        LinkEntriesService.deleteAreaClick($(this)[0]);
    });

    $(".prioritize").change(function(){
       LinkEntriesService.prioritizeChange($(this));
    });

    $(".switch").click(function(){
       LinkEntriesService.switchClick($(this));
    });
});