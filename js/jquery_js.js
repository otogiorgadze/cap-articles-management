jQuery(document).ready(function(){
    let indexNum = 0;
    $('.holder').each(function(){
        indexNum++;
        let id = $(this).find('input[name="temp_id[]"]').val();
        let title = $(this).find('input[name="title[]"]').val();
        if(typeof id === ''){
            id = '';
        }
        if(title === ''){
            title = 'New page default input';
        }else{
            title = title;
        }
        $(this).find('h5.title').html(title);
    });

    let validateExit = false;

    window.onbeforeunload = function(evt) {
        if(evt && validateExit) {
            evt.returnValue = 'message';
        }
    };
    function removePostEdited(){
        let elm = $('.in_dash input[name="post_edited"]');
        elm.val('');
        $('button.temp_save').prop('disabled', true);
        validateExit = false;
    }
    function setPostEdited(){
        let elm = $('.in_dash input[name="post_edited"]');
        elm.val('true');
        elm.trigger('change');
        $('button.temp_save').prop('disabled', false);
        validateExit = true;
    }
    function detect(element){
        setPostEdited();
        return true;
    }
    jQuery('.expand-collapse-icon').click(function() {
        jQuery(this).toggleClass('collapsed');
        if(!$(this).hasClass('collapsed')){
            jQuery('.in_dash form .form_row .holder').addClass('collapsed');
        }else{
            jQuery('.in_dash form .form_row .holder').removeClass('collapsed');
        }
    });
    jQuery("button.add").click(function(){
        if(jQuery(".holder").length) {
            if (jQuery(".holder").eq(-1).find('input[name="title[]"]').val()) {
                duplicateBasePage();
            }
        }else {
            location.reload();
        }
    });
    jQuery("button.template").click(function(){
        jQuery(".templates").toggle();
    });
    jQuery(".temp").click(function(){
        jQuery('.temp').removeClass('active');
        jQuery(this).addClass('active');
    });
    jQuery('.in_dash').on('input', 'input, textarea', function() {
        detect($(this));
    });

    validateFields();

    jQuery('.in_dash .opening_page').on('input', 'input, textarea', function() {
        setPostEdited();
        validateFields();
    });
    jQuery('.temp_save').on('change', function() {
        removePostEdited();
    });
    jQuery('.opening_page select').on('change', function() {
        validateFields();
        setPostEdited();
        // $(this).closest('div').find('input').eq(0).trigger('focusout');
    });
    jQuery('input[name="image[]"]').change(function(){
        detect($(this));
    });
    jQuery('.in_dash').on('click', '.holder .title', function(){
       $(this).closest('.holder').toggleClass('collapsed');
    });
    jQuery('.in_dash .holder.active').addClass('collapsed');
    $('select.choose_id').on('change', function(){
        let id = $(this).find('option:selected').val();
        var url = window.location.href;
        var queries = {};
        $.each(document.location.search.substr(1).split('&'), function(c,q){
            var i = q.split('=');
            queries[i[0].toString()] = unescape(i[1].toString()); // change escaped characters in actual format
        });

        queries['page_id']=id;

        document.location.href="?"+$.param(queries); // it reload page
        //OR
        history.pushState({}, '', "?"+$.param(queries)); // it change url but not reload page
    });
});
function validateFields(){
    let empty = false;
    jQuery("input[required], textarea[required]").each(
        function (ss) {
            if (jQuery(this).val() === ""){
                empty = true;
                $(this).addClass('validateEmpty');
            }else{
                $(this).removeClass('validateEmpty');
            }
        });
    if(jQuery(".opening_page select[name='author']").val() === "0"){
        empty = true;
    }
    if(jQuery('input[name="wp_image_id"]').attr('value') === ""){
        empty = true;
    }
    if(empty){
        jQuery('.holder').hide();
        jQuery('.baseBar .expand-collapse-icon').hide();
    }else{
        jQuery('.holder').show();
        jQuery('.baseBar .expand-collapse-icon').show();
    }
}
function duplicateBasePage(imgSrc = "", imgId = ""){
    let holder = jQuery(".holder").eq(0).clone();
    holder.find('input').val("");
    holder.find('img.imgPrev').attr("src", imgSrc);
    holder.find('input[name="image[]"]').val(imgId);
    holder.find('textarea').val("");
    holder.find('h5.title').html('New page default input');
    jQuery(".form_row").append(holder);
}
function removeFirstEmptyPage(){
    let holder = jQuery(".holder").eq(0);
    let src = holder.find('input.imgPrev').attr('src');
    if(holder.find('input[name="image[]"]').val() === 7){
        alert('d');
        holder.remove();
    }
}