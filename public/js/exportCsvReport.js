function exportCsv(from, to) {
    $.ajax({
        type: "post",
        url: appRoot + "transactions/export/" + from + "/" + to,
        data: {
            from_date: from,
            to_date: to
        },
        success: function (returnedData) {
            if (returnedData.status === 1) {
                // Open in new window the data from file_name
                window.open(appRoot + "public/exported_files/" + returnedData.file_name, "_blank");
            } else {
                alert("Failed to export report data. Please try again later.");
            }
        },
        error: function () {
            if (!navigator.onLine) {
                changeFlashMsgContent("You appear to be offline. Please reconnect to the internet and try again", "", "red", "");
            } else {
                changeFlashMsgContent("Unable to process your request at this time. Please try again later!", "", "red", "");
            }
        }
    });
}