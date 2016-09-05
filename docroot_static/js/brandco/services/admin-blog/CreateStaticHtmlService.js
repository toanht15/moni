var CreateStaticHtmlService = {
};

function setFloatImage(result) {
    if (result != '') {
        var dom = $('#pagePartsFloatImageSetting .photo img');
        dom.attr('src', result);
    }
}

function setFullImage(result) {
    if (result != '') {
        var dom = $('#pagePartsFullImageSetting .photo img');
        dom.attr('src', result);
    }
}

$(document).ready(function() {
    //最初状態作る
});
