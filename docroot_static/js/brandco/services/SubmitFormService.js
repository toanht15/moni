$(function(){
    function aa_submit() {
        if (!$(this).data('submitted')) {
            this._submit();
        }

        $(this).data('submitted', true);
    }

    // onsubmitイベント対応
    window.addEventListener('submit', aa_submit, true);

    //.submit()対応
    HTMLFormElement.prototype._submit = HTMLFormElement.prototype.submit;
    HTMLFormElement.prototype.submit = aa_submit;
});