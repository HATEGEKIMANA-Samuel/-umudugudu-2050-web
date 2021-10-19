$(document).ready(() => {
  function hideAllOptions () {
    $('#province-group').hide()
    $('#district-group').hide()
    $('#sector-group').hide()
    $('#cell-group').hide()
    $('#village-group').hide()
    $('#location-text').show()

    $('#current_province').prop('required', false)
    $('#current_district').prop('required', false)
    $('#current_sectors').prop('required', false)
    $('#current_cells').prop('required', false)
    $('#current_villages').prop('required', false)

    $('#current_province').prop('selectedIndex', 0)
    $('#current_district').prop('selectedIndex', 0)
    $('#current_sectors').prop('selectedIndex', 0)
    $('#current_cells').prop('selectedIndex', 0)
    $('#current_villages').prop('selectedIndex', 0)
  }

  $('#level').change(() => {
    if ($('#level option:selected').val() == 7 || $('#level option:selected').val() == 1) { // HQ Minaloc, Admin
      hideAllOptions()
      $('#location-text').hide()
    } else if ($('#level option:selected').val() == 6) { // Province
      hideAllOptions()
      $('#province-group').show()
      $('#current_province').prop('required', true)
    } else if ($('#level option:selected').val() == 5) { // District
      hideAllOptions()
      $('#province-group').show()
      $('#district-group').show()
      $('#current_province').prop('required', true)
      $('#current_district').prop('required', true)
    } else if ($('#level option:selected').val() == 4) { // Sector
      hideAllOptions()
      $('#province-group').show()
      $('#district-group').show()
      $('#sector-group').show()
      $('#current_province').prop('required', true)
      $('#current_district').prop('required', true)
      $('#current_sectors').prop('required', true)
    } else if ($('#level option:selected').val() == 3) { // Cell
      hideAllOptions()
      $('#province-group').show()
      $('#district-group').show()
      $('#sector-group').show()
      $('#cell-group').show()
      $('#current_province').prop('required', true)
      $('#current_district').prop('required', true)
      $('#current_sectors').prop('required', true)
      $('#current_cells').prop('required', true)
    } else if ($('#level option:selected').val() == 2) { // Village
      hideAllOptions()
      $('#province-group').show()
      $('#district-group').show()
      $('#sector-group').show()
      $('#cell-group').show()
      $('#village-group').show()
      $('#current_province').prop('required', true)
      $('#current_district').prop('required', true)
      $('#current_sectors').prop('required', true)
      $('#current_cells').prop('required', true)
      $('#current_villages').prop('required', true)
    }
  })
})
