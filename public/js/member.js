var migrant = [];
var members = [];
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
    //console.log($(this).parents("form").serialize());
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
        console.log(data.error);
        if (data.viewError.length > 0) {
          showErrors(data.viewError);
        }
        if (data.view === "checkMember") {
          $(".errors").html("");
          // check if data exist
          if (data.data.length > 0) {
            let da = data.data[0];
            da["table"] = "member";
            da["relation"] = $("#s_relation").val();
            migrant.push(da);
            //rid.attr("readonly");
            $("#txtAction").val("transfermember");
            //  check if it is registered by others
            if (!da["location"]) {
              fillForm(da);
              $("input[name='transfer'").attr("value", "yes");
              // Registered by others
              $(".hide-all").addClass("show-all").removeClass("hide-all");
              $("#txtAction").val("member_to_family");
              $(".btnSaveMember").text("Emeza");
              if (da["relation"] === "Umushyitsi") {
                $(".dvVisitor").addClass("d-block").removeClass("display-none");
              } else {
                $(".what_relation")
                  .removeClass("d-block")
                  .addClass("display-none");
                $(".dvVisitor").removeClass("d-block").addClass("display-none");
              }
              return;
            }
            var d = describeLocation(data.location);
            var userLoc = describeLocation(data.user_loc);
            let is_head = da["familyId"] == "0" ? " Umukuru w'umuryango" : "";
            let names = `${da["givenName"]} ${da["familyName"]} <span class="d-block fs-12 text-info">${is_head}</span> `;
            showInTable(d, userLoc, ".errors", names);
            $(".btnSaveMember").text("Komeza");
          } else {
            $(".hide-all").addClass("show-all").removeClass("hide-all");
            $(".btnSaveMember").text("Emeza");
            $("#txtAction").val("member_to_family");
            var relation = $("#s_relation").val();
            if (relation === "Umushyitsi") {
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
          //let data = data.data;
          if (typeof data.data["migration"] == "string") {
            $("#txtAction").val("member_to_family");
            $("input[name='transfer'").attr("value", "yes");
            fillForm(data.data);
            $(".btnSaveMember").text("Emeza");
            $(".errors").html("");
            var relation = $("#s_relation").val();
            if (relation === "Umushyitsi") {
              $(".dvVisitor").addClass("d-block").removeClass("display-none");
            } else {
              $(".what_relation")
                .removeClass("d-block")
                .addClass("display-none");
              $(".dvVisitor").removeClass("d-block").addClass("display-none");
            }
          } else {
            $(".emeza-wrapper").removeClass("d-none");
            fillForm(data.data);
            members = data.data["migration"];
            $(".show-all").removeClass("show-all").addClass("hide-all");
            // $(".hide-all").addClass("show-all").removeClass("hide-all");
            $(".txtAction").val("find_head_of_family");
            $(".btnSaveMember").text("Komeza");
          }
        } else if (data.view === "viewLocationFromCode") {
          $(btnToDisable).attr("onClick", "toggle(this);").text("Reba/Hisha");
          var loc = data.data;
          var d = `<ul class="list-group-item list-group-flush"><li class="list-group-item ">
            <b>Intara:</b>${loc.province}</li>
            <li class="list-group-item "><b>Akarere:</b>${loc.district}</li>
            <li class="list-group-item "><b>Umurenge:</b>${loc.sector}
            </li><li class="list-group-item"><b>Akagari:</b>${loc.cell}
            </li><li class="list-group-item"><b>Umudugudu:</b>${loc.village}
            </li></ul>`;
          $(responseHolder).html(d);
        } else if (data.view === "helpInFamily") {
          $(btnToDisable).attr("disabled", "disabled");
          showWait(btnToDisable);
          navigate("family?dpl=" + data.data);
        }
      } else {
        alert(data.msg);
        console.log(data.msg);
      }
    },
    error: function (xhr) {
      $(btnToDisable).removeAttr("disabled");
      stopWait();
      //$(responseHolder).html(xhr.responseText);
      alert(xhr.responseText);
      console.log(xhr.responseText);
    },
  });
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
  console.log("show in table");
  var tb = `<table class="table table-bordered">
  <thead>
  <tr><th colspan="2"  class="text-white head">${names}</th></tr>
    <tr>
      <th scope="col">Aho Avuye</th>
      <th scope="col">Aho Ajya</th>
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
  if ($(e).val() == "Other") {
    $("#other_relationship").removeClass("display-none");
  } else {
    $("#other_relationship").addClass("display-none");
  }
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
    if (relation == "Umushyitsi") {
      $(".the-wraper").addClass("data-search-container");
      $("#paste").text($("#head_family").text());
      return;
    }
    //$(".hide-all").addClass("show-all").removeClass("hide-all");
    //$(".nid").addClass("display-none").removeClass("show-all");
    //$(".passport").addClass("display-none").removeClass("show-all");
    $(".btnSaveMember").removeAttr("disabled", "disabled");
  } else if (txt === "PASSPORT") {
    $(".passport").addClass("show-all").removeClass("display-none");
  }
}

function checkDocTypeForHeadOfFamily(e) {
  var docType = $(e).val();
  switchSelectorForHeadOfFamily($(e).val());
}

switchSelectorForHeadOfFamily($("#head_document_type").val());

function switchSelectorForHeadOfFamily(docType) {
  if ($(".rwanda_id").val() != "") {
    $(".no_document").remove();
    console.log("remove all belong to no_document");
    return;
  }
  if (docType == "ID") {
    $("#head_document_id_container")
      .addClass("show-all")
      .removeClass("display-none");
    $("#head_document_passport_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_issue_country_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_issue_date_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_expiry_date_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_to_member_relationship_container")
      .addClass("show-all")
      .removeClass("display-none");
  } else if (docType == "PASSPORT") {
    $("#head_document_id_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_passport_container")
      .addClass("show-all")
      .removeClass("display-none");
    $("#head_document_issue_country_container")
      .addClass("show-all")
      .removeClass("display-none");
    $("#head_document_issue_date_container")
      .addClass("show-all")
      .removeClass("display-none");
    $("#head_document_expiry_date_container")
      .addClass("show-all")
      .removeClass("display-none");
    $("#head_to_member_relationship_container")
      .addClass("show-all")
      .removeClass("display-none");
  } else {
    $("#head_document_id_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_passport_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_issue_country_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_issue_date_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_document_expiry_date_container")
      .addClass("display-none")
      .removeClass("show-all");
    $("#head_to_member_relationship_container")
      .addClass("display-none")
      .removeClass("show-all");
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
    } else if (relation === "Umushyitsi") {
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
    <button type="button" class="btn btn-sm text-white text-center" data-doc="${
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
  $("#whoSelected").text(
    `${data["givenName"]} ${data["familyName"]} ${data["documentNumber"]}`
  );
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
  $("input[name='issued_date']").val(data["issuedDate"]);
  $("input[name='rwandan_id']").val(data["documentNumber"]);
  $("input[name='email']").val(data["email"]);
  $("input[name='phone']").val(data["mobile"]);
  $("input[name='occupation']").val(data["occupation"]);
  $("select[name='level_education']").val(data["level_of_education"]);
  $("select[name='ubudehe']").val(data["ubudehe"]);
  $("#ubudehe-selector").trigger("change");
  $("input[name='migration']").val(data["migration"]);
  $("input[name='tid']").val(data["citizenId"]);
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
  $("select[name='ubudehe']")
    .parents(".hide-all")
    .addClass("show-all col-md-12");
}
function getFamilyHead() {
  $("#btn-remote").remove();
  $(".suggestion-data").addClass("d-none");
  $("#td_members").html("<span>-</span>");
  let id = $("#txtfindById").val();
  if (id.trim().length > 10) {
    showWait("#btnFindById");
    $.post(
      "controller/familyController.php",
      { action: "get_head_with_members", familyid: id },
      function (res) {
        stopWait();
        $(".suggestion-data").removeClass("d-none");
        if (res.status == "NOT_FOUND") {
          $("#td_members").html("");
          $("#td_location").html(
            `<span class='text-danger text-center'>Umukuru w'umuryango ntabwo abonetse</span>
            <button class="btn btn-success" type='button' data-document="${res.document}" onclick="registerHead(this)">
            kanda hano gukomeza</button>`
          );
          $("#td_names").text("-");
          $("#td_members").html(" ");
          return;
        } else {
          $("#btn-remote").remove();
          $("#td_location").html(
            "<b>Aho Atuye </b>:" + displayLocation(res.data["locationByName"])
          );
          let head = `${res.data["familyName"]} ${res.data["otherName"]} ${res.data["givenName"]}`;
          let addMember =
            res.data["locationByName"].length == 0
              ? `<span class='btn btn-success' onclick='addMemberRemote("${res.data["citizenId"]}")' id="btn-remote">  kanda hano gukomeza</span>`
              : "";
          $("#td_names").html(
            `<b>Umukuru y'umuryango : </b> ${
              res.data["documentNumber"]
            } ${head.replaceAll("null", "")}   `
          );
          // add new member to head of family
          $("#add-member").append(`${addMember}`);
          if (res.data["member_count"] == 1) {
            $("#td_members").html(
              `<span class='text-warning'>Ntamunyamuryango umwanditsweho</span>`
            );
            return;
          }
          $("#td_members").html(res.data["members"]);
        }
      },
      "json"
    );
  }
}
// register unregistered head of family
function registerHead(e) {
  let doc = $(e).attr("data-document");
  $("input[name='pid']").val(doc);
  $("#txtAction").val("member_to_family");
  $("#added_remotely").val("yes");
  $(".data-search-close").click();
  $(".hide-all").addClass("show-all").removeClass("hide-all");
  // related to visitors
  var relation = $("#s_relation").val();
  if (relation === "Umushyitsi") {
    $(".dvVisitor").addClass("d-block").removeClass("display-none");
  }
  return;
  showWait(e);
  $.post(
    "controller/familyController",
    { document: doc, action: "add_unregistered_head_of_family" },
    function (res) {
      stopWait();
      if (res.status == 0) alert("Igikorwa ntabwo gikunze mwongere mugerageze");
      $("input[name='pid']").val(res.status);
      $("#txtAction").val("member_to_family");
      $("#added_remotely").val("yes");
      $(".data-search-close").click();
      $(".hide-all").addClass("show-all").removeClass("hide-all");
      // related to visitors
      var relation = $("#s_relation").val();
      if (relation === "Umushyitsi") {
        $(".dvVisitor").addClass("d-block").removeClass("display-none");
      }
    },
    "json"
  );
}
function addMemberRemote(head_id) {
  $("input[name='pid']").val(head_id);
  $("#txtAction").val("member_to_family");
  $("#added_remotely").val("yes");
  $("input[name='members']").val("add");
  $(".data-search-close").click();
  $(".hide-all").addClass("show-all").removeClass("hide-all");
  $(".dvVisitor").addClass("d-block").removeClass("display-none");
}

function displayLocation(locationcode) {
  if (locationcode.length == 0) {
    return " Ntihabonetse";
  }
  let names = "";
  locationcode.forEach((office) => {
    names += office.name + "/";
  });
  return names;
}
// confirm visitor
function confirmVisitor(data) {
  $("#txtAction").val("member_to_family");
  $("input[name='transfer'").attr("value", "yes");
  fillForm(data);
  $(".errors").html("");
  var relation = $("#s_relation").val();
  if (relation === "Umushyitsi") {
    $(".dvVisitor").addClass("d-block").removeClass("display-none");
  } else {
    $(".what_relation").removeClass("d-block").addClass("display-none");
    $(".dvVisitor").removeClass("d-block").addClass("display-none");
  }
  // close popup
  $(".data-search-close").click();
  // $(".btnSaveMember").click();
  $(".opacity").removeClass("opacity").addClass("show-forminputs");
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
