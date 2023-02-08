$('.select').hide();
jQuery(document).ready(function($) {
    let readyToSave = false;
    let currentId;
    $('.select').show();
    let block = $('.heading .dom_elm').clone().removeClass('inactive');
    let blockb = $('.heading_bottom .dom_elm').clone().removeClass('inactive');
    let btn, btnb, turnb = false, turn = false, parsed, str, post_var = [], input_val = [], head_script = [], footer_script = [], conf, save = false;
    let cloned = $('.post_variation:first').clone([false, false]);
    let pages;

    cloned.find('.opening_page').removeClass('d-none');
    cloned.find('.heading').removeClass('inactive');
    cloned.find('.heading_bottom').removeClass('inactive');
    cloned.removeClass('d-none');

    let validateExit = false;

    window.onbeforeunload = function(evt) {
        if(evt && validateExit) {
            evt.returnValue = 'message';
        }
    };

        $('form.variations').on('DOMSubtreeModified', '.post_variation', function(){
                if ($(".saveVariation[disabled]").length > 0){
                    validateExit = false;
                }else{
                    validateExit = true;
                }
        });


    $('form.variations').on('DOMSubtreeModified', '.post_variation .heading_content', function(){
        $(this).closest('.post_variation').find('button.saveVariation').prop('disabled', false);
    });
    $('form.variations').on('input, change', '.post_variation select', function() {
        $(this).closest('.post_variation').find('button.saveVariation').prop('disabled', false);
    });
    $('form.variations').on('keyup', '.post_variation input', function() {
        $(this).closest('.post_variation').find('button.saveVariation').prop('disabled', false);
    });
    $('form.variations').on('click', '.remove_head, .remove', function(){
        let last = false;
        if($(this).closest('.block_input').is(':last-child')){
            last = true;
        }
        let empty = 0;
        $(this).closest('.dom_elm').find('input[name="input_value[]"]').each(function(){
            if($(this).val() === ''){
                empty++;
            }
        });
        if(empty > 1){
            last = false;
        }
        if(length > 1 && $(this).closest('.block_input').find('input').val() !== '' || !last){
            $(this).closest('.post_variation').find('button.saveVariation').prop('disabled', false);
        }
    });

    function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    }

    setTimeout(function(){
        let varId = getUrlParameter('var');
        let headVar = getUrlParameter('headVar');
        $('.select select option[value="'+varId+'"]').prop('selected', true);
        $('div.select select').change();
    }, 1000);

    $(document).on('change', '.dom_elm select', function() {
        let val = $(this).find('option:selected').val();
        if(val === "1") {
            let input = $(this).closest('.block_input').find('input').val();
            if (!$.isNumeric(input)) {
                $(this).find('option[value="0"]').prop('selected', true);
            }
        }
    });
    $(document).on('input', '.dom_elm input', function() {
        let select = $(this).closest('.block_input').find('select');
        let val = select.find('option:selected').val();
        if(val === "1") {
            let input = $(this).val();
            if (!$.isNumeric(input)) {
                select.find('option[value="0"]').prop('selected', true);
            }
        }

    });
    function add_pages(pages){
        $.each(pages, function(val){
            alert(val);
            $('.post_variation').eq(index).find('select[name="post_content"] option[value="'+val+'"]').prop('selected', true);
            $('.post_variation').eq(index).find('.add_selection').click();

        });
    }
    $(document).on('click', '.saveVariation', function(){
        if(!save) {
            save = true;
            post_var = [];
            input_val = [];
            head_script = [];
            footer_script = [];

            let variation = $(this).closest('.post_variation');
            let array;
            let head_scriptEach = [];
            variation.find('.heading .dom_elm').each(function () {
                array = $(this).find('input, select').serialize()
                let head = $(this).find('[name="head_script[]"]').val();
                let h_array = [];
                $(this).find('.block_input').each(function () {
                    let input = $(this).find('input').val();
                    let select = $(this).find('select').val();
                    h_array.push({input, select})
                });
                h_array = {head, h_array};
                head_scriptEach.push(h_array);

            });
            let footer_scriptEach = [];
            variation.find('.heading_bottom .dom_elm').each(function () {
                array = $(this).find('input, select').serialize()
                let head = $(this).find('[name="footer_script[]"]').val();
                let h_array = [];
                $(this).find('.block_input').each(function () {
                    let input = $(this).find('input').val();
                    let select = $(this).find('select').val();
                    h_array.push({input, select})
                });
                h_array = {head, h_array};
                footer_scriptEach.push(h_array);

            });

            variation.find('.heading_content .box').each(function () {
                array += '&page_id[]=' + $(this).attr('value');
            });
            array += '&var_id='+variation.find('input[name="var_id"]').attr('value');
            array += '&in_template_body_page='+variation.find('select[name="in_template_body_page"] option:selected').val();
            array += '&post_title='+variation.find('input[name="post_title"]').val();
            array += '&image='+variation.find('input[name="image[]"]').val();
            array += '&pre_image_ad_template_id='+variation.find('select[name="pre_image_ad_template_id"] option:selected').val();
            array += '&page_end_ad_template_id='+variation.find('select[name="page_end_ad_template_id"] option:selected').val();
            array += '&pre_image_ad_template_id_mobile='+variation.find('select[name="pre_image_ad_template_id_mobile"] option:selected').val();
            array += '&page_end_ad_template_id_mobile='+variation.find('select[name="page_end_ad_template_id_mobile"] option:selected').val();
            array += '&name='+variation.find('input[name="name"]').val();
            post_var.push(array);
            head_script.push(head_scriptEach);
            footer_script.push(footer_scriptEach);


            var data = {
                'action': 'in_save_variation',
                'post_id': $('div.select select').children("option:selected").val(),
                'data': post_var,
                'head_script': head_script,
                'footer_script': footer_script,
            };
            jQuery.post(ajax_object.ajax_url, data, function (response) {

                variation.find('input[name="var_id"]').val(response);
                variation.find('h4 span.id').html('('+response+')');
                variation.find('button.saveVariation').prop('disabled', true);

                save = false;

            });
        }
    });
    function runAdder(sm, pages){
        if(pages !== undefined) {
            pages = pages.replace('undefined', '');
        }
        let run = 0;
        let pagesArray = pages.split(";");
        pagesArray = pagesArray.filter(item => item);
        if (run === 0) {
            let m = 0;
            $.each(pagesArray, function (i, v) {
                run++;
                let page = v.split(',');
                $.each(page, function (index, value) {
                    $('.post_variation').eq(i).find('select[name="post_content"] option[value="' + value + '"]').prop('selected', true);
                    $('.post_variation').eq(i).find('.add_selection').click();
                });
                m++;
            });

            addDnDHandlers();
        }
    }
    function fillBlock(id, image, title){
        return str = '<div class="form-group shadow my-2 dom_elm " draggable="true"><div class="row boxer"><div class="col-9 flex-center"><div class="box" value="' + id + '"><img src="' + image + '" class="smallImg">' + title + '</div></div><div class="col-3"><button class="btn btn-success edit_page" value="' + id + '" type="button">Edit</button><button class="btn btn-danger remove_head" type="button">Remove Page</button></div></div></div>';
    }
    function myPages(parsed, single = false){
        $.each(parsed, function(i, v){
            // alert(parsed);
            let str = fillBlock(v.id, v.image, v.title);

            if(!single) {

                //ADDITION NEEDED HERE!
                //INDEXING NUMBER+ !!

                $('.heading_content .selection select').each(function () {
                    if($(this).find('option[value="'+v.id+'"]').length === 0) {
                        $(this).append('<option style="display: none" value="' + v.id + '" imgSrc="' + v.image + '">' + v.title + '</option>');
                    }
                });
                $('.heading_content .row.selection').each(function () {
                    $(this).before(str)
                });
                if($('.post_variation:last input[name="var_id"]').val() !== ''){
                    $('.post_variation').find('.heading_content .remove_head').click();
                }else{
                    $('.post_variation').not(':last').find('.heading_content .remove_head').click();
                }

            }else{
                $('.heading_content .selection select:last').append('<option style="display: none" value="' + v.id + '" imgSrc="' + v.image + '">' + v.title + '</option>');
                $('.heading_content .row.selection:last').before(str);
            }
        });

        addDnDHandlers();
        return true;
    }

    $(document).on('change', 'div.select select', function(){
        if(validateExit) {
            if (!confirm('Changes you made will not be saved!')) {
                $(this).children("option[value='"+currentId+"']").prop('selected', true);
                return false;
            }
        }


            if ($(this).children("option:selected").val() === "Select Post ID") {
                $('.heading').addClass('inactive');
                $('.heading_bottom').addClass('inactive');
                $('.post_variation').addClass('d-none');
                $('.opening_page').addClass('d-none');
            } else {

                $('.heading_content .dom_elm').remove();
                $('.post_variation').not(':first').remove();
                currentId = $(this).children("option:selected").val();
                var data = {
                    'action': 'return_pages',
                    'id': currentId,
                };
                jQuery.post(ajax_object.ajax_url, data, function (response) {
                    let parsed = JSON.parse(response);
                    $('.heading_content .selection select input').not(':first').remove();
                    setTimeout(function () {
                        runAdder(myPages(parsed), pages);
                        $('.post_variation .saveVariation').prop('disabled', true);
                    }, 2500);
                    //    IMG NEEDS TO BE MANIPULATED HERE!
                    //     GET IMG SRC

                });
                data = {
                    'action': 'saved_variation_configurations',
                    'id': $(this).children("option:selected").val(),
                };

                jQuery.post(ajax_object.ajax_url, data, function (res) {
                    manipulateDom(res);
                });
            }
    });
    function manipulateDom(object, jsonType = true){
        let response = object;

        response = response.slice(0, -1);
        json = JSON.parse(response);
        if(jsonType) {
            $('.post_variation').not(':first').remove();
        }

        let cc = 0;

        $.each(json, function(index, v){

            let clone = cloned.clone([false, false]);
            if(jsonType) {
                clone.find('input[name="var_id"]').val(v.id);
            }
            if(v.name !== null) {
                clone.find('.heading_config input[name="name"]').val(v.name);
            }
            if(v.name !== '') {
                clone.find('h4 span.name').html('- ' + v.name + ' - ');
            }
            if(cc === 0 && jsonType){
                clone.find('h4 span.status').addClass('activeStat');
                clone.find('h4 span.status').removeClass('inactiveStat');
            }else{
                clone.find('h4 span.status').addClass('inactiveStat');
                clone.find('h4 span.status').removeClass('activeStat');
            }
            clone.find('h4 span.id').html('('+v.id+')')

            try {
                conf = JSON.parse(v.config);
            } catch (e) {
                conf = v.config;
            }
            conf = JSON.parse(v.config);


            if(conf !== null) {

                clone.find('.heading_config input[name="post_title"]').val(conf.postTitle);

                clone.find('.heading_config input[name="image[]"]').val(conf.featuredImageId);

                var data;
                if (conf.featuredImageId !== '') {
                    data = {
                        'action': 'image_url',
                        'id': conf.featuredImageId,
                    };
                    jQuery.post(ajax_object.ajax_url, data, function (response) {

                        clone.find('img.imgPrev').attr("src", response);

                    });
                }

                clone.find('.heading_config select[name="in_template_body_page"] option[value="' + conf.body_page_template_id + '"]').prop('selected', true);
                clone.find('.heading_config select[name="pre_image_ad_template_id"] option[value="' + conf.advTemplateIds.pre_image_ad_template_id + '"]').prop('selected', true);
                clone.find('.heading_config select[name="page_end_ad_template_id"] option[value="' + conf.advTemplateIds.page_end_ad_template_id + '"]').prop('selected', true);
                clone.find('.heading_config select[name="pre_image_ad_template_id_mobile"] option[value="' + conf.advTemplateIds.pre_image_ad_template_id_mobile + '"]').prop('selected', true);
                clone.find('.heading_config select[name="page_end_ad_template_id_mobile"] option[value="' + conf.advTemplateIds.page_end_ad_template_id_mobile + '"]').prop('selected', true);


                let count = 0;

                if (conf.head_scripts !== null) {
                    var heading = conf.head_scripts;
                    $.each(heading, function (i, v) {
                        count++;

                        if (v.scriptId !== '') {

                            var head_cloned = clone.find('.heading .dom_elm:first').clone([false, false]);
                            var input_cloned = head_cloned.find('.block_input:first').clone([false, false]);

                            head_cloned.find('select option[value="' + v.scriptId + '"]').prop('selected', true);

                            $.each(v.scriptParams, function (i, v) {

                                let type, value;

                                if(typeof v === 'string') {
                                    type = 0;
                                    value = v;
                                }else{
                                    $.map(v, function (typeV, valueV) {

                                        type = typeV;
                                        value = valueV;
                                    });
                                }

                                input_cloned = head_cloned.find('.block_input:first').clone([false, false]);
                                input_cloned.find('input[name="input_value[]"]').val(value);
                                input_cloned.find('select option[value="' + type + '"]').prop('selected', true);
                                head_cloned.find('.block_input:last ').after(input_cloned);

                            });

                            var input_cloned = head_cloned.find('.block_input:first').clone([false, false]);
                            input_cloned.find('input[name="input_value[]"]').val('');
                            head_cloned.find('.block_input:last ').after(input_cloned);

                            head_cloned.find('.block_input:first').remove();
                            clone.find('.heading .dom_elm:last').after(head_cloned);
                            clone.find('.block_input').removeClass('inactive');
                        }
                    });
                }
                if (count > 0) {
                    clone.find('.heading select').each(function () {
                        if ($(this).val() === "Select head script") {
                            $(this).closest('.dom_elm').hide();
                        }
                    });
                }
                //HEAD SCRIPT END
                //BOTTOM SCRIPT START
                let bottom_count = 0;
                if (conf.footer_scripts !== null) {
                    var bottom = conf.footer_scripts;
                    $.each(bottom, function (i, v) {
                        bottom_count++;
                        if (v.scriptId !== '') {

                            var bottom_cloned = clone.find('.heading_bottom .dom_elm:first').clone([false, false]);
                            var input_cloned = bottom_cloned.find('.block_input:first').clone([false, false]);
                            bottom_cloned.find('select option[value="' + v.scriptId + '"]').prop('selected', true);
                            $.each(v.scriptParams, function (i, v) {

                                let type = 0, value;

                                $.map(v, function (typeV, valueV) {

                                    type = typeV;
                                    value = valueV;

                                });
                                input_cloned = bottom_cloned.find('.block_input:first').clone([false, false]);
                                input_cloned.find('input[name="input_value[]"]').val(value);
                                input_cloned.find('select option[value="' + type + '"]').prop('selected', true);
                                bottom_cloned.find('.block_input:last ').after(input_cloned);

                            });

                            var input_cloned = bottom_cloned.find('.block_input:first').clone([false, false]);
                            input_cloned.find('input[name="input_value[]"]').val('');
                            bottom_cloned.find('.block_input:last ').after(input_cloned);

                            bottom_cloned.find('.block_input:first').remove();
                            clone.find('.heading_bottom .dom_elm:last').after(bottom_cloned);
                            clone.find('.block_input').removeClass('inactive');
                        }
                    });
                }
                if (bottom_count > 0) {
                    clone.find('.heading_bottom select').each(function () {
                        if ($(this).val() === "Select footer script") {
                            $(this).closest('.dom_elm').remove();
                        }
                    });
                }
                clone.find('.heading_content').addClass('collapsed');
                clone.find('.heading').addClass('collapsed');
                clone.find('.heading_bottom').addClass('collapsed');
                clone.find('.heading_config').addClass('collapsed');

                let postId = $('form.variations select[name="post_id"] option:selected').val();

                $('.post_variation:last').after(clone);
                $('a.relevant_post').attr('href', '?page=Intellecto&post_id='+postId);
                if(conf.body_pages !== undefined){
                    pages += conf.body_pages+";";
                }

            }
            cc++;
        });

        if(cc === 0){
            $('.post_variation:last').after(cloned.clone([false, false]));
        }
        if(jsonType) {
            $('.post_variation:first').remove();
        }
        addEditButtonsParams();
        validateHeadScriptOptions();
        set_var();
    }

    $(document).on('change', '.dom_elm select', function(){
        if($(this).children("option:selected").val() === "Select head script" || $(this).children("option:selected").val() === "Select footer script"){
            $(this).closest('.dom_elm').find('.row').addClass('inactive');
        }else{
            $(this).closest('.dom_elm').find('.row').removeClass('inactive');
        }
    });
    let parent_row, length, index, html;
    $('form').on('keyup', 'input[name="name"]', function(){
        $(this).closest('.post_variation').find('h4 span.name').html(" - "+$(this).val()+" - ");
    });
    $('form').on('keyup','.dom_elm input', function(){
        html = $('.dom_elm').find('.block_input').eq(0).clone();
        html.removeClass('inactive');
        html.find('input').val('');
        parent_row = $(this).closest('.block_input');
        if($(this).val() !== ""){
            index = $(this).closest('.block_input').index();
            length = $(this).closest('.dom_elm').find('.block_input').length;

            if(index === length){
                $(this).closest('.dom_elm').append(html);
            }
        }
    });
    // $('form').on('change', '.heading_content', function(){
    //     $(this).closest('.post_variation').find('button.saveVariation').prop('disabled', false);
    // });
    $('form').on('click', '.heading_content .remove_head', function(){
        let attr_id = $(this).closest('.dom_elm').find('.box').attr('value');
        $(this).closest('.heading_content').find('.selection select option[value="'+attr_id+'"]').show();
        $(this).closest('.heading_content').closest('.post_variation').find('button.saveVariation').prop('disabled', false);
    });
    $('form').on('click', '.add_selection', function(){


        $(this).closest('.selection').find('select').children("option:selected").each(function () {

            let selected_id = $(this).val();
            let selected_val = $(this).text();
            let smallImg = $(this).attr("imgSrc");

            if (selected_id !== "" && $('.box[value="'+selected_id+'"]').length === 0) {
                $(this).closest('.selection').find('select').children("option:selected").hide();
                $(this).closest('.selection').find('select option').eq(0).prop('selected', true);

                let str = fillBlock(selected_id, smallImg, selected_val);

                // ADDITION NEEDED HERE!
                // INDEXING NUMBER + !!

                $(this).closest('.heading_content .row.selection').before(str);


            }
            let attr_id = $(this).closest('.dom_elm').find('.box').attr('value');
            $(this).closest('.heading_content').find('.selection select option[value="' + attr_id + '"]').show();
        });
        addDnDHandlers();



    });
    $('form').on('click', '.remove_head', function(){
        if($(this).closest('.heading').find('.dom_elm').length === 1) {
            block = $('.dom_elm').eq(0).clone();
            turn = true;
        }
        $(this).closest('.dom_elm').remove();
    });
    $('form').on('click', '.remove_variation', function(){
        if ($('.post_variation').length > 1) {
            if (confirm('Are you sure ?')) {
                $(this).closest('.post_variation').remove();
                let var_id = $(this).closest('.post_variation').find('input[name="var_id"]').val();
                if(var_id !== ""){
                    var data = {
                        'action': 'delete_variation',
                        'id': var_id,
                    };
                    jQuery.post(ajax_object.ajax_url, data, function (response) {
                        console.log('Deleted');
                    });
                }
            }
        }
    });
    $(document).on('click','.heading .add_block', function(){
        let cloneH = cloned.find('.heading .dom_elm:first').clone([false, false]);
        btn = $(this).clone();
        if(turn){
            turn = false;
        }else{
            block = $('.heading .dom_elm').eq(0).clone();
        }
        if($(this).closest('.heading.inactive').length === 0 && $(this).closest('.heading').find('.block_input.inactive').length === 0) {
            $(this).closest('.heading').append(cloneH);
            $(this).closest('.heading').append(btn);
            $(this).remove();
        }

    });
    $(document).on('click','.heading_bottom .add_block', function(){
        let cloneHB = cloned.find('.heading_bottom .dom_elm:first').clone([false, false]);
        btnb = $(this).clone();
        if(turnb){
            turnb = false;
        }else{
            blockb = $('.heading_bottom .dom_elm').eq(0).clone();
        }
        if($(this).closest('.heading_bottom.inactive').length === 0 && $(this).closest('.heading_bottom').find('.block_input.inactive').length === 0) {
            $(this).closest('.heading_bottom').append(cloneHB);
            $(this).closest('.heading_bottom').append(btnb);
            $(this).remove();
        }
    });
    $('.add_variation').click(function(){
        let clone = cloned.clone([false, false]);
        clone.find('.heading .dom_elm').not(':first').remove();
        clone.find('.heading_bottom .dom_elm').not(':first').remove();
        clone.find('.heading .block_input').not(':first').remove();
        clone.find('.heading .block_input').addClass('inactive');
        clone.find('.heading_bottom .block_input').not(':first').remove();
        clone.find('.heading_bottom .block_input').addClass('inactive');
        clone.find('input').val('');
        clone.find('h4 span').html('');
        clone.find('.heading_content select option').not(':first').remove();
        clone.removeClass('collapsed');

        var data = {
            'action': 'return_pages',
            'id': $('.select select').children("option:selected").val(),
        };
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            // IMG NEEDS TO BE MANIPULATED HERE!
            // GET IMG SRC
            let parsed = JSON.parse(response);
            $('.heading_content .selection select input').not(':first').remove();

            $.each(parsed, function (i, v) {
                str = fillBlock(v.id, v.image, v.title);
                // ADDITION NEEDED HERE!
                // INDEXING NUMBER + !!
                if(clone.find('.heading_content select option[value="'+v.id+'"]').length === 0) {
                    clone.find('.heading_content .selection select').append('<option style="display: none" value="' + v.id + '">' + v.id + ' - ' + v.title + '</option>');
                    clone.find('.heading_content .row.selection').before(str);
                }
            });
            addDnDHandlers();
        });
        $('.intellecto_page_post_variations form .post_variation:last').after(clone);

        addDnDHandlers();
    });
    $(document).on('click','.remove', function() {

        length = $('.block_input').length;
        let last = false;
        if($(this).closest('.block_input').is(':last-child')){
            last = true;
        }
        let empty = 0;
        $(this).closest('.dom_elm').find('input[name="input_value[]"]').each(function(){
            if($(this).val() === ''){
                empty++;
            }
        });
        if(empty > 1){
            last = false;
        }
        if(length > 1 && $(this).closest('.block_input').find('input').val() !== '' || !last){
            $(this).closest('.block_input').remove();
            $(this).closest('.block_input').closest('.heading').closest('.post_variation').find('button.saveVariation').prop('disabled', false);
        }
    });

    $(document).on('click', '.post_variation h4', function(){
        $(this).closest('.post_variation').toggleClass('collapsed')
    });
    $(document).on('click', '.heading_config label[for="exampleFormControlSelect"]', function(){
        $(this).closest('.heading_config').toggleClass('collapsed')
    });
    $(document).on('click', '.heading label[for="exampleFormControlSelect2"]', function(){
        $(this).closest('.heading').toggleClass('collapsed')
    });
    $(document).on('click', '.heading_bottom label[for="exampleFormControlSelect2"]', function(){
        $(this).closest('.heading_bottom').toggleClass('collapsed')
    });
    $(document).on('click', '.heading_content label[for="exampleFormControlSelect2"]', function(){
        $(this).closest('.heading_content').toggleClass('collapsed')
    });
    $(document).on('click', '.edit_page', function(){
        let page_id = $(this).attr('value');
        let variation = $('.select select[name="post_id"] option:selected').val();
        let headVar = $(this).closest('.post_variation').find('input[name="var_id"]').val();
        window.location = window.location.href.split("?")[0] + "?page=Intellecto"+"&page_id="+page_id+"&edit=true"+"&var="+variation;
        // window.location = window.location.href.split("?")[0] + "?page=Intellecto"+"&post_id="+post_id+"&edit=true"+"&var="+variation+"&headVar="+headVar;

    });
    var dragSrcEl = null;
    var cols = [];
    function handleDragStart(e) {
        dragSrcEl = this;
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
    }

    function handleDragEnd(e) {
        [].forEach.call(cols, function (col) {
            col.classList.remove('over');
        });
    }

    function handleDragEnter(e) {
        this.classList.add('over');
    }

    function handleDragLeave(e) {
        this.classList.remove('over');
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }

        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }
        if(this.outerHTML){
            dragSrcEl.outerHTML = this.outerHTML;
        }
        this.outerHTML = e.dataTransfer.getData('text/html');
        addDnDHandlers();
        return true;
    }

    function addDnDHandlers() {
        var cols = $('.heading_content .dom_elm');
        [].forEach.call(cols, function(col) {
            col.addEventListener('dragstart', handleDragStart, false);
            col.addEventListener('dragenter', handleDragEnter, false)
            col.addEventListener('dragover', handleDragOver, false);
            col.addEventListener('dragleave', handleDragLeave, false);
            col.addEventListener('drop', handleDrop, false);
            col.addEventListener('dragend', handleDragEnd, false);
        });
    }
    function set_var(){
        $('.changeUrl').each(function (i, v) {
            let postId = $('.select select option:selected').val();
            let link = $(this).attr('href');
            $(this).attr("href", link + '&var=' + postId);
        });
    }
    let clicked = false;
    let input, template, btnM;
    setTimeout(function(){
        input = $('form.editMode .cont_template:first').clone([false, false]);
        input.find('input:first').attr('name', 'name[x]');
        input.find('input:first').val('');
        input.find('textarea:first').attr('name', 'html[x]');
        input.find('textarea:first').val('');
        input.find('.btn[name="temp"]').val('');
    }, 500);
    $('.add_list').click(function(){
        if(!clicked) {
            let form = $('.editMode');
            let cloned = input.clone([false, false]);
            cloned.remove('btn:last');
            form.find('.cont_template:last').after(cloned);
            clicked = true;
            $(this).remove();
        }
    });
    $('.variations').on('change', 'select[name=in_template_body_page], select[name=pre_image_ad_template_id], select[name=page_end_ad_template_id], select[name=pre_image_ad_template_id_mobile], select[name=page_end_ad_template_id_mobile]', function(){
        let id = $(this).find('option:selected').val();
        let elm = $(this).next('.changeUrl');
        let link = elm.attr('href');
        if(id === '0' || id === 'Select Page Content Template'){
            id = 'all';
        }
        elm.attr("href", link + '&id=' + id);
    });
    $('.variations').on('change', 'select[name="head_script[]"], select[name="footer_script[]"]', function(){
        let id = $(this).find('option:selected').val();
        let elm = $(this).closest('.dom_elm').find('.changeUrl');
        let link = elm.attr('href');
        if(id === '0' || id === 'Select Page Content Template'){
            id = 'all';
        }
        elm.attr("href", link + '&id=' + id);
    });
    function addEditButtonsParams(){
        $('select[name=in_template_body_page], select[name=pre_image_ad_template_id], select[name=page_end_ad_template_id], select[name=pre_image_ad_template_id_mobile], select[name=page_end_ad_template_id_mobile]').each(function(){
            let id = $(this).find('option:selected').val();
            let elm = $(this).next('.changeUrl');
            let link = elm.attr('href');
            if(id === '0' || id === 'Select Page Content Template'){
                id = 'all';
            }
            elm.attr("href", link + '&id=' + id);
        });
        $('select[name="head_script[]"], select[name="footer_script[]"]').each(function(){
            let id = $(this).find('option:selected').val();
            let elm = $(this).closest('.dom_elm').find('.changeUrl');
            let link = elm.attr('href');
            if(id === '0' || id === 'Select head script' || id === 'Select footer script'){
                id = 'all';
            }
            elm.attr("href", link + '&id=' + id);
        });
    }

    $(document).on('change', 'select[name="head_script[]"], select[name="footer_script[]"]', function(){
        validateHeadScriptOptions();
    });
    function validateHeadScriptOptions(){
        $('select[name="head_script[]"], select[name="footer_script[]"]').each(function(){
            let id = $(this).find('option:selected').val();
            if(id !== 'Select head script' || id !== 'Select footer script'){
                if( $(this).find('option:first').val() === 'Select head script' || $(this).find('option:first').val() === 'Select footer script') {
                    $(this).find('option:first').hide();
                }
            }
        });
        $('.heading').each(function(){
            let sm = $(this);
            if (sm.find('.dom_elm').length === 0) {
                let cl = cloned.find('.dom_elm:first').clone([false,false]);
                sm.find('label').after(cl);
            }
        });
    }

    $('.editMode').on('change', 'select.choose_id', function(){
        let id = $(this).find('option:selected').val();
        var url = window.location.href;
        url += '&id='+id;
        window.location.href = url;
    });
    $('.editMode').on('change', 'select.script_type', function(){
        let id = $(this).find('option:selected').val();
        var url = window.location.href;
        url += '&script_type='+id;
        window.location.href = url;
    });
    $('.editGroupType').change(function(){
        let type = $(this).find('option:selected').val();
        var url = window.location.href;
        url += '&type='+type;
        window.location.href = url;
    });
    $('.addCol').click(function(){
        let col = $(this).closest('form').find('tr:last').clone([false, false]);
        col.find('input').val('');
        col.find('a').remove();
        $(this).closest('form').find('tr:last').after(col);
    });
    $('button.pop_right_top').click(function (){
        $('.variation_pop_up').toggle();
        runAjax();
    });
    $('.close_pop').click(function() {
        $('.variation_pop_up').toggle();
    });
    $(".variation_pop_up").on("click", ".use_template", function (d) {
        $('.variation_pop_up').show();
        let id = $(this).attr('id');
        let data = {
            'action': 'get_variation_templates',
            'id': id
        };
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            manipulateDom(response, false);

            var data = {
                'action': 'return_pages',
                'id': $('div.select select').children("option:selected").val(),
            };
            jQuery.post(ajax_object.ajax_url, data, function (response) {
                let parsed = JSON.parse(response);

                setTimeout(function () {
                    myPages(parsed, true);
                }, 2500);
                //    IMG NEEDS TO BE MANIPULATED HERE!
                //     GET IMG SRC

            });

            $('.variation_pop_up').hide();
        });

        $('.spinner-box').hide();
    });
    function runAjax(){
        // TO ACTIVATE
        var data = {
            'action': 'get_variation_templates'
        };
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            response = response.slice(0, -1);
            let json = JSON.parse(response);

            $('.card .card').remove();
            $('.spinner-box').hide();

            $.each(json, function (i, v) {
                $('.card svg').after('<div class="card mt-3 font-weight-bold">'+v.name +'<div><button type="button" class="btn btn-info use_template" id="'+v.id+'">USE</button><button type="button" class="btn btn-light delete_template" id="'+v.id+'">DELETE</button></div></div>');
            });
        });
    }
    $("form").on('click', '.save_variation_template', function(){
        let elm = $(this).closest('.post_variation').find('input[name="name"]').val().length;
        let save_id = $(this).closest('.post_variation').find('input[name="var_id"]').val().length;
        if(elm === 0 || save_id === 0){
            alert('Please fill the post name!');
            return false;
        }else {

            let id = $(this).closest('.post_variation').find('input[name="var_id"]').val();
            let name = prompt("Template name");

            if (name != null) {

                var data = {
                    'id': id,
                    'name': name,
                    'action': 'save_variation_template'
                };
                jQuery.post(ajax_object.ajax_url, data, function (response) {
                    // VARIATION TEMPLATE HAS BEEN SAVED
                });
            }
        }
    });
    $(".variation_pop_up").on('click', '.delete_template', function(){

        let id = $(this).attr('id');
        $(this).closest('.card').hide();

        var data = {
            'id': id,
            'action': 'drop_variation_template'
        };
        jQuery.post(ajax_object.ajax_url, data, function (response) {
            // VARIATION TEMPLATE HAS BEEN SAVED
        });
    });
});