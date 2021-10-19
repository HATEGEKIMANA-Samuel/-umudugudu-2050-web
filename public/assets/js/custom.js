document.onkeydown = function (e) {
  if (event.keyCode == 123) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == "I".charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == "C".charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.shiftKey && e.keyCode == "J".charCodeAt(0)) {
    return false;
  }
  if (e.ctrlKey && e.keyCode == "U".charCodeAt(0)) {
    return false;
  }
};
$(function () {
  $("form.search-form").submit(function () {
    $("#page-loader").removeClass("d-none");
  });
  $(".openOffice").click(function () {
    $(".main-report-area").css("left", "0");
  });
  $(".navigate").click(function () {
    $("#page-loader").removeClass("d-none");
    $(this).append(
      `<i class='fa fa-spinner fa-spin gifWait text-warning' style='font-size:20px'></i>`
    );
  });
  $(".navbar .navbar-nav li.menu-item-has-children .sub-menu li").mouseover(
    function (e) {
      $(this).css("background-color", "#6a005b");
    }
  );

  $(".navbar .navbar-nav li.menu-item-has-children .sub-menu li").mouseleave(
    function (e) {
      $(this).css("background-color", "transparent");
    }
  );
  $(".header-left .dropdown .dropdown-menu .dropdown-item").mouseover(function (
    e
  ) {
    $(this).css({
      "background-color": "#dbecfd",
      color: "#fff",
    });
  });

  $(".header-left .dropdown .dropdown-menu .dropdown-item").mouseleave(
    function (e) {
      $(this).css("background-color", "transparent");
    }
  );

  $(".dropdown-toggle").click(function (e) {
    e.preventDefault();
    // e.stopPropagation();
    if ($(this).next(".sub-menu").is(":visible")) {
      $(this).next(".sub-menu").hide();
      $(this)
        .parents(".menu-item-has-children")
        .siblings()
        .find(".sub-menu")
        .hide();
    }
  });

  $(".menuMenu").mouseleave(function (e) {
    e.preventDefault();
    $(this).hide("fast");
  });

  $("input.form-control, select.form-control").focusin(function (e) {
    e.preventDefault();
    $(this).css({
      border: "2px solid #25a3ff",
      "background-color": "#fff",
    });
  });
  $("input.form-control, select.form-control").focusout(function (e) {
    e.preventDefault();
    $(this).css({
      border: "none",
      "background-color": "#fff",
    });
    if ($(this).hasClass("required") && $(this).val() == "") {
      // $(this).css({'border':'1px solid #e74c3c', 'background-color':'#fff'});
    }
  });

  $(".info-list").mouseover(function () {
    $(this).css({
      "background-color": "#dbecfd",
      color: "#fff",
    });
  });

  $(".info-list").mouseleave(function () {
    $(this).css("background-color", "transparent");
  });

  $(".theTitle").change(function () {
    if ($(this).val() == "Other") {
      notification - notice;
      // alert('tested')
      $(".nationalityField").removeClass("col-md-6").addClass("col-md-4");
      $(".otherIfAny").removeClass("col-md-6").addClass("col-md-4");
    } else {
      $(".nationalityField").addClass("col-md-6").removeClass("col-md-4");
      $(".otherIfAny").addClass("col-md-6").removeClass("col-md-4");
    }
  });

  $(".datepicker").change(function () {
    $(
      "div.datepicker.datepicker-dropdown.dropdown-menu.datepicker-orient-left.datepicker-orient-bottom"
    ).css("display", "none");
  });

  $(".menu-item-has-children").click(function () {
    $(this)
      .find(
        ".open aside.left-panel .navbar .navbar-nav li.menu-item-has-children .sub-menu"
      )
      .css("display", "block !important");
  });

  $(".seePassword").click(function (e) {
    e.preventDefault();

    if (
      $(this)
        .parents(".position-relative")
        .find("input.form-control")
        .attr("type") === "password"
    ) {
      $(this)
        .parents(".position-relative")
        .find("input.form-control")
        .attr("type", "text");
    } else {
      $(this)
        .parents(".position-relative")
        .find("input.form-control")
        .attr("type", "password");
    }
  });

  // 	EDIT PASSWORD
  $("#changePasswordForm").submit(function (e) {
    e.preventDefault();

    const password = $("#password").val();
    const confirmPassword = $("#p_check").val();
    const id = $("#updatebtn").attr("data-id");

    if ($.trim(password) !== $.trim(confirmPassword)) {
      $(".resp").html(
        '<p class="alert mb-10 fs-13 w-100p alert-danger">Passwords dont match</p>'
      );
      return false;
    } else if (password === "" || confirmPassword === "") {
      $(".resp").html(
        '<p class="alert mb-10 fs-13 w-100p alert-danger">Fill all the fields</p>'
      );
    } else {
      $.ajax({
        url: "userAction.php",
        method: "POST",
        data: {
          id: id,
          adminChange: "editpswd",
          password: password,
        },
        beforeSend: function () {
          $(".resp").html(
            '<p class="alert mb-10 fs-13 w-100p alert-info">changing....</p>'
          );
        },
        success: function (data) {
          if (data.trim() == "404") {
            $(".resp").html(
              '<p class="alert mb-10 fs-13 w-100p alert-danger">password not changed</p>'
            );
          } else {
            $(".resp").html(
              '<p class="alert mb-10 fs-13 w-100p alert-success">Passwords changed</p>'
            );
          }
        },
        error: function (xhr, status) {
          console.log(xhr.status);
        },
      });
    }
  });
});
function getTotalSummaryInHome() {
  if ($("#summaryHolder").length) {
    $("#loaderh").text("Guhindura Amukuru ...");
    $.get(`ajax/homeData`, function (response) {
      $("#loaderh").text("");
      $("#summaryHolder").html(response);
      // $("#summaryHolder").slideUp(500, function () {

      //   $("#summaryHolder").slideDown();
      // });
    });
  }
}
const umutekano = document.getElementById("umutekano_id");
if (umutekano) {
  umutekano.addEventListener("change", getIcyabaye);
}
function getIcyabaye() {
  var issue = _("umutekano_id").value;
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = function () {
    if (ajaxReturn(ajax) == true) {
      _("icyabaye_id").innerHTML = ajax.responseText;
    }
  };
  ajax.send("issue_to_display=" + issue);
}

