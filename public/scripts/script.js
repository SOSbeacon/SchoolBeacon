function setInputAutoClear() {
    if ($('.textbox-auto-clear').length > 0) {
        var initText = '';
        var inputText = $('.textbox-auto-clear');
        if (initText != '') $(inputText).val(initText);
        var inputValue = inputText.val();
        $(inputText).click(function() {
            $(this).val() == inputValue ? $(this).val('') : false;
        })
        .blur(function() {
            $(this).val() == '' ? $(this).val(inputValue) : false;
        });
        if ($('.submit-auto-clear').length > 0) {
            $('.submit-auto-clear').click(function() {
                $(inputText).val() == inputValue ? $(inputText).val('') : false;
            });
        }
    }
}

function getCurrentTime() {
    var d = new Date();
    return d.getFullYear() + (d.getMonth() + 1).toString() + d.getDate().toString() + ' ' + d.getHours().toString() + ':' + d.getMinutes().toString(); // Month date Year, h:m A
}

$(document).ready(function() {
    setInputAutoClear();
    jQuery.validator.addMethod('phoneUS', function(number, element) {
        number = number.replace(/\s+/g, '');
        return this.optional(element) || number.length > 9 && number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, 'Please specify a valid US phone number');
        
    $('.fancybox').fancybox({fitToView:false});
    $(document).pngFix();
});