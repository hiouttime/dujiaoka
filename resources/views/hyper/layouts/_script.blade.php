<script src="/assets/hyper/js/jquery-3.4.1.min.js"></script>
<script src="/assets/hyper/js/vendor.min.js"></script>
<script src="/assets/hyper/js/app.min.js"></script>
<script src="/assets/hyper/js/hyper.js?v=215115"></script>
@if(dujiaoka_config_get('is_open_google_translate') == \App\Models\BaseModel::STATUS_OPEN)
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'zh',
            layout: google.translate.TranslateElement.FloatPosition.TOP_LEFT
        }, 'google_translate_element');
    }

    function triggerHtmlEvent(element, eventName) {
        var event;
        if (document.createEvent) {
            event = document.createEvent('HTMLEvents');
            event.initEvent(eventName, true, true);
            element.dispatchEvent(event);
        } else {
            event = document.createEventObject();
            event.eventType = eventName;
            element.fireEvent('on' + event.eventType, event);
        }
    }

    $('.lang-select').click(function () {
        var theLang = $(this).attr('data-lang');
        if (theLang = 'zh') {
            document.cookie = "googtrans=zh-CN;path=/;";
        }
        $('.goog-te-combo').val(theLang);
        window.location = $(this).attr('lang');
        location.reload();

    });
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>
@endif