$(".toggleMenu, .closeMenu").click(function () {
  $("aside").toggleClass("left-0");
  $(".main-menu ").addClass("show");
});
var stickyCard = "#province-card";
var provinceId, districtId, sectorId, cellId, villageId;

// set bg
function setBg(e) {
  let page = window.location.pathname;
  $("tr").removeClass("bg-ibiro");
  $(e).parent().addClass("bg-ibiro");
  if (page != "/home") {
    $("#page-loader").removeClass("d-none");
    window.location.reload();
  }
}
// data related to home
var timeoutv2 = null;
function configureDashboard() {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      hideCards();
      if (ajax.responseText.split(".")[0] == "loadFromSession") {
        var response = ajax.responseText.split(".");
        const userLevel = Number.parseInt(response[1].split("-")[0]);
        const userLevelNumeric = Number.parseInt(response[1].split("-")[1]);
        const userLevelText = response[1].split("-")[2];
        const userFullLocation = response[1].split("-")[3];
        var CLtext = response[2].split("-")[1];
        var CLnumeric = response[2].split("-")[0];
        var textLocation = CLtext.split(" / ")[CLtext.split(" / ").length - 1];

        $("#current-location").text(CLtext);

        switch (userLevel) {
          case 6:
            stickyCard = "#district-card";
            if (textLocation == userLevelText) {
              getDistricts(CLnumeric, textLocation);
            } else {
              var locationArray = CLnumeric.split("#");
              var districtCode = locationArray[0] + "#" + locationArray[1];
              var sectorCode =
                locationArray[0] +
                "#" +
                locationArray[1] +
                "#" +
                locationArray[2];
              getDistricts(locationArray[0], CLtext.split(" / ")[1]);
              if (locationArray.length == 2) {
                getSectors(CLnumeric, textLocation);
              } else if (locationArray.length == 3) {
                getSectors(districtCode, CLtext.split(" / ")[2]);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getCells(CLnumeric, textLocation);
                }, 100);
              } else if (locationArray.length == 4) {
                getSectors(districtCode, CLtext.split(" / ")[2]);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getCells(sectorCode, CLtext.split(" / ")[3]);
                }, 100);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getVillages(CLnumeric, textLocation);
                }, 200);
              }
            }
            break;
          case 5:
            stickyCard = "#sector-card";
            if (textLocation == userLevelText) {
              getSectors(CLnumeric, textLocation);
            } else {
              var locationArray = CLnumeric.split("#");
              var districtCode = locationArray[0] + "#" + locationArray[1];
              getSectors(districtCode, userLevelText);
              if (locationArray.length == 3) {
                getCells(CLnumeric, textLocation);
              } else if (locationArray.length == 4) {
                var sectorCode =
                  locationArray[0] +
                  "#" +
                  locationArray[1] +
                  "#" +
                  locationArray[2];
                var sectorText = CLtext.split(" / ")[3];
                getCells(sectorCode, sectorText);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getVillages(CLnumeric, textLocation);
                }, 100);
              }
            }
            break;
          case 4:
            stickyCard = "#cell-card";
            if (textLocation == userLevelText) {
              getCells(CLnumeric, textLocation);
            } else {
              var locationArray = CLnumeric.split("#");
              var sectorCode =
                locationArray[0] +
                "#" +
                locationArray[1] +
                "#" +
                locationArray[2];
              getCells(sectorCode, userLevelText);
              getVillages(CLnumeric, textLocation);
            }
            break;
          case 3:
            stickyCard = "#village-card";
            getVillages(CLnumeric, textLocation);
            break;
          case 2:
            stickyCard = "";
            break;
          default:
            stickyCard = "#province-card";
            if (textLocation == userLevelText) {
              getProvinces();
            } else {
              var locationArray = CLnumeric.split("#");
              var districtCode = locationArray[0] + "#" + locationArray[1];
              var sectorCode =
                locationArray[0] +
                "#" +
                locationArray[1] +
                "#" +
                locationArray[2];
              getProvinces();
              if (locationArray.length == 1) {
                getDistricts(CLnumeric, textLocation);
              } else if (locationArray.length == 2) {
                getDistricts(locationArray[0], CLtext.split(" / ")[1]);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getSectors(CLnumeric, textLocation);
                }, 100);
              } else if (locationArray.length == 3) {
                getDistricts(locationArray[0], CLtext.split(" / ")[1]);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getSectors(districtCode, CLtext.split(" / ")[2]);
                }, 100);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getCells(CLnumeric, textLocation);
                }, 200);
              } else if (locationArray.length == 4) {
                getDistricts(locationArray[0], CLtext.split(" / ")[1]);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getSectors(districtCode, CLtext.split(" / ")[2]);
                }, 100);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getCells(sectorCode, CLtext.split(" / ")[3]);
                }, 200);
                clearTimeout(timeoutv2);
                timeoutv2 = setTimeout(() => {
                  getVillages(CLnumeric, textLocation);
                }, 300);
              }
            }
            break;
        }
      } else {
        // FIXME: delete section
        var response = ajax.responseText.split(".");
        const userLevel = Number.parseInt(response[0].split("-")[0]);
        var CLtext = response[1].split("-")[1];
        var CLnumeric = response[1].split("-")[0];
        var textLocation = CLtext.split(" / ")[CLtext.split(" / ").length - 1];

        $("#current-location").text(CLtext);

        switch (userLevel) {
          case 6:
            stickyCard = "#district-card";
            getDistricts(CLnumeric, textLocation);
            break;
          case 5:
            stickyCard = "#sector-card";
            getSectors(CLnumeric, textLocation);
            break;
          case 4:
            stickyCard = "#cell-card";
            getCells(CLnumeric, textLocation);
            break;
          case 3:
            stickyCard = "#village-card";
            getVillages(CLnumeric, textLocation);
            break;
          case 2:
            stickyCard = "";
            getVillageStats(CLnumeric);
            break;
          default:
            getProvinces();
            break;
        }
      }
    } else {
      // console.log(ajax.responseText)
    }
  };
  ajax.send("configure_dashboard=all");
}

