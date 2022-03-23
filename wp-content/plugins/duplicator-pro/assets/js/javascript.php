<?php
defined("ABSPATH") or die("");
?>
<script>
/* ============================================================================
* DESCRIPTION: Methods and Objects in this file are global and common in nature
* use this file to place all shared methods and varibles
* UNIQUE NAMESPACE */
DupPro = {};
DupPro.Pack = {};
DupPro.Schedule = {};
DupPro.Settings = {};
DupPro.Storage = {};
DupPro.Storage.Dropbox = {};
DupPro.Storage.OneDrive = {};
DupPro.Storage.FTP = {};
DupPro.Storage.SFTP = {};
DupPro.Storage.GDrive = {};
DupPro.Storage.S3 = {};
DupPro.Support = {};
DupPro.Template = {};
DupPro.Tools = {};
DupPro.UI = {};

//New Format
Duplicator = {};
Duplicator.Util = {};
Duplicator.Debug = {};
Duplicator.Storage = {};

(function ($) {

    /* ============================================================================
     *  BASE NAMESPACE: All methods at the top of the Duplicator Namespace
     * ============================================================================ */

    DupPro._WordPressInitDateTime = '<?php echo current_time("D M d Y H:i:s O") ?>';
    DupPro._WordPressInitTime = '<?php echo current_time("H:i:s") ?>';
    DupPro._ServerInitDateTime = '<?php echo date("D M d Y H:i:s O") ?>';
    DupPro._ClientInitDateTime = new Date();

    DupPro.parseJSON = function (mixData) {
        try {
            var parsed = JSON.parse(mixData);
            return parsed;
        } catch (e) {
            console.log("JSON parse failed - 1");
            console.log(mixData);
        }

        if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
            if (mixData.indexOf('{') < mixData.indexOf('[')) {
                var startBracket = '{';
                var endBracket = '}';
            } else {
                var startBracket = '[';
                var endBracket = ']';
            }
        } else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
            var startBracket = '[';
            var endBracket = ']';
        } else {
            var startBracket = '{';
            var endBracket = '}';
        }

        var jsonStartPos = mixData.indexOf(startBracket);
        var jsonLastPos = mixData.lastIndexOf(endBracket);
        if (jsonStartPos > -1 && jsonLastPos > -1) {
            var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
            try {
                var parsed = JSON.parse(expectedJsonStr);
                return parsed;
            } catch (e) {
                console.log("JSON parse failed - 2");
                console.log(mixData);
                throw e;
                // errorCallback(xHr, textstatus, 'extract');
                return false;
            }
        }
        // errorCallback(xHr, textstatus, 'extract');
        throw "could not parse the JSON";
        return false;
    }

    /**
     *
     * @param string message // html message conent
     * @param string errLevel // notice warning error
     * @param function updateCallback // called after message content is updated
     * 
     * @returns void
     */
    DupPro.addAdminMessage = function (message, errLevel, options) {
        let settings = $.extend({}, {
            'isDismissible': true,
            'hideDelay': 0, // 0 no hide or millisec
            'updateCallback': false
        }, options);

        var classErrLevel = 'notice';
        switch (errLevel) {
            case 'error':
                classErrLevel = 'error';
                break;
            case 'warning':
                classErrLevel = 'update-nag';
                break;
            case 'notice':
            default:
                classErrLevel = 'updated';
                break;
        }

        var noticeCLasses = 'notice ' + classErrLevel + ' no_display';
        if (settings.isDismissible) {
            noticeCLasses += ' is-dismissible';
        }

        var msgNode = $('<div class="' + noticeCLasses + '">' +
                '<div class="margin-top-1 margin-bottom-1 msg-content">' + message + '</div>' +
                '</div>');
        var dismissButton = $('<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
                '</button>');

        var anchor = $("#wpcontent");
        if (anchor.find('.wrap').length) {
            anchor = anchor.find('.wrap').first();
        }

        if (anchor.find('h1').length) {
            anchor = anchor.find('h1').first();
            msgNode.insertAfter(anchor);
        } else {
            msgNode.prependTo(anchor);
        }

        if (settings.isDismissible) {
            dismissButton.appendTo(msgNode).click(function () {
                dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                    $(this).remove();
                });
            });
        }

        if (typeof settings.updateCallback === "function") {
            settings.updateCallback(msgNode);
        }

        $("body, html").animate({scrollTop: 0}, 500);
        $(msgNode).css('display', 'none').removeClass("no_display").fadeIn("slow", function () {
            if (settings.hideDelay > 0) {
                setTimeout(function () {
                    dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                        $(this).remove();
                    });
                }, settings.hideDelay);
            }
        });
    };

    /**
     * 
     * @param string filename
     * @param string content
     * @param string mimeType // text/html, text/plain
     * @returns {undefined}
     */
    DupPro.downloadContentAsfile = function (filename, content, mimeType) {
        mimeType = (typeof mimeType !== 'undefined') ? mimeType : 'text/plain';
        var element = document.createElement('a');
        element.setAttribute('href', 'data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', filename);

        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    }


    DupPro.openWindow = function () {
        $("[data-dup-open-window]").each(function () {
            let url = $(this).data('dup-open-window');
            let name = $(this).data('dup-window-name');

            $(this).click(function () {
                window.open(url, name);
            });
        });        
    }

})(jQuery);
</script>

