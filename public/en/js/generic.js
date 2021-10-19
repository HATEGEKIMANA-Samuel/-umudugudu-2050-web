var $ = jQuery;
var migrant = [];
$(document).ready(function () {
  $(".save_diplomat").attr("disabled", "disabled");
  $(".txtAction").val("checkmember");
  if ($("input[name='id_to_edit']").length) {
    $(".save_diplomat").removeAttr("disabled");
    $(".opacity").addClass("show-forminputs");
    $(".txtAction").val("head_of_family");
  }
  // do staff after page loading~
  $(".save_diplomat").click(function () {
    var data = new FormData($(this).parents("form").last()[0]);
    if (migrant.length > 0) {
      var json_arr = JSON.stringify(migrant[0]);
      data.append("migrantInfo", json_arr);
    }
    post(data, $(this), ".responseHolder");
  });
  // check ubudehe
  $(".ubudehe").keyup(function () {
    var n = Number($(this).val());
    if (n > 4 || n == 0) {
      $(this).val("");
    }
  });
});
// to check if member exists in system
var timeout = null;
function checkMember(value, docType = "ID") {
  $(".txtAction").val("checkmember");
  $(".errors").html("");
  if (docType === "ID" && value.length === 16) {
    $(".save_diplomat").removeAttr("disabled", "disabled");
  } else if (docType === "PASSPORT" && value.length > 0) {
    $(".save_diplomat").removeAttr("disabled", "disabled");
  } else {
    $(".save_diplomat").attr("disabled", "disabled");
    $(".show-forminputs").removeClass("show-forminputs").addClass("opacity");
    $(".waiting").addClass("display-none");
  }
}
function onlyNumber(selector) {
  $(selector).val(
    $(selector)
      .val()
      .replace(/[^0-9]/g, "")
  );
}
function checkDocType(e) {
  $(".errors").html("");
  $(".show-forminputs").removeClass("show-forminputs").addClass("opacity");
  var v = $(e).val();
  if (v === "ID") {
    $(".txtAction").val("checkmember");
    $(".divpp").addClass("display-none");
    $(".divid").removeClass("display-none");
    $(".doctypes").removeClass("col-md-12").addClass("col-md-6");
  } else if (v === "PASSPORT") {
    $(".txtAction").val("checkmember");
    $(".divpp").removeClass("display-none");
    $(".divid").addClass("display-none");
    $(".doctypes").removeClass("col-md-6").addClass("col-md-12");
  } else {
    $(".divid").addClass("display-none");
    $(".divpp").addClass("display-none");
  }
}
function get(
  data,
  btnToDisable,
  responseHolder,
  page = "controller/familyController.php"
) {
  $.ajax({
    type: "GET",
    data: data,
    url: page,
    contentType: false,
    beforeSend: function () {
      $(btnToDisable).attr("disabled", "disabled");
      showWait(btnToDisable);
    },
    success: function (data) {
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      // check if it is object
      var jdata = JSON.parse(data);
      if (typeof jdata === "object") {
        if (jdata.error === "none") {
        } else {
          // expected result not found
          alert(jdata.msg);
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
function checkRent(e) {
  // $("#nh").val("");
  // $("#ni").val("");
  var txt = $(e).val();
  if (txt == "yego") {
    $(".owner_house_info")
      .addClass("show-forminputs")
      .removeClass("display-none");
    $(".owner_house").removeClass("show-forminputs").addClass("display-none");
  } else if (txt == "hoya") {
    $(".owner_house").addClass("show-forminputs").removeClass("display-none");
    $(".owner_house_info")
      .removeClass("show-forminputs")
      .addClass("display-none");
  }
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
      $(".waiting").removeClass("display-none");
    },
    success: function (data) {
      $(".waiting").addClass("display-none");
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      var jdata = data;
      if (typeof jdata === "object") {
        if (jdata.error === "none") {
          if (jdata.viewError.length > 0) {
            showErrors(jdata.viewError);
          }
          if (jdata.view === "checkMember") {
            $(".errors").html("");
            // check if data exist
            if (jdata.data.length > 0) {
              // console.log(jdata.data);
              migrant.push(jdata.data[0]);
              $(".txtAction").val("transfermember");
              var d = describeLocation(jdata.location);
              var userLoc = describeLocation(jdata.user_loc);
              showInTable(d, userLoc);
            } else {
              $(".opacity").addClass("show-forminputs").removeClass("opacity");
              $(".txtAction").val("head_of_family");
            }
          } else if (jdata.view == "viewMovements") {
            if (jdata.data.length > 0) {
              displayMovements(jdata.data);
              $(".txtAction").val("transfermember");
            } else {
              $(".contentHolder").html("He/she never left his/her home");
            }
          } else if (jdata.view === "transferMigrant") {
            $(".txtAction").val("head_of_family");
            $("input[name='transfer'").attr("value", "yes");
            fillForm(jdata.data);
            $(".errors").html("");
          } else if (jdata.view === "viewLocationFromCode") {
            $(btnToDisable).attr("onClick", "toggle(this);").text("Show/Hide");
            var loc = jdata.data;
            var d = `<ul class="list-group-item list-group-flush"><li class="list-group-item ">
            <b>Province:</b>${loc.province}</li>
            <li class="list-group-item "><b>District:</b>${loc.district}</li>
            <li class="list-group-item "><b>Sector:</b>${loc.sector}
            </li><li class="list-group-item"><b>Cell:</b>${loc.cell}
            </li><li class="list-group-item"><b>Village:</b>${loc.village}
            </li></ul>`;
            $(responseHolder).html(d);
          } else if (jdata.view === "addHeadOfFamily") {
            $(".errors").html("");
            if (jdata.id > 0) {
              $(btnToDisable).attr("disabled", "disabled");
              showWait(btnToDisable);
              window.location.href = "family?dpl=" + jdata.data;
            } else {
              showErrors(
                ["Head of Family Not registered and retry"],
                "Information"
              );
            }
          }
        } else {
          // expected result not found
          alert(jdata.msg);
        }
      } else {
        $(responseHolder).html(data);
      }
    },
    error: function (xhr) {
      $(btnToDisable).removeAttr("disabled");
      alert(xhr.responseText);
      console.log(xhr);
    },
  });
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
      from += " <li class='list-group-item'><b>Village :</b> " + v.name + sep;
    }
  });
  return from;
}
function showInTable(from, to, holder = ".errors") {
  var tb = `<table class="table table-bordered">
  <thead>
  <tr><th colspan="2"  class="text-white head">
The resident registered</th></tr>
    <tr>
      <th scope="col">Came From</th>
      <th scope="col">Where to go</th>
    </tr>
  </thead>
  <tbody>
    <tr class="fs-12">
      <td><ul class="list-group list-group-flush">${from}
      </ul></td>
      <td>${to}</td>
    </tr</tbody></table>`;
  $(holder).html(tb);
}
function openModalMovement(e) {
  $("#modalMovement").modal("show");
  showWait(
    ".contentHolder",
    "font-size:50px;margin-left:40%;border-radius:4rem;overflow:hide;overflow:hidden;color:darkred;background:#60085f;"
  );
  $(".txtAction").val("get_movements");
  $(".save_diplomat").click();
}
// display movements
function displayMovements(array, contentHolder = ".contentHolder") {
  var tb = `<table class="table table-bordered table-responsive">
  <thead>
  <tr><th colspan="6"  class="text-white head">${array[0]["names"]}/${array[0]["document_id"]} </th></tr>
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
    <button type="button" class="btn btn-sm  text-center" data-doc="${
      movement["document_id"]
    }" >Amakuru ye</button></td></tr>
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
  $("input[name='members']").val(data["members"]);
  $("input[name='issued_date']").val(data["issued_date"]);
  $("input[name='rwandan_id']").val(data["document_id"]);
  $("input[name='doctype']").val(data["type"]);
  $("input[name='email']").val(data["email"]);
  $("input[name='phone']").val(data["phone"]);
  $("input[name='ubudehe']").val(data["ubudehe"]);
  $("input[name='isibo']").val(data["isibo"]);
  $("input[name='tid']").val(data["id"]);
  $("input[name='table']").val(data["tb"]);
  // $("select[name='rent_house']").val(data["rent_house"]);
  $("input[name='occupation']").val(data["occupation"]);
  $("select[name='level_education']").val(data["level_education"]);
  // $("input[name='number_house']").attr("value", data["number_house"]);
  // $("input[name='house_info']").attr("value", data["house_info"]);
  // show some field
  $("input[name='occupation']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12");
  $("input[name='ubudehe']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12")
    .removeClass("opacity");
  $("input[name='isibo']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12")
    .removeClass("opacity");
  $("select[name='rent_house']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12")
    .removeClass("opacity");
  $("select[name='level_education']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12")
    .removeClass("opacity");
  $("input[name='members']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12")
    .removeClass("opacity");
}
