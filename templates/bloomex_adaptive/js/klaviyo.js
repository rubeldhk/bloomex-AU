var KlaviyoTracker = function () {
    "use strict";
    return {
        addCustomerEmail: (email) => {
            document.cookie = "klaviyoEmail="+email+"; expires=30;  path=/; secure";
        },
        getCustomerEmail: () => {
            return  getCookie('klaviyoEmail')
        },
        track: (eventName, eventInfo = {}) => {
            const email = KlaviyoTracker.getCustomerEmail();
            if(!email){
                return false;
            }

            klaviyo.push(['identify', { 'email': email }]);
            klaviyo.track( eventName, eventInfo);
            klaviyo.sendCachedEvents();
        }
    }
}()