function getProvinces() {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var provinces = ajax.responseText.split("-");
      var html = "";
      provinces.forEach((item) => {
        if (item.split(",")[1]) {
          html += `<tr><td class="text-left fs-14 bg-none" onclick="setBg(this);getDistricts(${
            item.split(",")[0]
          }, \'${
            item.split(",")[1]
          }\')"><span class="legend-label ball-2"></span>${
            item.split(",")[1]
          }</td><td><label class="badge badge-outline-success badge-pill AllFDS">${
            item.split(",")[2]
          }</label></td></tr>`;
        }
      });
      $("#province-list").html(html);
      hideCards();
      $(stickyCard).show();
    }
  };
  ajax.send("get_provinces=all");
}

function getDistricts(province, name) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var districts = ajax.responseText.split("_")[0].split("-");
      var html = "";
      districts.forEach((item) => {
        if (item.split(",")[1]) {
          html += `<tr><td class="text-left fs-14 bg-none" onclick="setBg(this);getSectors('${province}#${
            item.split(",")[0]
          }', \'${
            item.split(",")[1]
          }\')"><span class="legend-label ball-2"></span>${
            item.split(",")[1]
          }</td><td><label class="badge badge-outline-danger badge-pill AllFDS">${
            item.split(",")[2]
          }</label></td></tr>`;
        }
      });
      $("#district-list").html(html);
      hideCards();
      $(stickyCard).show();
      $("#district-card").show();
      $("#district-name").text(name);
      $("#current-location").text(ajax.responseText.split("_")[2]);

      // update head of family & members
      var stats = ajax.responseText.split("_")[1];
      if (!document.getElementById("summaryHolder")) {
      } else {
        console.log("am called in province");
        getTotalSummaryInHome();
        loadSecurityData();
      }
      // update view labels
      provinceId = province;
    }
  };
  ajax.send("get_districts=" + province + "." + name);
}

