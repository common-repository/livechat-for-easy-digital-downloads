(function ($) {
    $(document).ready(function () {
        var dismissButton = $("#lc-edd-review-notice button");
        dismissButton.hide();
        $("#lc-edd-review-dismiss").click(function (e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_edd_review_dismiss'
                }
            });
            dismissButton.click();
        });
        $("#lc-edd-review-postpone").click(function (e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_edd_review_postpone'
                }
            });
            dismissButton.click();
        });
        $("#lc-edd-review-now").click(function () {
            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: {
                    action: 'lc_edd_review_dismiss'
                }
            });
            dismissButton.click();
        });
    })
})(jQuery);