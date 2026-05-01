(function () {
    function sendImpression(adId) {
        if (!adId || !window.adcoreFrontend) {
            return;
        }

        fetch(adcoreFrontend.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'adcore_record_impression',
                nonce: adcoreFrontend.nonce,
                ad_id: adId,
            }),
        });
    }

    function initAdcoreImpressions() {
        var ads = document.querySelectorAll('.adcore-ad[data-adcore-ad-id]');

        if (!ads.length) {
            return;
        }

        var seenAds = new Set();

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                var ad = entry.target;
                var adId = ad.getAttribute('data-adcore-ad-id');

                if (seenAds.has(adId)) {
                    return;
                }

                seenAds.add(adId);
                sendImpression(adId);
                observer.unobserve(ad);
            });
        }, {
            threshold: 0.5,
        });

        ads.forEach(function (ad) {
            observer.observe(ad);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdcoreImpressions);
    } else {
        initAdcoreImpressions();
    }
})();