define(["jquery", "ko", "Magento_Ui/js/modal/modal", "uiComponent"], function (
    $,
    ko,
    modal,
    Component
) {
    return Component.extend({
        defaults: {
            items: [],
            resultData: ko.observable(),
            isShowImage: false,
            options: {
                type: "popup",
                responsive: true,
                innerScroll: true,
                title: false,
                buttons: false,
            },
            templates: {
                iframe: "Ecommage_VideoReview/modal/iframe.html",
                image: "Ecommage_VideoReview/modal/image.html",
            },
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().observe("items");

            this.resultData(undefined);

            return this;
        },
        convertMedia: function (url) {
            const regExpYoutube =
                /.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
            const regExpVimeo =
                /^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/|\/video\/|))?([0-9]+)/;
            const regExpImg =
                /^https?:\/\/.*\/.*\.(png|gif|webp|jpeg|jpg)\??.*$/gim;

            let match = url.match(regExpYoutube);
            if (match) {
                this.isShowImage = false;
                url = `//www.youtube.com/embed/${match[1]}`;
            } else if ((match = url.match(regExpVimeo))) {
                this.isShowImage = false;
                url = `https://player.vimeo.com/video/${match[5]}`;
            } else if ((match = url.match(regExpImg))) {
                this.isShowImage = true;
            }
            return url;
        },
        showPopup: function (parent, item) {
            if (item.is_popup) {
                const url = this.convertMedia(item.url);
                item.url = url ? url : item.url;
                const data = {
                    item: item,
                    template: this.isShowImage
                        ? this.templates.image
                        : this.templates.iframe,
                };
                parent.resultData(data);

                $("#modal-video-review").modal(this.options).modal("openModal");
            } else {
                $('[data-gallery-role="amasty-main-container"]').trigger(
                    "click"
                );
            }
            return false;
        },
       	 /**
         * Get whether component is visible
         *
         * @returns {boolean}
         */
        isVisible: function () {
            return this.items().length > 0;
        },
    });
});
