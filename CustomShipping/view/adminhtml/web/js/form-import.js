define([
    'uiElement',
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'uiRegistry'
], function (Element, $, confirm, $t, registry) {
    return Element.extend({
        defaults: {
            processUrl: ''
        },

        initObservable: function () {
            this._super().observe();
            return this;
        },
        onchangeInputFile: function() {
            let file = $('input[name=import_file]')[0].files[0];
            let filename = file.name;
            const url = this.processUrl;
            if (filename && url) {
                if (this.checkTypeXlsx(filename)) {
                    var formData = new FormData();
                    formData.append("import_file",file);
                    formData.append("form_key", window.FORM_KEY);
                    confirm({
                        title: $t('Confirmation Import'),
                        content: $t(`Import a file: ${filename}`),
                        actions: {
                            confirm: function () {
                                $.ajax({
                                    url: url,
                                    data: formData,
                                    enctype: 'multipart/form-data',
                                    type: "POST",
                                    showLoader: true,
                                    processData: false,
                                    contentType: false,
                                    success: function (message) {
                                        if (message.success) {
                                            location.reload();
                                            alert($t(message.success))
                                        } else if (message.error) {
                                            alert($t(message.error))
                                        } else {
                                            alert(message);
                                        }
                                    }.bind(this)
                                });
                            }
                        }
                    });
                }
            }
        },
        checkTypeXlsx: function (filename) {
            if (!/.xlsx$/i.test(filename)) {
                confirm({
                    title: $t('Import failed!!'),
                    content: $t(`Import a file: ${filename}. failed, please choose xlsx`),
                    actions: {
                        confirm: function () {
                            $('input[name=import_file]').click();
                        }
                    }
                });
                return false;
            }
            return true;
        }
    });
});
