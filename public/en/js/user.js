var username_state = false
var validator = $('#registerfrm').validate()
var validatorUpdate = $('#updatefrm').validate()
var validatorUpdateProfile = $('#update_profile').validate()
function _ (el) {
  return document.getElementById(el)
}

function check_username () {
  var status = _('username_status')
  var username = _('username').value

  if (username.replace(/\s/g, '') == '') {
    status.innerHTML = 'Username is blank'
  } else if (username == '' || username.length < 4) {
    username_state = false
    status.innerHTML = ''
  } else {
    var ajax = ajaxObj('POST', 'userAction')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == 'taken') {
          username_state = false

          status.innerHTML =
            "<span style='color:red;'>Username taken. Please choose another one</span>"
        } else if (ajax.responseText == 'not_taken') {
          username_state = true
          status.innerHTML = ''
        }
      }
    }
    ajax.send('username=' + username + '&username_check=1')
  }
}
function check_username_update (id) {
  var username = _('username').value
  var status = _('username_status')
  var i = id
  if (username == '' || username.length < 4) {
    username_state = false
    status.innerHTML = ''
  } else {
    var ajax = ajaxObj('POST', 'userAction.php')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == 'taken') {
          username_state = false
          status.innerHTML =
            "<span style='color:red;'>Username taken. Please choose another one</span>"
        } else if (ajax.responseText == 'not_taken') {
          username_state = true
          status.innerHTML = ''
        }
      }
    }
    ajax.send('username=' + username + '&id=' + i + '&username_update=1')
  }
}

function check_firstname () {
  if (_('firstname').value.replace(/\s/g, '') == '') {
    _('firstname_status').innerHTML = 'First name is blank'
  }
}

function check_lastname () {
  if (_('lastname').value.replace(/\s/g, '') == '') {
    _('lastname_status').innerHTML = 'Last name is blank'
  }
}

function check_password () {
  if (_('password').value.replace(/\s/g, '') == '') {
    _('password_status').innerHTML = 'Password is blank'
  }
}

function register () {
  var firstname = _('firstname').value
  var lastname = _('lastname').value
  var middle = _('middlename').value
  var email = _('email').value
  var password = _('password').value
  var institution = _('institution').value
  var level = _('level').value
  var username = _('username').value
  var current_province = _('current_province').value
  var current_district = _('current_district').value
  var current_sectors = _('current_sectors').value
  var current_cells = _('current_cells').value
  var current_villages = _('current_villages').value

  $('#registerfrm').validate({
    rules: {
      email: {
        required: true,
        email: true
      }
    }
  })

  if (username_state && validator.form()) {
    var ajax = ajaxObj('POST', 'userAction')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == 'success') {
          _('registerfrm').reset()
          window.location = 'users'
        }
        console.log(ajax.responseText) // README: return result from userAction.php
      }
    }
    ajax.send(
      'username=' +
        username +
        '&firstname=' +
        firstname +
        '&middlename=' +
        middle +
        '&password=' +
        password +
        '&email=' +
        email +
        '&institution=' +
        institution +
        '&level=' +
        level +
        '&lastname=' +
        lastname +
        '&current_province=' +
        current_province +
        '&current_district=' +
        current_district +
        '&current_sectors=' +
        current_sectors +
        '&current_cells=' +
        current_cells +
        '&current_villages=' +
        current_villages +
        '&add=add'
    )
  } else {
    // _("registerfrm").reset();
    console.log('form submission failed')
  }
}
/// users location
/// get district by province
document
  .getElementById('current_province')
  .addEventListener('change', getDistrict)
function getDistrict () {
  var province = _('current_province').value
  if (province == '') {
    /// _("current_district").innerHTML = '<span style="color:red;">Please ent</span>';
  } else {
    _('current_district').innerHTML =
      '<span style="color:green;">Please wait ...</span>'
    var ajax = ajaxObj('POST', 'includes/ajax_calls.php')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        _('current_district').innerHTML = ajax.responseText
      }
    }
    ajax.send('current_province=' + province)
  }
}
// get sector by district
document
  .getElementById('current_district')
  .addEventListener('change', getSector)
function getSector () {
  var district = _('current_district').value
  if (district == '') {
    /// _("current_district").innerHTML = '<span style="color:red;">Please ent</span>';
  } else {
    _('current_sectors').innerHTML =
      '<span style="color:green;">Please wait ...</span>'
    var ajax = ajaxObj('POST', 'includes/ajax_calls.php')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        _('current_sectors').innerHTML = ajax.responseText
      }
    }
    ajax.send('current_sectors=' + district)
  }
}

// get cell by sectors
document.getElementById('current_sectors').addEventListener('change', getCells)
function getCells () {
  var district = _('current_sectors').value
  if (district == '') {
    /// _("current_district").innerHTML = '<span style="color:red;">Please ent</span>';
  } else {
    _('current_cells').innerHTML =
      '<span style="color:green;">Please wait ...</span>'
    var ajax = ajaxObj('POST', 'includes/ajax_calls.php')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        _('current_cells').innerHTML = ajax.responseText
      }
    }
    ajax.send('current_cells=' + district)
  }
}

// get village by cell
document
  .getElementById('current_cells')
  .addEventListener('change', getVillages)
function getVillages () {
  var district = _('current_cells').value
  if (district == '') {
    /// _("current_district").innerHTML = '<span style="color:red;">Please ent</span>';
  } else {
    _('current_villages').innerHTML =
      '<span style="color:green;">Please wait ...</span>'
    var ajax = ajaxObj('POST', 'includes/ajax_calls.php')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        _('current_villages').innerHTML = ajax.responseText
      }
    }
    ajax.send('current_villages=' + district)
  }
}
/// end of users location

function editUser (id) {
  username_state = true
  var firstname = _('firstname').value
  var lastname = _('lastname').value
  var middle = _('middlename').value
  var email = _('email').value
  var password = _('password').value
  var institution = _('institution').value
  var level = _('level').value
  var username = _('username').value

  if (username != '' && username_state === false) {
    _('username_status').focus()
  } else if (username_state && validatorUpdate.form()) {
    var ajax = ajaxObj('POST', 'userAction')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == 'updated') {
          swal('updated!')
        }
      }
    }
    ajax.send(
      'username=' +
        username +
        '&firstname=' +
        firstname +
        '&middlename=' +
        middle +
        '&password=' +
        password +
        '&email=' +
        email +
        '&institution=' +
        institution +
        '&level=' +
        level +
        '&lastname=' +
        lastname +
        '&id=' +
        id +
        '&edit=edit'
    )
  }
}
function ValidateEmail (inputText) {
  var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
  if (inputText.match(mailformat)) {
    return true
  } else {
    return false
  }
}

function update_profile () {
  var firstname = _('firstname').value
  var lastname = _('lastname').value
  var middle = _('middlename').value
  var email = _('email').value
  var username = _('username').value
  var id = _('hash').value
  check_username_update(id)
  if (username_state && validatorUpdateProfile.form()) {
    var ajax = ajaxObj('POST', 'userAction')
    ajax.onreadystatechange = function () {
      if (ajaxReturn(ajax) == true) {
        if (ajax.responseText == 'updated') {
          swal('updated!')
          location.reload()
        }
      }
    }
    ajax.send(
      'username=' +
        username +
        '&firstname=' +
        firstname +
        '&middlename=' +
        middle +
        '&email=' +
        email +
        '&lastname=' +
        lastname +
        '&id=' +
        id +
        '&update=update'
    )
  }
}
