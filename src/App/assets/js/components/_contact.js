$(document).ready(function () {
    $('.open-button').on('click', function (e) {
        e.preventDefault();
        let container = $('#contact-form-container');

        $.ajax({
            type: 'GET',
            url: '/contact/form',
        }).done(function (response) {
            container.replaceWith(response);
            document.getElementById("contact-form-yb-frontend").style.display = "block";
        }).fail(function (jqXHR) {
            switch (jqXHR.status) {
                default:
                    window.toastr.error(translateText('Something went wrong! Please try again!'));
                    break;
            }
        });
    });
    $('.cancel').on('click', function () {
        document.getElementById("contact-form-yb-frontend").style.display = "none";
    });

    $('#send-expert-contact-form').on('click', function (e) {
        e.preventDefault();
        let form = $('#contact_form');

        $.ajax({
            type: "POST",
            url: '/contact/save-contact-message',
            data: $(form).serialize()
        }).done(function (response) {
            if (response.message.type === 'success') {
                $('input[name="email"]').val('');
                $('input[name="name"]').val('');
                $('textarea[name="message"]').val('');
                document.getElementById("contact-form-yb-frontend").style.display = "none";
                window.toastr.success(translateText(response.message.text));
            } else {
                window.toastr.error(translateText(response.message.text));
            }
        }).fail(function (jqXHR) {
            switch (jqXHR.status) {
                case 422:
                    let responseText = JSON.parse(translateText(jqXHR.responseText));
                    window.toastr.error(responseText);
                    break;

                case 401:
                    let data = JSON.parse(translateText(jqXHR.responseText));
                    redirectTo(data.redirect);
                    break;

                default:
                    window.toastr.error(translateText("Unexpected error. Please try again!"));
                    break;
            }
        });
    });
});

function validateContactUsForm()
{
    event.preventDefault();
    $('.contactUsFormErrors').hide();
    var submit = true;
    if (!$('.g-recaptcha')[0].dataset.sitekey) {
        submit = false;
        $('#contactUsErrors').show();
        $('#recaptchaSiteKeyEmpty').show();
        return false;
    }
    if ($('#contact_form #name').val() == '') {
        submit = false;
        $('#contactUsErrors').show();
        $('#contactUsEmptyName').show();
    }
    if ($('#contact_form #email').val() == '') {
        submit = false;
        $('#contactUsErrors').show();
        $('#contactUsEmptyEmail').show();
    }
    if ($('#contact_form #message').val() == '') {
        submit = false;
        $('#contactUsErrors').show();
        $('#contactUsEmptyText').show();
    }
    if (submit == true) {
        grecaptcha.execute();
    }
}
window.validateContactUsForm = validateContactUsForm;

function submitContactUsForm()
{
    $('#contact_form').submit();
}
window.submitContactUsForm = submitContactUsForm;
