jQuery(document).ready(function($) {
    let st = Date.now().toString().substring(-1, 9);
    let validating = false;
    let idsToDelete = [];

    $(".in_dash form button.save").click(function (e) {
        e.preventDefault();

        var serialized = jQuery(".in_dash form").serialize();
        var data = {
            'action': 'in_create',
            'st': st,
            serialized      // We pass php values differently!
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        if(!validating) {
            let empty = false;
            jQuery("input[required], textarea[required]").each(function (ss) {
                if (jQuery(this).val() === ""){
                    empty = true;
                }
            });
            if(jQuery(".opening_page select[name='author']").val() === "0"){
                empty = true;
            }
            if(!empty){
                validating = true;
                $('.temp_save').trigger('change');
                jQuery.post(ajax_object.ajax_url, data, function (response) {
                    window.location = window.location.href.split("?")[0] + "?page=Intellecto";
                    validating = false;
                });
            }else{
                alert('Please fill page content fields!');
            }
        }else{
            jQuery('.in_dash .text-danger').show();
            jQuery('.in_dash .text-danger').hide(6000);
        }
    });
    $(".temp_save").click(function (e) {
        elementClicked = false;
        if ($('button.save').is(':hover')) {
            elementClicked = true;
        }
        if( elementClicked === false ) {
            updateAjax(st, idsToDelete);
        }
    });
    $(".in_dash form").on("validate_img", function(){

        var elm = jQuery(".in_dash form input[value='true']");
        if(elm){
            var id = elm.closest('div').find('input[name="image[]"]').val();

            var data = {
                'action': 'in_validate_img',
                'st': st,
                'id': id
            };
            jQuery.post(ajax_object.ajax_url, data, function (response) {
                if(response === ""){
                    elm.closest('div').find('input[name="image[]"]').val("");
                    elm.closest('div').find('img').removeAttr("src");
                }
                // elm.closest('div').find('input').eq(0).trigger('focusout');

            });
        }
    });
    $(".in_dash form").on("validate_imgTop", function(){

        var elm = jQuery(".in_dash form input[name='post_edited']");
        if(elm){
            var id = elm.closest('div').find('input[name="wp_image_id"]').val();
            var data = {
                'action': 'in_validate_img',
                'st': st,
                'id': id
            };
            jQuery.post(ajax_object.ajax_url, data, function (response) {
                if(response === ""){
                    elm.closest('div').find('input[name="wp_image_id"]').val("");
                    elm.closest('div').find('img').removeAttr("src");
                }
                // elm.closest('div').find('input').eq(0).trigger('focusout');
                let empty = false;
                jQuery("input[required], textarea[required]").each(function (ss) {
                    if (jQuery(this).val() === "") {
                        empty = true;
                    }
                });
                if(empty){
                    jQuery('.holder').hide();
                }else{
                    jQuery('.holder').show();
                }
            });
        }
    });
    $(".in_dash form").on("click", "button.del_record", function (d) {
        $('.opening_page select').trigger('change');
        jQuery(this).closest('div').remove();
        let id = jQuery(this).closest('div').find('[name="temp_id[]"]').val();
        if(id !== ''){
            idsToDelete.push(id);
        }
        console.log(idsToDelete);
    });
    $(".in_dash form button.cancel").click(function ($) {
        if (confirm('Are you sure ?')) {
            let save_id = jQuery(this).closest('form').find('[name="save_id"]').val();
            var data = {
                'action': 'in_cancel',
                'post_id': save_id,
                'st': st
            }
            jQuery.post(ajax_object.ajax_url, data, function(response) {
                window.location = window.location.href.split("?")[0] + "?page=Intellecto";
            });
        }else{
            console.log('cancel')
        }
    });
});
function ajaxResponser(response){
    const responser = response.split(";")
    responser.splice(-1);
    jQuery("input[name='temp_id[]']").each(function (index) {
        let element = jQuery(this);
        element.val(responser[index]);
        element.closest('div').find('input[name="edited[]"]').val('');
        element.removeClass("empty");
    });
    let elm = $('.in_dash input[name="post_edited"]');
    elm.val();
    elm.trigger('change');
    $('.holder').each(function () {
        let id = $(this).find('input[name="temp_id[]"]').val();
        let title = $(this).find('input[name="title[]"]').val();
        if (typeof id === '') {
            id = '';
        }
        if (title === '') {
            title = 'New page default input';
        } else {
            title = ' - ' + title;
        }
        $(this).find('h5.title').html(title);
        let imageInput = $(this).find('input[name="image[]"]');
        let imagePrev = $(this).find('img.imgPrev');
        if(imageInput.val() === '') {
            imagePrev.attr('src', 'https://dev1.dailyfeednews.com/wp-content/uploads/2022/08/7.jpg');
            imageInput.val('5132');
        }
    });
}
function updateAjax(timeOfExecution, idsToDelete){
    var serialized = jQuery(".in_dash form").serialize();
    var data = {
        'action': 'in_update',
        'st': timeOfExecution,
        serialized      // We pass php values differently!
    };
    jQuery.post(ajax_object.ajax_url, data, function (response) {
        ajaxResponser(response);
        $('.temp_save').trigger('change');

        var data = {
            'id': idsToDelete,
            'action': 'in_drop',
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function (response) {
        // DELETED PAGES
        });

        return true;
    });
}