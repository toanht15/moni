if($('#text_entry_title')[0]) {
    $("#limitEntryTitle").html(("（")+($('#text_entry_title')[0].value.length)+("文字/80文字）"));
}

$('#text_entry_title').on('input', function(){
    $("#limitEntryTitle").html(("（")+($('#text_entry_title')[0].value.length)+("文字/80文字）"));
});
