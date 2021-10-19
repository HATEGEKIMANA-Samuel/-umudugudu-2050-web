var stickyCard = "#province-card";
var provinceId, districtId, sectorId, cellId, villageId;

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
                setTimeout(() => {
                  getCells(CLnumeric, textLocation);
                }, 100);
              } else if (locationArray.length == 4) {
                getSectors(districtCode, CLtext.split(" / ")[2]);
                setTimeout(() => {
                  getCells(sectorCode, CLtext.split(" / ")[3]);
                }, 100);
                setTimeout(() => {
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
                setTimeout(() => {
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
                setTimeout(() => {
                  getSectors(CLnumeric, textLocation);
                }, 100);
              } else if (locationArray.length == 3) {
                getDistricts(locationArray[0], CLtext.split(" / ")[1]);
                setTimeout(() => {
                  getSectors(districtCode, CLtext.split(" / ")[2]);
                }, 100);
                setTimeout(() => {
                  getCells(CLnumeric, textLocation);
                }, 200);
              } else if (locationArray.length == 4) {
                getDistricts(locationArray[0], CLtext.split(" / ")[1]);
                setTimeout(() => {
                  getSectors(districtCode, CLtext.split(" / ")[2]);
                }, 100);
                setTimeout(() => {
                  getCells(sectorCode, CLtext.split(" / ")[3]);
                }, 200);
                setTimeout(() => {
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
          html += `<tr class="none-ibiro"><td class="text-left fs-14" onclick="getDistricts(${
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
          html += `<tr class="none-ibiro"><td class="text-left fs-14" onclick="getSectors('${province}#${
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
          html += `<tr class="none-ibiro"><td class="text-left fs-14" onclick="getCells('${district}#${
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
      $("#family-count").text(stats.split(",")[0]);
      $("#member-count").text(stats.split(",")[1]);

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
          html += `<tr class="none-ibiro"><td class="text-left fs-14" onclick="getVillages('${sector}#${
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
      $("#family-count").text(stats.split(",")[0]);
      $("#member-count").text(stats.split(",")[1]);

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
      $("#family-count").text(stats.split(",")[0]);
      $("#member-count").text(stats.split(",")[1]);

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
configureDashboard();
// if (!document.getElementById("summaryHolder")) {
// } else {

// }

$(".close-main-report-area").click(function (e) {
  e.preventDefault();
  $(".main-report-area").css("left", "-100%");
});

$(".show-main-report-area").click(function (e) {
  e.preventDefault();
  $(".main-report-area").css("left", "0");
});
