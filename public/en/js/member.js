var migrant = [];
var $ = jQuery;
$(document).ready(function () {
  initialCheck();
  // add help to family
  $(".btnSaveHelp").click(function () {
    var data = new FormData($(this).parents("form").last()[0]);
    post(data, $(this), ".responseHolder");
  });
  // add member to head of family
  $(".btnSaveMember").click(function () {
    var data = new FormData($(this).parents("form").last()[0]);
    if (migrant.length > 0) {
      var json_arr = JSON.stringify(migrant[0]);
      data.append("migrantInfo", json_arr);
    }
    post(data, $(this), ".responseHolder");
  });
});
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
      $(".waiting").removeClass("display-none");
    },
    success: function (data) {
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      if (data.error === "none") {
        if (data.viewError.length > 0) {
          showErrors(data.viewError);
        }
        if (data.view === "checkMember") {
          $(".errors").html("");
          // check if data exist
          if (data.data.length > 0) {
            migrant.push(data.data[0]);
            $("#txtAction").val("transfermember");
            var d = describeLocation(data.location);
            var userLoc = describeLocation(data.user_loc);
            showInTable(d, userLoc);
          } else {
            $(".hide-all").addClass("show-all").removeClass("hide-all");
            $("#txtAction").val("member_to_family");
            var relation = $("#s_relation").val();
            if (relation === "Visitor") {
              $(".dvVisitor").addClass("d-block").removeClass("display-none");
            } else {
              $(".what_relation")
                .removeClass("d-block")
                .addClass("display-none");
              $(".dvVisitor").removeClass("d-block").addClass("display-none");
            }
          }
        } else if (data.view === "addMemberInFamily") {
          $(btnToDisable).attr("disabled", "disabled");
          showWait(btnToDisable);
          navigate("family?dpl=" + data.data);
        } else if (data.view == "viewMovements") {
          if (data.data.length > 0) {
            displayMovements(data.data);
            $("#txtAction").val("transfermember");
          } else {
            $(".contentHolder").modal("hide");
          }
        } else if (data.view === "transferMigrant") {
          $("#txtAction").val("member_to_family");
          $("input[name='transfer'").attr("value", "yes");
          fillForm(data.data);
          $(".errors").html("");
          var relation = $("#s_relation").val();
          if (relation === "Visitor") {
            $(".dvVisitor").addClass("d-block").removeClass("display-none");
          } else {
            $(".what_relation").removeClass("d-block").addClass("display-none");
            $(".dvVisitor").removeClass("d-block").addClass("display-none");
          }
        } else if (data.view === "viewLocationFromCode") {
          $(btnToDisable).attr("onClick", "toggle(this);").text("show/hide");
          var loc = data.data;
          var d = `<ul class="list-group-item list-group-flush"><li class="list-group-item ">
            <b>Province:</b>${loc.province}</li>
            <li class="list-group-item "><b>District:</b>${loc.district}</li>
            <li class="list-group-item "><b>Sector:</b>${loc.sector}
            </li><li class="list-group-item"><b>Cell:</b>${loc.cell}
            </li><li class="list-group-item"><b>Village:</b>${loc.village}
            </li></ul>`;
          $(responseHolder).html(d);
        } else if (data.view === "helpInFamily") {
          $(btnToDisable).attr("disabled", "disabled");
          showWait(btnToDisable);
          navigate("family?dpl=" + data.data);
        }
      } else {
        alert(data.msg);
      }
    },
    error: function (xhr) {
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      //$(responseHolder).html(xhr.responseText);
      alert(xhr.responseText);
    },
  });
}
function describeLocation($locations, sep = "</li>") {
  var from = "";
  $.each($locations, function (i, v) {
    if (v.level === "p") {
      from += "<li class='list-group-item'> <b>Province :</b> " + v.name + sep;
    } else if (v.level === "d") {
      from += " <li class='list-group-item'><b>District : </b>" + v.name + sep;
    } else if (v.level === "s") {
      from += " <li class='list-group-item'><b>Sector :</b> " + v.name + sep;
    } else if (v.level === "c") {
      from += " <li class='list-group-item'><b>Cell : </b>" + v.name + sep;
    } else if (v.level === "v") {
      from += " <li class='list-group-item'><b>Village:</b> " + v.name + sep;
    }
  });
  return from;
}
function showInTable(from, to, holder = ".errors") {
  var tb = `<table class="table table-bordered">
  <thead>
  <tr><th colspan="2"  class="text-white head">The resident registered</th></tr>
    <tr>
      <th scope="col">Came from</th>
      <th scope="col">Where to go</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><ul class="list-group list-group-flush">${from}
      </ul></td>
      <td>${to}</td>
    </tr</tbody></table>`;
  $(holder).html(tb);
}
function checkRelationShip(e) {
  $(".nid").addClass("display-none").removeClass("show-all");
  $(".passport").addClass("display-none").removeClass("show-all");
  $(".show-all").removeClass("show-all").addClass("hide-all");
  $("#doctype").removeAttr("disabled");
  $("#doctype").val("");
  $("#txtAction").val("checkmember");
  $(".errors").html("");
}
function checkDocType(e) {
  $(".errors").html("");
  var txt = $(e).val();
  $(".nid").addClass("display-none").removeClass("show-all");
  $(".passport").addClass("display-none").removeClass("show-all");
  $(".show-all").removeClass("show-all").addClass("hide-all");
  if (txt === "ID") {
    $(".nid").addClass("show-all").removeClass("display-none");
    $("#txtAction").val("checkmember");
  } else if (txt === "NONE") {
    var relation = $("#s_relation").val();
    if (relation !== "Visitor" && relation !== "Kid") {
      $("#txtAction").val("check_unknown_family_member");
    }
    $(".hide-all").addClass("show-all").removeClass("hide-all");
    $(".nid").addClass("display-none").removeClass("show-all");
    $(".passport").addClass("display-none").removeClass("show-all");
    $(".btnSaveMember").removeAttr("disabled", "disabled");
  } else if (txt === "PASSPORT") {
    $(".passport").addClass("show-all").removeClass("display-none");
  }
}
function validateIDorPassport(e, docType = "ID") {
  var value = $(e).val().trim();
  if (docType === "ID" && value.length === 16) {
    $(".btnSaveMember").removeAttr("disabled", "disabled");
    $("#txtAction").val("checkmember");
  } else if (docType === "PASSPORT" && value.length > 0) {
    $(".btnSaveMember").removeAttr("disabled", "disabled");
    $("#txtAction").val("checkmember");
  } else {
    $(".btnSaveMember").attr("disabled", "disabled");
  }
}
function onlyNumber(selector) {
  $(selector).val(
    $(selector)
      .val()
      .replace(/[^0-9]/g, "")
  );
}

