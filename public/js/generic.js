var $ = jQuery;
var members = [];
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
  if (v == "unknown") {
    $(".navigate").click();
    window.location.href = "add-unknown";
    return;
  }
  showOrHideIdInputs(v);
}

showOrHideIdInputs($("#doctype").val());

function showOrHideIdInputs(v) {
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
      console.log(xhr.responseText);
    },
  });
}
function checkRent(e) {
  // $("#nh").val("");
  // $("#ni").val("");
  showOrHideRentInputs($(e).val());
}

showOrHideRentInputs($("#rent_house_selector").val());

function showOrHideRentInputs(txt) {
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
  console.log(txt);
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
              // data[0]["table"] = "head";
              // check if was added outside
              jdata.data[0]["table"] = "head";
              jdata.data[0]["relation"] = "head";
              if (jdata.location.length == 0) {
                $(".opacity")
                  .addClass("show-forminputs")
                  .removeClass("opacity");
                $(".txtAction").val("head_of_family");
                $("input[name='transfer'").attr("value", "yes");
                $("input[name='tid'").attr("value", jdata.data[0]["citizenId"]);
                $("#added_remotely").val("yes");
                $(".save_diplomat").text("Emeza");
                return;
              }
              let datas = jdata.data[0];
              migrant.push(datas);
              let is_head =
                datas["familyId"] == "0" ? " Umukuru w'umuryango" : "";
              let names = `${datas["givenName"]} ${datas["familyName"]} <span class="d-block fs-12 text-info">${is_head}</span> `;
              $(".txtAction").val("transfermember");
              var d = describeLocation(jdata.location);
              var userLoc = describeLocation(jdata.user_loc);
              showInTable(d, userLoc, ".errors", names);
            } else {
              $(".opacity").addClass("show-forminputs").removeClass("opacity");
              $(".save_diplomat").text("Emeza");
              $(".txtAction").val("head_of_family");
            }
          } else if (jdata.view == "viewMovements") {
            if (jdata.data.length > 0) {
              displayMovements(jdata.data);
              $(".txtAction").val("transfermember");
            } else {
              $(".contentHolder").html("Ntabwo arava aho aba");
            }
          } else if (jdata.view === "transferMigrant") {
            let data = jdata.data;
            if (typeof data["migration"] == "string") {
              $(".errors").html("");
              $(".txtAction").val("head_of_family");
              $("input[name='transfer'").attr("value", "yes");
              fillForm(jdata.data);
              $(".save_diplomat").text("Emeza");
            } else {
              $(".emeza-wrapper").removeClass("d-none");
              fillForm(jdata.data);
              members = data["migration"];
              $(".show-forminputs")
                .removeClass("show-forminputs")
                .addClass("opacity");
              $(".txtAction").val("find_head_of_family");
              $(".save_diplomat").text("Komeza");
            }
          } else if (jdata.view === "find_head_of_family") {
            $(".emeza-wrapper").removeClass("d-none");
          } else if (jdata.view === "viewLocationFromCode") {
            $(btnToDisable).attr("onClick", "toggle(this);").text("Reba/Hisha");
            var loc = jdata.data;
            var d = `<ul class="list-group-item list-group-flush"><li class="list-group-item ">
            <b>Intara:</b>${loc.province}</li>
            <li class="list-group-item "><b>Akarere:</b>${loc.district}</li>
            <li class="list-group-item "><b>Umurenge:</b>${loc.sector}
            </li><li class="list-group-item"><b>Akagari:</b>${loc.cell}
            </li><li class="list-group-item"><b>Umudugudu:</b>${loc.village}
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
                [
                  "Umukuru w'umuryango Ntabwo abashishwe kwandikwa mwongere mugeraze",
                ],
                "Kumenyesha"
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
  $title = "Wibagiwe kuzuza ibi bikurikira.",
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
      from += "<li class='list-group-item'> <b>Intara :</b> " + v.name + sep;
    } else if (v.level === "d") {
      from += " <li class='list-group-item'><b>Akarere : </b>" + v.name + sep;
    } else if (v.level === "s") {
      from += " <li class='list-group-item'><b>Umurenge :</b> " + v.name + sep;
    } else if (v.level === "c") {
      from += " <li class='list-group-item'><b>Akagari : </b>" + v.name + sep;
    } else if (v.level === "v") {
      from += " <li class='list-group-item'><b>Umudugudu :</b> " + v.name + sep;
    }
  });
  return from;
}
function showInTable(from, to, holder = ".errors", names = "") {
  var tb = `<table class="table table-bordered">
  <thead>
  <tr><th colspan="2"  class="text-white head">${names}</th></tr>
    <tr>
      <th scope="col">Aho Avuye</th>
      <th scope="col">Aho Ajya</th>
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
  <tr><th colspan="6"  class="text-white head">${array[0]["names"]}</th></tr>
    <tr>
    <th scope="col">#</th>
    <th scope="col">Italiki</th>
      <th scope="col">Yavuye</th>
      <th scope="col">Ajya</th>
      <th scope="col">Ibyavuzwe</th>
      <th scope="col">byinshi</th>
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
    }" onClick="getLocationByName(this);">Nyereka</button></td>
    <td>
    <p class="location "></p>
    <button type="button" class="btn btn-sm bg-primary text-white text-center" 
    data-location="${
      movement["current_location"]
    }" onClick="getLocationByName(this);">Nyereka</button>
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
  $("input[name='given_name']").val(data["givenName"]);
  $("input[name='family_name']").val(data["familyName"]);
  $("input[name='other_name']").val(data["otherName"]);
  $("select[name='gender']").val(data["gender"]);
  $("input[name='dob']").val(data["dob"]);
  $("select[name='marital_status']").val(data["maritalstatus"]);
  $("input[name='birth_place']").val(data["birthplace"]);
  $("input[name='birth_nationality']").val(data["birthNationality"]);
  $("input[name='other_nationality']").val(data["otherNationality"]);
  $("input[name='passport']").val(data["documentNumber"]);
  $("select[name='issued_country']").val(data["issuedCountry"]);
  $("input[name='members']").val(data["members"]);
  $("input[name='issued_date']").val(data["issuedDate"]);
  $("input[name='rwandan_id']").val(data["documentNumber"]);
  $("input[name='doctype']").val(data["documentType"]);
  $("input[name='email']").val(data["email"]);
  $("input[name='phone']").val(data["mobile"]);
  $("select[name='ubudehe']").val(data["ubudehe"]);
  $("input[name='isibo']").val(data["isibo"]);
  $("input[name='tid']").val(data["citizenId"]);
  $("input[name='table']").val(data["tb"]);
  let msg = "yego";
  if (data["landLord"] === null) {
    msg = "hoya";
  }
  $("select[name='rent_house']").val(msg);
  $("#rent_house_selector").trigger("change");
  $("#ubudehe-selector").trigger("change");
  $("input[name='occupation']").val(data["occupation"]);
  $("select[name='level_education']").val(data["level_of_education"]);
  $("input[name='number_house']").attr("value", data["number_of_house"]);
  $("input[name='house_info']").attr("value", data["landLord"]);
  // show some field
  $("input[name='occupation']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-12");
  $("select[name='ubudehe']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-6");

  $("input[name='email']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-6");

  $("input[name='phone']")
    .parents(".opacity")
    .addClass("show-forminputs col-md-6");

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
function onMemberSelected(...elem) {
  $("#selectedmember").val(elem[0] + "," + elem[1] + "," + elem[2]);
}
function displayMembers(array) {
  if (typeof array == "string") return;
  let label = "";
  array.forEach((member) => {
    if ($.trim(member["documentNumber"]).length != 0) {
      let names = `${member["givenName"]} ${member["familyName"]}  ${member["documentNumber"]}`;
      label += `<label class="radio radio-gradient" style="margin-right: 20px;">
            <span class="radio__input">
              <input type="radio" name="umuryango[]" value="${member["citizenId"]}" onchange="onMemberSelected('${member["citizenId"]}','${member["familyId"]}','${names}')" >
              <span class="radio__control"></span>
            </span>
            <span class="radio__label">${names}</span>
          </label>`;
    }
  });
  return `<h4 style="border-bottom: 1px solid #eee; margin-bottom: 30px; padding-bottom: 15px;">Hitamo ugusimbura kuba umukuru w'umuryango</h4>
        <div class="">
          ${label}
        </div>`;
}
