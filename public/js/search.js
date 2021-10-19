var timeout = null;
function autoSearch(e) {
  var text = $(e).val().trim();
  if (text.length > 0) {
    var data = new FormData();
    data.set("action", "find_people");
    data.set("table", $("input[name='table']").val());
    data.set("search", text);
    data.set("location", $("input[name='location']").val());
    $(".search_content").html("");
    showWait(".search_content");
    clearTimeout(timeout);
    timeout = setTimeout(function () {
      post(data, $("#wait"), ".search_content");
    }, 1000);
    return;
  }
  $(".search_content").html("");
  $(".old_content").removeClass("display-none");
}
function showWait(waitholder, style = "font-size:30px") {
  $(waitholder).append(
    `<i class='fa fa-spinner fa-spin gifWait text-warning' style=${style}></i>`
  );
}
function stopWait(selector = ".gifWait") {
  $(selector).hide();
  $(".gifWait").remove();
}
function post(
  postData,
  btnToDisable,
  responseHolder,
  page = "controller/familyController.php"
) {
  $.ajax({
    type: "POST",
    data: postData,
    url: page,
    dataType: "json",
    contentType: false,
    cache: false,
    processData: false,
    beforeSend: function () {
      $(btnToDisable).attr("disabled", "disabled");
      showWait(btnToDisable);
    },
    success: function (data) {
      console.log(data);
      // $("#search_diplomat").focus();
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      if (typeof data === "object") {
        if (data.error === "none") {
          if (data.view === "viewSearchResult") {
            var d = data.data;
            if (d.trim().length > 0) {
              $(".old_content").addClass("display-none");
              $(responseHolder).html(data.data);
            } else {
              $(responseHolder).html(
                "<tr ><td colspan='4' class='text-center'><span><b>'" +
                  $("input[name='search_people']").val() +
                  "' </b> ntiyabonetse</span></td></tr>"
              );
              $(".old_content").removeClass("display-none");
            }
          }
        } else {
          alert(data.msg);
        }
      } else {
        $(responseHolder).html(data);
      }
    },
    error: function (xhr) {
      $(btnToDisable).removeAttr("disabled");
      alert(xhr.responseText);
    },
  });
}
