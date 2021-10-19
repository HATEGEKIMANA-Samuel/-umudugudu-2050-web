function checkChanges(e, val = "", elemToggle) {
  if ($(e).val() === val) {
    $(elemToggle).removeClass("display-none").addClass("d-block");
  } else {
    $(elemToggle).addClass("display-none").removeClass("d-block");
  }
}
$(document).ready(function () {
  $(".btnStatistic").click(function () {
    $(".card-view").removeClass("display-none");
    console.log($("form").serialize());
    var data = new FormData($(this).parents("form").last()[0]);
    post(data, $(this), ".response");
  });
});

function displayData(array) {
  var tr = "";
  var tb = `<table class="table table-bordered">
  <thead>
  <tr><th colspan="4"  class="text-white bg-dark">Total ${array.length}</th></tr>
    <tr>
      <th scope="col">
Names / documents</th>
      <th scope="col">Date of birth</th>
      <th scope="col">Level of Education </th>
      <th scope="col">More</th>
    </tr>
  </thead>
  <tbody>`;
  array.forEach((element) => {
    tr += `<tr class="fs-12">
     <td>${element.names}</td>
      <td>${element.dob}</td>
      <td>${element.level_education}</td>
      <td><button class='btn btn-sm' type='button'>View more</button></td>
    </tr>`;
  });
  return tb + tr + "</tbody></table>";
}
function post(
  postData,
  btnToDisable,
  responseHolder,
  page = "controller/statisticController.php"
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
      $(".waiting").removeClass("display-none");
    },
    success: function (data) {
      $(".waiting").addClass("display-none");
      $(btnToDisable).removeAttr("disabled");
      var jdata = data;
      if (typeof jdata === "object") {
        if (jdata.error === "none") {
          if (jdata.view === "viewStatistic") {
            if (data.data.length > 0) {
              $(responseHolder).html(displayData(data.data));
            } else {
              $(responseHolder).html(`<h1>0</h1>`);
            }
          }
        } else {
          // expected result not found
          alert(jdata.msg);
          //console.log(data);
        }
      } else {
        $(responseHolder).html(data);
      }
    },
    error: function (xhr) {
      $(btnToDisable).removeAttr("disabled");
      alert(xhr.responseText);
      // console.log(xhr.responseText);
    },
  });
}
