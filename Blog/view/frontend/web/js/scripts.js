require([
    "jquery",
    "Magento_Customer/js/customer-data",
    "mage/translate",
    'Magento_Customer/js/section-config'
], function ($, customerData, $t, sectionConfig) {
    function submitFormEditPost() {
        $(document).on("submit", "#insert_post", function (e) {
            if ($(this).has('input[name="editRecordId"]').length > 0) {
                $.ajax({
                    type: "POST",
                    url: $(this).attr("action"),
                    data: $(this).serializeArray(),
                    showLoader: true,
                    cache: false,
                    success: function (data) {
                        var customerMessages = customerData.get("messages")() || {},
                            messages = customerMessages.messages || [];
                        try {
                            if (data.val_published) {
                                $(this).find(`option[value="${data.val_published}"]`).remove();
                            }
                            if(data.error_message){
                                messages.push({
                                    text: $t(data.error_message),
                                    type: "error",
                                });
                            }else {
                                messages.push({
                                    text: $t(`Post "${data.title}" has been updated successfully.`),
                                    type: "success",
                                });
                            }
                        } catch (e) {
                            messages.push({
                                text: $t(e.message),
                                type: "error",
                            });
                        }
                        customerMessages.messages = messages;
                        setTimeout(function () {
                            customerData.set("messages", customerMessages);
                        }, 600);
                    }.bind(this),
                    error: function (xhr, status, error) {
                        setTimeout(function () {
                            customerData.set("messages", {
                                messages: [{
                                    text: error,
                                    type: "error",
                                }],
                            });
                        }, 600);
                    },
                });
                return false;
            }
        });
    }
    $(document).ready(function () {
        submitFormEditPost();
    });
});
