$(function () {
  if (mw.config.get("wgAction") !== "view") {
    return;
  }
  console.log("alert init");
  var spans = $(".alert-span");
  var messageDialog = new OO.ui.MessageDialog();
  var windowManager = new OO.ui.WindowManager();
  windowManager.addWindows([messageDialog]);
  $("body").append(windowManager.$element);
  spans.each(function (index, obj) {
    var title = obj.getAttribute("data-alert-title");
    if (obj.getAttribute("data-alert-ok")) {
      var ok = obj.getAttribute("data-alert-ok");
    } else {
      var ok = mw.message("ok").text();
    }
    var content = obj.innerHTML;
    alert(JSON.stringify({ index, title, ok, content }));
    windowManager.openWindow(messageDialog, {
      title: title,
      message: content,
      size: "large",
      actions: [
        {
          action: "ok",
          flags: "primary",
          label: ok,
        },
      ],
    });
  });
});