function showErrors(
  errors,
  $title = "You forgot to complete the following:",
  errorHolder = ".errors"
) {
  var divError = `<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Errors!</strong> ${$title}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <ul class="list-group list-group-flush">`;
  var e = "";
  for (const err of errors) {
    e += `<li class="list-group-item list-group-item-danger">${err}</li>
  </li>`;
  }
  divError += e + "</ul></div>";
  $(errorHolder).html(divError);
}
function showWait(waitholder, style = "font-size:20px") {
  $(waitholder).append(
    `<i class='fa fa-spinner fa-spin gifWait text-warning' style=${style}></i>`
  );
}
function stopWait(selector = ".gifWait") {
  $(selector).hide();
  $(".gifWait").remove();
}
function navigate(url = "") {
  window.location.href = url;
}
function initialCheck() {
  if ($("input[name='id_to_edit']").length) {
    $(".hide-all").addClass("show-all");
    $(".btnSaveMember").removeAttr("disabled", "disabled");
    var docType = $("#doctype").val();
    $("#doctype").removeAttr("disabled");
    if (docType === "ID") {
      $(".nid").addClass("show-all");
    } else if (docType === "PASSPORT") {
      $(".passport").addClass("show-all");
    }
    var relation = $("#s_relation").val();
    if (relation === "Partner") {
      $(".what_relation").addClass("show-all");
    } else if (relation === "Visitor") {
      $(".dvVisitor").addClass("show-all");
    }
  }
}
function openModalMovement(e) {
  $("#modalMovement").modal("show");
  showWait(
    ".contentHolder",
    "font-size:50px;margin-left:40%;border-radius:4rem;overflow:hide;overflow:hidden;color:darkred;background:#60085f;"
  );
  $("#txtAction").val("get_movements");
  $(".btnSaveMember").click();
}
// display movements
function displayMovements(array, contentHolder = ".contentHolder") {
  var tb = `<table class="table table-bordered table-responsive">
  <thead>
  <tr><th colspan="6"  class="text-white head">${array[0]["names"]}/${array[0]["document_id"]}</th></tr>
    <tr>
    <th scope="col">#</th>
    <th scope="col">Date</th>
      <th scope="col">Came From</th>
      <th scope="col">lived</th>
      <th scope="col">comment</th>
      <th scope="col">More</th>
    </tr>
  </thead>
  <tbody>`;
  var tr = "";
  let i = 1;
  array.forEach((movement) => {
    tr += `
    <tr class="fs-12"><td>${i}</td><td>${movement["action_date"]}</td>
    <td>
    <p class="location"></p>
    <button type="button" class="btn btn-sm bg-primary text-white text-center" 
    data-location="${
      movement["pre_location"]
    }" onClick="getLocationByName(this);">View</button></td>
    <td>
    <p class="location "></p>
    <button type="button" class="btn btn-sm bg-primary text-white text-center" 
    data-location="${
      movement["current_location"]
    }" onClick="getLocationByName(this);">View</button>
    </td>
    <td>
    <p class="fs-12">${
      movement["comment"].length > 0 ? movement["comment"] : "-"
    }</p>
    </td>
    <td>
    <p></p>
    <button type="button" class="btn btn-sm text-white text-center" data-doc="${
      movement["document_id"]
    }" >More info</button></td></tr>
    `;
    i++;
  });
  tb += tr + "</tbody></table>";
  $(contentHolder).html(tb);
}
// to get location from code to name
function getLocationByName(e) {
  $("#modalMovement").modal("show");
  // showWait(".contentHolder");
  var form = new FormData();
  form.set("action", "get_location_by_name");
  form.set("location_code", $(e).attr("data-location"));
  post(form, $(e), $(e).parent().find("p"));
}
// toggle elemement
function toggle(e) {
  $(e).parent().find("p").toggle();
}
function fillForm(data) {
  $("input[name='given_name']").val(data["given_name"]);
  $("input[name='family_name']").val(data["family_name"]);
  $("input[name='other_name']").val(data["other_name"]);
  $("select[name='gender']").val(data["gender"]);
  $("input[name='dob']").val(data["dob"]);
  $("select[name='marital_status']").val(data["marital_status"]);
  $("input[name='birth_place']").val(data["birth_place"]);
  $("input[name='birth_nationality']").val(data["birth_nationality"]);
  $("input[name='other_nationality']").val(data["other_nationality"]);
  $("input[name='passport']").val(data["document_id"]);
  $("select[name='issued_country']").val(data["issued_country"]);
  $("input[name='issued_date']").val(data["issued_date"]);
  $("input[name='rwandan_id']").val(data["document_id"]);
  $("input[name='email']").val(data["email"]);
  $("input[name='phone']").val(data["phone"]);
  $("input[name='occupation']").val(data["occupation"]);
  $("select[name='level_education']").val(data["level_education"]);
  $("input[name='tid']").val(data["id"]);
  $("input[name='table']").val(data["tb"]);
  // show some field
  $("select[name='level_education']")
    .parents(".hide-all")
    .addClass("show-all col-md-12")
    .removeClass(".hide-all");
  $("input[name='occupation']")
    .parents(".hide-all")
    .addClass("show-all col-md-12")
    .removeClass(".hide-all");
}