function getSectors(district, name) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var sectors = ajax.responseText.split("_")[0].split("-");
      var html = "";
      sectors.forEach((item) => {
        if (item.split(",")[1]) {
          html += `<tr><td class="text-left fs-14 bg-none" onclick="setBg(this);getCells('${district}#${
            item.split(",")[0]
          }', \'${
            item.split(",")[1]
          }\')"><span class="legend-label ball-2"></span>${
            item.split(",")[1]
          }</td><td><label class="badge badge-outline-danger badge-pill AllFDS">${
            item.split(",")[2]
          }</label></td></tr>`;
        }
      });
      $("#sector-list").html(html);
      hideCards();
      $(stickyCard).show();
      $("#sector-card").show();
      $("#sector-name").text(name);
      $("#current-location").text(ajax.responseText.split("_")[2]);

      // update head of family & members
      var stats = ajax.responseText.split("_")[1];
      if (!document.getElementById("summaryHolder")) {
      } else {
        console.log("Am called in district");
        getTotalSummaryInHome();
        loadSecurityData();
      }
      // $("#family-count").text(stats.split(",")[0]);
      // $("#member-count").text(stats.split(",")[1]);

      // update view labels
      districtId = district;
    }
  };
  ajax.send("get_sectors=" + district + "." + name);
}

