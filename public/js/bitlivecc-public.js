/**
 * Public js
 */
(function ($) {
    'use strict';

    function validateEmail($email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test($email);
    }

    $(document).ready(function () {
        $('#reg_stu_submit').on('click', function () {
            var has_error = false;
            if ('' === $('#first_name1').val()) {
                $('.first_name1-error').css('display', 'block');
                $('#first_name1').focus();
                has_error = true;
            } else {
                $('.first_name1-error').hide();
            }

            if ('' === $('#last_name1').val()) {
                $('.last_name1-error').css('display', 'block');
                $('#last_name1').focus();
                has_error = true;
            } else {
                $('.last_name1-error').hide();
                $('#last_name1').focus();
            }

            if (!$('#user_email1').val()) {
                $('.user_email1-error').css('display', 'block');
                $('#user_email1').focus();
                has_error = true;
            } else if (!validateEmail($('#user_email1').val())) {
                $('.user_email1-error').text('Enter valid Email address');
                $('#user_email1').focus();
                $('.user_email1-error').css('display', 'block');
            }

            if (!$('#timezone').val() || $('#timezone').val() === 'NA') {
                $('.timezone-error').css('display', 'block');
                $('#timezone').focus();
                has_error = true;
            } else {
                $('.timezone-error').hide();
            }

            if (!has_error) {
                $('form#new_studnet_reg').submit();
            }
        });
    });
})(jQuery);