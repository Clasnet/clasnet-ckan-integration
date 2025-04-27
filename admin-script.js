jQuery(document).ready(function($)
{
    var customUploader;

    $('#clasnet_slider_image').on('click', function(e)
    {
        e.preventDefault();

        if (customUploader)
        {
            customUploader.open();

            return;
        }

        customUploader = wp.media(
        {
            title: 'Pilih Gambar Slider',
            button:
            {
                text: 'Gunakan Gambar'
            },
            multiple: false,
            library:
            {
                type: 'image'
            }
        });

        customUploader.on('select', function()
        {
            var attachment = customUploader.state().get('selection').first().toJSON();

            $('#clasnet_slider_media_id').val(attachment.id);
            $('#clasnet_slider_image_name').text(attachment.filename);
        });

        customUploader.open();
    });

    $('form').on('submit', function()
    {
        console.log('Form submitted, media ID:', $('#clasnet_slider_media_id').val());
    });
});