function getCells(sector, name) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var cells = ajax.responseText.split("_")[0].split("-");
      var html = "";
      cells.forEach((item) => {
        if (item.split(",")[1]) {
          html += `<tr><td class="text-left fs-14 bg-none" onclick="setBg(this);getVillages('${sector}#${
            item.split(",")[0]
          }', \'${
            item.split(",")[1]
          }\')"><span class="legend-label ball-2"></span>${
            item.split(",")[1]
          }</td><td><label class="badge badge-outline-danger badge-pill AllFDS">${
            item.split(",")[2]
          }</label></td></tr>`;
        }
      });
      $("#cell-list").html(html);
      hideCards();
      $(stickyCard).show();
      $("#cell-card").show();
      $("#cell-name").text(name);
      $("#current-location").text(ajax.responseText.split("_")[2]);

      // update head of family & members
      var stats = ajax.responseText.split("_")[1];
      // $("#family-count").text(stats.split(",")[0]);
      // $("#member-count").text(stats.split(",")[1]);
      if (!document.getElementById("summaryHolder")) {
      } else {
        console.log("am called in sector");
        getTotalSummaryInHome();
        loadSecurityData();
      }
      // update view labels
      sectorId = sector;
    }
  };
  ajax.send("get_cells=" + sector + "." + name);
}

function getVillages(cell, name) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var villages = ajax.responseText.split("_")[0].split("-");
      var html = "";
      villages.forEach((item) => {
        if (item.split(",")[1]) {
          html += `<tr><td class="text-left fs-14"><span class="legend-label ball-2"></span>${
            item.split(",")[1]
          }</td><td><label class="badge badge-outline-danger badge-pill AllFDS">${
            item.split(",")[2]
          }</label></td></tr>`;
        }
      });
      $("#village-list").html(html);
      hideCards();
      $(stickyCard).show();
      $("#village-card").show();
      $("#village-name").text(name);
      $("#current-location").text(ajax.responseText.split("_")[2]);

      // update head of family & members
      var stats = ajax.responseText.split("_")[1];
      if (!document.getElementById("summaryHolder")) {
      } else {
        console.log("am called from cell");
        getTotalSummaryInHome();
        loadSecurityData();
      }
      // $("#family-count").text(stats.split(",")[0]);
      // $("#member-count").text(stats.split(",")[1]);
      // getTotalSummaryInHome();

      // update view labels
      cellId = cell;
    }
  };
  ajax.send("get_villages=" + cell + "." + name);
}

function getVillageStats(village) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      var response = ajax.responseText.split("_");
      var value = response[0].split(",");

      // update head of family & members
      if (!document.getElementById("summaryHolder")) {
      } else {
        console.log("am called from village");
        getTotalSummaryInHome();
        loadSecurityData();
      }
      // $("#family-count").text(value[0]);
      // $("#member-count").text(value[1]);
      $("#current-location").text(response[1]);

      // update view labels
      villageId = village;
    }
  };
  ajax.send("get_village_stats=" + village);
}

function get_encryption(
  strToEncrypt = "0-",
  page = "diplomats-list?l=",
  ...elem
) {
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      encrypt = ajax.responseText;
      for (let index = 0; index < elem.length; index++) {
        $(elem[index]).click(function () {
          window.location.href = page + encrypt;
        });
      }
    }
  };
  ajax.send("get_encryption=" + strToEncrypt);
}

function displayCLsession() {
  // FIXME: delete this section
  var ajax = ajaxObj("POST", "includes/ajax_calls.php");
  ajax.onreadystatechange = () => {
    if (ajaxReturn(ajax) == true) {
      console.log(ajax.responseText);
    }
  };
  ajax.send("get_cl_session=ok");
}
function hideCards() {
  $("#province-card").hide();
  $("#district-card").hide();
  $("#sector-card").hide();
  $("#cell-card").hide();
  $("#village-card").hide();
}

// configure dashboard statitics
if (!document.getElementById("summaryHolder")) {
} else {
  configureDashboard();
}
$(".close-main-report-area").click(function (e) {
  e.preventDefault();
  $(".main-report-area").css("left", "-100%");
});

$(".show-main-report-area").click(function (e) {
  e.preventDefault();
  $(".main-report-area").css("left", "0");
});
let timeouts = null;
function loadSecurityData() {
  clearTimeout(timeouts);
  $("#loader").text("Tegereza ...");
  timeouts = setTimeout(() => {
    $.get("ajax/securityData", function (data) {
      $("#loader").remove();
      $("#securityData").html(data);
    });
  }, 500);
}