<?php
    //require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/duplicator/dup.debug.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/duplicator/dup.storage.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/duplicator/dup.ui.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/duplicator/dup.util.php');
?>

<script>
//Init
jQuery(document).ready(function ($)
{
    DupPro.openWindow();
    
    DupPro.UI.tippyInitialized = false;
    DupPro.UI.loadTippy = function ()
    {
        if (DupPro.UI.tippyInitialized) {
            return;
        }

        tippy('[data-tooltip]',{
            content: function(ref) {
                var header = ref.dataset.tooltipTitle;
                var body   = ref.dataset.tooltip;
                var res    = header !== undefined ? '<h3>'+header+'</h3>' : '';

                res += '<div class="dup-tippy-content">'+body+'</div>';
                return res;
            },
            allowHTML: true,
            interactive: true,
            placement: 'bottom-start',
            theme: 'duplicator',
            zIndex: 900000,
            appendTo: document.body
        });

        $('[data-dup-pro-copy-value]').each(function () {

            var element = $(this);
            if (element.hasClass('disabled')) {
                return;
            }

            var tippyElement = tippy(this,{
                allowHTML: true,
                placement: 'bottom-start',
                theme: 'duplicator',
                zIndex: 900000,
                hideOnClick: false,
                trigger:'manual'
            });

            var copyTitle = element.is('[data-dup-pro-copy-title]') ? element.data('dup-pro-copy-title') : '<?php echo DUP_PRO_U::__('Copy to clipboard');?>';
            tippyElement.setContent( '<div class="dup-tippy-content">'+copyTitle+'</div>');

            //Have to set manually otherwise might hide on click.
            element.mouseover(function() {
                tippyElement.show();
            }).mouseout(function() {
                tippyElement.hide();
            });

            element.click(function () {
                var valueToCopy = element.data('dup-pro-copy-value');
                var copiedTitle = element.is('[data-dup-pro-copied-title]') ? element.data('dup-pro-copied-title') : valueToCopy + ' copied to clipboard';
                var message = '<?php echo DUP_PRO_U::__('Unable to copy');?>';
                var tmpArea = jQuery("<textarea></textarea>").css({
                    position: 'absolute',
                    top: '-10000px'
                }).text(valueToCopy).appendTo("body");
                tmpArea.select();

                try {
                    message = document.execCommand('copy') ? copiedTitle : 'Unable to copy';
                } catch (err) {
                    console.log(err);
                }

                tippyElement.setContent( '<div class="dup-tippy-content">'+message+'</div>');
                tippyElement.setProps({theme: 'duplicator-filled'});

                setTimeout(function () {
                    tippyElement.setContent( '<div class="dup-tippy-content">'+copyTitle+'</div>');
                    tippyElement.setProps({theme: 'duplicator'});
                }, 2000);
            });
        });

        DupPro.UI.tippyInitialized = true;
    };

    DupPro.UI.unloadTippy = function(){
        var tooltips = document.querySelectorAll('[data-tooltip], [data-dup-pro-copy-value]');
        tooltips.forEach(function (element) {
            if (element._tippy) {
                element._tippy.destroy();
            }
        });
        DupPro.UI.tippyInitialized = false;
    };

    DupPro.UI.reloadTippy = function(){
        DupPro.UI.unloadTippy();
        DupPro.UI.loadTippy();
    };

    //INIT: DupPro Tabs
    $("div[data-dpro-tabs='true']").each(function ()
    {
        //Load Tab Setup
        var $root = $(this);
        var $lblRoot = $root.find('ul:first-child')
        var $lblKids = $lblRoot.children('li');
        var $lblKidsA = $lblRoot.children('li a');
        var $pnls = $root.children('div');

        //Apply Styles
        $root.addClass('categorydiv');
        $lblRoot.addClass('category-tabs');
        $pnls.addClass('tabs-panel').css('display', 'none');
        $lblKids.eq(0).addClass('tabs').css('font-weight', 'bold');
        $pnls.eq(0).show();

        var _clickEvt = function (evt)
        {
            var $target = $(evt.target);
            if (evt.target.nodeName == 'A') {
                var $target = $(evt.target).parent();
            }
            var $lbls = $target.parent().children('li');
            var $pnls = $target.parent().parent().children('div');
            var index = $target.index();

            $lbls.removeClass('tabs').css('font-weight', 'normal');
            $lbls.eq(index).addClass('tabs').css('font-weight', 'bold');
            $pnls.hide();
            $pnls.eq(index).show();
        }

        //Attach Events
        $lblKids.click(_clickEvt);
        $lblKids.click(_clickEvt);
    });

    //INIT: Toggle MetaBoxes
    $('div.dup-box div.dup-box-title').each(function () {
        var $title = $(this);
        var $panel = $title.parent().find('.dup-box-panel');
        var $arrow = $title.find('.dup-box-arrow');
        $title.click(DupPro.UI.ToggleMetaBox);
        ($panel.is(":visible"))
                ? $arrow.html('<i class="fa fa-caret-up"></i>')
                : $arrow.html('<i class="fa fa-caret-down"></i>');
    });

    DupPro.UI.loadTippy();

    //HANDLEBARS HELPERS
    if (typeof (Handlebars) != "undefined") {

        function _handleBarscheckCondition(v1, operator, v2) {
            switch (operator) {
                case '==':
                    return (v1 == v2);
                case '===':
                    return (v1 === v2);
                case '!==':
                    return (v1 !== v2);
                case '<':
                    return (v1 < v2);
                case '<=':
                    return (v1 <= v2);
                case '>':
                    return (v1 > v2);
                case '>=':
                    return (v1 >= v2);
                case '&&':
                    return (v1 && v2);
                case '||':
                    return (v1 || v2);
                case 'obj||':
                    v1 = typeof (v1) == 'object' ? v1.length : v1;
                    v2 = typeof (v2) == 'object' ? v2.length : v2;
                    return (v1 != 0 || v2 != 0);
                default:
                    return false;
            }
        }

        Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
            return _handleBarscheckCondition(v1, operator, v2)
                    ? options.fn(this)
                    : options.inverse(this);
        });

        Handlebars.registerHelper('if_eq', function (a, b, opts) {
            return (a == b) ? opts.fn(this) : opts.inverse(this);
        });
        Handlebars.registerHelper('if_neq', function (a, b, opts) {
            return (a != b) ? opts.fn(this) : opts.inverse(this);
        });
    }

    //Prevent notice boxes from flashing as its re-positioned in DOM
    $('div.dpro-wpnotice-box').show(300);

    $('.dup-pseudo-checkbox').each(function () {
        let checkbox = $(this);
        checkbox.on('click', function(e) {
            e.stopPropagation();
            checkbox.toggleClass('checked');
        });

        checkbox.closest('label').on('click', function () {
            checkbox.trigger('click');
        });
    });
});
</script>
