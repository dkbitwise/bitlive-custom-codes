/**
 * Public js
 */
(function ($) {
    'use strict';
    $(document).ready(function () {
        $('#fep-menu-newmessage').on('click', function () {
            let url = $(this).attr('href');
            if('-1' !==url.search('newmessage')){
               $('.bwlive_overlay').removeClass('bwlive-bit-hide');
            }
        });

        $('.bw-close').on('click',function (){
            $('.bwlive_overlay').addClass('bwlive-bit-hide');
        });
    });
})(jQuery);