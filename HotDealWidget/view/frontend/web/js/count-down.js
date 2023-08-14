define(['uiComponent', 'ko'], function (Component, ko) {
    'use strict';

    const toTimestamp = strDate => Date.parse(strDate) / 1000;
    const pad = n => (n < 10 ? '0' : '') + n;

    return Component.extend({
        defaults: {
            template: 'Ecommage_HotDealWidget/count-down',
            isVisible: ko.observable(true),
            label: ko.observable(""),
            content: ko.observable("")
        },
        initialize: function (config) {
            this._super();

            if (config.dateFrom !== "") {
                const currentTime = Math.floor(Date.now() / 1000);
                const startTime  = toTimestamp(config.dateFrom);
                const endTime  = toTimestamp(config.dateTo);
                if(startTime >= endTime) {
                    this.isVisible(false);
                }
                let time = "";  
                if (currentTime > startTime) {
                    time = endTime;
                    this.label(config.labelOnsale);
                } else {
                    time = startTime;
                    this.label(config.labelsaleStart);
                }
                setInterval(this.getCountdown.bind(this, time),1000);
            }
        },
        getCountdown: function (time) {
            const currentTime = Math.floor(Date.now() / 1000);
            if (time <= currentTime ) {
                this.isVisible(false);
                clearInterval();
            } else {
                const seconds = Math.floor((time - currentTime));
                const minutes = Math.floor(seconds / 60);
                const hours = Math.floor(minutes / 60);
                const days = Math.floor(hours / 24);
                let content = [
                    { name: "hours", value: pad(hours - days * 24) },
                    { name: "minutes", value: pad(minutes - hours * 60) },
                    { name: "seconds", value: pad(seconds - minutes * 60) },
                ];
                if(days){
                    content = [{ name: 'days', value: days }, ...content];
                }
                this.content(content);
            }
        }
    });
}
);