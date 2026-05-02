(function () {
    function getSessionKey(adId) {
        return 'adcore_ad_views_' + adId;
    }

    function getSessionViews(adId) {
        var value = sessionStorage.getItem(getSessionKey(adId));
        return value ? parseInt(value, 10) || 0 : 0;
    }

    function incrementSessionViews(adId) {
        var views = getSessionViews(adId);
        sessionStorage.setItem(getSessionKey(adId), String(views + 1));
    }

    function isFrequencyCapped(ad) {
        var adId = ad.getAttribute('data-adcore-ad-id');
        var cap = parseInt(ad.getAttribute('data-adcore-frequency-cap'), 10) || 0;

        if (!cap) {
            return false;
        }

        return getSessionViews(adId) >= cap;
    }

    function hideCappedAds() {
        var ads = document.querySelectorAll('.adcore-ad[data-adcore-ad-id]');

        ads.forEach(function (ad) {
            if (isFrequencyCapped(ad)) {
                ad.style.display = 'none';
            }
        });
    }

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
        hideCappedAds();

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

                if (isFrequencyCapped(ad)) {
                    ad.style.display = 'none';
                    observer.unobserve(ad);
                    return;
                }

                var adId = ad.getAttribute('data-adcore-ad-id');

                if (seenAds.has(adId)) {
                    return;
                }

                seenAds.add(adId);
                incrementSessionViews(adId);
                sendImpression(adId);
                observer.unobserve(ad);
            });
        }, {
            threshold: 0.5,
        });

        ads.forEach(function (ad) {
            if (!isFrequencyCapped(ad)) {
                observer.observe(ad);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdcoreImpressions);
    } else {
        initAdcoreImpressions();
    }
})();