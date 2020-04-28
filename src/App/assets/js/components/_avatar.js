$(document).ready(function () {
    let fileInputCommonConfig = {
        previewTemplates: {
            image: '<div class="file-preview-frame krajee-default kv-preview-thumb" id="{previewId}" data-fileindex="{fileindex}" data-template="{template}">\n' +
                '   <div class="kv-file-content" style="width: 100%;">' +
                '       <img src="{data}" class="kv-preview-data file-preview-image" title="{caption}" alt="{caption}" ' +
                '           style="height: auto; width: auto; max-width: 100%; max-height: 100%;">\n' +
                '   </div>\n' +
                '</div>\n',
        },
        layoutTemplates: {
            footer: ''
        },
        showRemove: false,
        showUpload: false,
        showCancel: false,
        initialPreviewShowDelete: false,
        dropZoneEnabled: false,
        required: false,
        showClose: false,
        browseClass: 'btn btn-default',
        browseLabel: 'Browse',
        removeClass: 'btn btn-default',
        uploadClass: 'btn btn-default',
        allowedFileTypes: ['image'],
        showUploadedThumbs: false,
        maxFileSize: 10000,
        maxFilePreviewSize: 10000
    };

    let $imageInput = $('.img-input');
    let userUploadUrl = $imageInput.data('url');
    let profilePreview = $imageInput.data('preview');

    fileInputCommonConfig.uploadUrl = userUploadUrl;
    fileInputCommonConfig.defaultPreviewContent = '<div class="file-preview-frame krajee-default kv-preview-thumb">\n' +
        '   <div class="kv-file-content" style="width: 100%;">' +
        '       <img src="'+ profilePreview +'" class="kv-preview-data file-preview-image"' +
        '           style="height: auto; width: auto; max-width: 100%; max-height: 100%;">\n' +
        '   </div>\n' +
        '</div>\n';

    $imageInput.fileinput(fileInputCommonConfig);

    $imageInput.on('fileuploaded', function (event, data, previewId, index) {
        $(this).fileinput('reset');
        $('.file-preview-frame').find('img').attr('src', data.response.imageUrl);
    });

    $imageInput.on('change', function () {
        $('.img-input').fileinput('upload');

    });
});
