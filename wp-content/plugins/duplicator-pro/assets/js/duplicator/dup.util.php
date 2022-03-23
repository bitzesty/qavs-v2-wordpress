<?php
/* ============================================================================
*  UTIL NAMESPACE: All methods at the top of the Duplicator Namespace
*  =========================================================================== */
defined("ABSPATH") or die("");
use Duplicator\Libs\Snap\SnapJson;

?>

<script>
Duplicator.Util.ajaxProgress = null;

Duplicator.Util.ajaxProgressShow = function () {
    if (Duplicator.Util.ajaxProgress === null) {
        Duplicator.Util.ajaxProgress = jQuery('#dup-pro-ajax-loader')
    }
    Duplicator.Util.ajaxProgress.stop().css('display', 'block').animate({
        opacity: 1
    }, 200);
}

Duplicator.Util.ajaxProgressHide = function () {
    if (Duplicator.Util.ajaxProgress !== null) {
        Duplicator.Util.ajaxProgress.stop().css({
            'display': 'none',
            'opacity': 0
        });
    }
}

Duplicator.Util.ajaxWrapper = function (ajaxData, callbackSuccess, callbackFail, loadProgress = false) {
    
    if (loadProgress) {
        Duplicator.Util.ajaxProgressShow();
    }
    
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        dataType: "json",
        data: ajaxData,
        success: function (result, textStatus, jqXHR) {
            var message = '';
            if (result.success) {
                if (typeof callbackSuccess === "function") {
                    try {
                        message = callbackSuccess(result.data.funcData, result.data, textStatus, jqXHR);
                    } catch (error) {
                        console.error(error);
                        DupPro.addAdminMessage(error.message, 'error');
                        message = '';
                    }
                } else {
                    message = '<?php DUP_PRO_U::_e('RESPONSE SUCCESS'); ?>';
                }

                if (String(message).length) {
                    DupPro.addAdminMessage(message, 'notice');
                }
            } else {
                if (typeof callbackFail === "function") {
                    try {
                        message = callbackFail(result.data.funcData, result.data, textStatus, jqXHR);
                    } catch (error) {
                        console.error(error);
                        message = error.message;
                    }
                } else {
                    message = '<?php DUP_PRO_U::_e('RESPONSE ERROR!'); ?>' + '<br><br>' + result.data.message;
                }
                if (String(message).length) {
                    DupPro.addAdminMessage(message, 'error');
                }
            }
        },
        error: function (result) {
            DupPro.addAdminMessage(<?php echo SnapJson::jsonEncode(DUP_PRO_U::__('AJAX ERROR!') . '<br>' . DUP_PRO_U::__('Ajax request error')); ?>, 'error');
        },
        complete: function () {
            Duplicator.Util.ajaxProgressHide();
        }
    });
};

Duplicator.Util.humanFileSize = function(size) {
    var i = Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
};

Duplicator.Util.isEmpty = function (val) {
    return (val === undefined || val == null || val.length <= 0) ? true : false;
};


</script>