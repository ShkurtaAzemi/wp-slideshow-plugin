// media library script
(function ($) {
    $(document).ready(function () {

        function refreshExistingIds(){
            let existingImageIds = [];
            $('.slideshow-container .item').each(function () {
                // let elementwithoutdisplaynone = $(this).not([style*="display: none"]);
                // console.log('elementwithoutdisplaynone',elementwithoutdisplaynone)
                if($(this).is(':visible')){
                    existingImageIds.push($(this).data('id'))
                }

            })

            return existingImageIds;
        }
        // wp.media.frames.file_frame.on('all', function(e) { console.log(e); });
        $(document).on('click', '.upload_image_button', function (e) {
            e.preventDefault();
            var button = $(this);


            let existingImageIds = refreshExistingIds();


            // $('#images').val(existingImageIds)

            console.log('existingIds',existingImageIds);
            var file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Choose Slideshow images',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Select'
                },
                multiple: 'add'
            });
            file_frame.on('open', function () {
                // alert('open')
                var selection = file_frame.state().get('selection');
                var ids_value = jQuery('#images').val();

                if (ids_value.length > 0) {
                    var ids = ids_value.split(',');

                    existingImageIds.forEach(function (id) {
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        selection.add(attachment ? [attachment] : []);
                    });
                }
            });

            file_frame.on('select', function () {
                var img = file_frame.state().get('selection');
                var urls = [];
                var ids = [];
                img.each(function (selection) {
                    // console.log(selection.attributes);
                    urls.push(selection.attributes.url)
                    ids.push(selection.attributes.id)
                    button.siblings('input').val(ids).change();
                    // $('.slideshow-container').append(' <div class="ui-state-default col-md-6 col-lg-4 item" data-id="'+selection.attributes.id+'"><img' +
                    //     '                                        class="img-fluid image scale-on-hover"\n' +
                    //     '                                        src="'+selection.attributes.url+'\n' +
                    //     '                            </div>');
                });

                // existingImageIds = []
                // $('.slideshow-container .item').each(function () {
                //     existingImageIds.push($(this).data('id'))
                // })
                //
                // ids.forEach(function (item) {
                //     if ($.inArray(item, existingImageIds) < 0) {
                //         attachment = wp.media.attachment(item);
                //         attachment.fetch();
                //
                //         // console.log('attachment', attachment)
                //         var attachment = attachment.toJSON();
                //         var image_url = attachment.url;
                //         var image_id = attachment.id;
                //         $('.slideshow-container .ui-sortable').append(`<div class="ui-state-default col-md-6 col-lg-4 item" data-id="${image_id}"><img class="img-fluid image scale-on-hover" src="${image_url}"></div>`);
                //     }
                // })

                console.log('onselectids', ids);
            });

            file_frame.open();

            file_frame.on('close', function () {
                // alert('closed')
                var img = file_frame.state().get('selection');
                var urls = [];
                var ids = [];
                img.each(function (selection) {
                    // console.log(selection.attributes);
                    urls.push(selection.attributes.url)
                    ids.push(selection.attributes.id)
                    button.siblings('input').val(urls).change();
                });

                let existingImageIds = refreshExistingIds();

                existingImageIds.forEach(function (existing_item){
                    if ($.inArray(existing_item,ids) < 0) {
                        $(`.item[data-id='${existing_item}']`).css('display', 'none')
                    }
                })

                ids.forEach(function (item) {
                    if ($.inArray(item, existingImageIds) < 0) {
                        attachment = wp.media.attachment(item);
                        attachment.fetch();

                        // console.log('attachment', attachment)
                        var attachment = attachment.toJSON();
                        var image_url = attachment.url;
                        var image_id = attachment.id;
                        $('.slideshow-container .ui-sortable').append(`<div class="ui-state-default col-md-6 col-lg-4 item" data-id="${image_id}"><img class="img-fluid image scale-on-hover" src="${image_url}"> <span class="remove-image" href="#">&#215;</span></div>`);
                    }
                })
            });


        });
        
        $('.slideshow-block').on('click', '.remove-image', function (){
            // console.log('this', $(this))
            let parentId = $(this).parent().data('id');
            // console.log('parentid', parentId)
            $(`.item[data-id='${parentId}']`).css('display', 'none')
            let existingImageIds = refreshExistingIds();
            $('#images').val(existingImageIds)

        })

        $(function () {
            $("#sortable").sortable({
                update: function (event, ui) {
                    existingImageIds = []
                    $('.slideshow-container .item').each(function () {
                        existingImageIds.push($(this).data('id'))
                    })

                    $('#images').val(existingImageIds)
                }
            });
            $("#sortable").disableSelection();
        });

    });
}(jQuery));