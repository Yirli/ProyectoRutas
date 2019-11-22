$(document).ready(function(){


  // On page load: datatable
  var table_routes = $('#table_routes').dataTable({
    "ajax": "route.php?job=get_routes",
    "columns": [
      { "data": "route_id" },
      { "data": "description",   "sClass": "description" },
      { "data": "duration" },
      { "data": "cost",        "sClass": "integer" },
      { "data": "startHour",    "sClass": "integer" },
      { "data": "endHour",      "sClass": "integer" },
      { "data": "handicap",     "sClass": "integer" },
      { "data": "name" },
      { "data": "functions",      "sClass": "functions" }
    ],
    "aoColumnDefs": [
      { "bSortable": false, "aTargets": [-1] }
    ],
    "lengthMenu": [[5, 10, -1], [5, 10, "All"]],
    "oLanguage": {
      "oPaginate": {
        "sFirst":       " ",
        "sPrevious":    " ",
        "sNext":        " ",
        "sLast":        " ",
      },
      "sLengthMenu":    "Records per page: _MENU_",
      "sInfo":          "Total of _TOTAL_ records (showing _START_ to _END_)",
      "sInfoFiltered":  "(filtered from _MAX_ total records)"
    }
  });

  var table = $('#table_routes').DataTable();

  $('#table_routes').on('click', 'tr', function () {
    console.log( table.row( this ).data().route_id);   
    var $id = table.row(this).data().route_id;
    showRoute($id);
    
    //var data = table.row(this).data('id');
    //alert( 'You clicked on '+data.index()+ ' row' );
} );
  
  // On page load: form validation
  jQuery.validator.setDefaults({
    success: 'valid',
    rules: {
      fiscal_year: {
        required: true,
        min:      2000,
        max:      2025
      }
    },
    errorPlacement: function(error, element){
      error.insertBefore(element);
    },
    highlight: function(element){
      $(element).parent('.field_container').removeClass('valid').addClass('error');
    },
    unhighlight: function(element){
      $(element).parent('.field_container').addClass('valid').removeClass('error');
    }
  });
  var form_route = $('#form_route');
  form_route.validate();

  // Show message
  function show_message(message_text, message_type){
    $('#message').html('<p>' + message_text + '</p>').attr('class', message_type);
    $('#message_container').show();
    if (typeof timeout_message !== 'undefined'){
      window.clearTimeout(timeout_message);
    }
    timeout_message = setTimeout(function(){
      hide_message();
    }, 8000);
  }
  // Hide message
  function hide_message(){
    $('#message').html('').attr('class', '');
    $('#message_container').hide();
  }

  // Show loading message
  function show_loading_message(){
    $('#loading_container').show();
  }
  // Hide loading message
  function hide_loading_message(){
    $('#loading_container').hide();
  }

  // Show lightbox
  function show_lightbox(){
    $('.lightbox_bg').show();
    $('.lightbox_container').show();
  }
  // Hide lightbox
  function hide_lightbox(){
    $('.lightbox_bg').hide();
    $('.lightbox_container').hide();
  }
  // Lightbox background
  $(document).on('click', '.lightbox_bg', function(){
    hide_lightbox();
  });
  // Lightbox close button
  $(document).on('click', '.lightbox_close', function(){
    hide_lightbox();
  });
  // Escape keyboard key
  $(document).keyup(function(e){
    if (e.keyCode == 27){
      hide_lightbox();
    }
  });

  // Hide iPad keyboard
  function hide_ipad_keyboard(){
    document.activeElement.blur();
    $('input').blur();
  }

  // Add route button
  $(document).on('click', '#add_route', function(e){
    e.preventDefault();
    $('.lightbox_content h2').text('Add route');
    $('#form_route #addRoute').text('Add route');
    $('#form_route').attr('class', 'form add');
    $('#form_route').attr('data-id', '');
    $('#form_route .field_container label.error').hide();
    $('#form_route .field_container').removeClass('valid').removeClass('error');
    $('#form_route #route_id').val('');
    $('#form_route #description').val('');
    $('#form_route #duration').val('');
    $('#form_route #cost').val('');
    $('#form_route #startHour').val('');
    $('#form_route #endHour').val('');
    $('#form_route #handicap').val('');
    $('#form_route #company_id').empty();

    
    var request = $.ajax({
      url:          'route.php?job=fill_companySelect',
      cache:        false,
      dataType:     'json',
      contentType:  'application/json; charset=utf-8',
      type:         'get'
    });
    request.done(function(output){
      if (output.result == 'success'){
        for(i=0;i<output.data.length;i++)
        $('#form_route #company_id').append('<option value='+output.data[i].company_id+'>'+output.data[i].name + "</option>");
        hide_loading_message();
        show_lightbox();
      } else {
        hide_loading_message();
        show_message('Information request failed', 'error');
      }
    });
    request.fail(function(jqXHR, textStatus){
      hide_loading_message();
      show_message('Information request failed: ' + textStatus, 'error');
    });

   
    show_lightbox();
  });

  // Add route submit form
  $(document).on('submit', '#form_route.add', function(e){
    e.preventDefault();
    // Validate form
    if (form_route.valid() == true){
      // Send route information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var form_data = $('#form_route').serialize();
      var request   = $.ajax({
        url:          'route.php?job=add_route',
        cache:        false,
        data:         form_data,
        dataType:     'json',
        contentType:  'application/json; charset=utf-8',
        type:         'get'
      });
      request.done(function(output){
        if (output.result == 'success'){ 
          createRoute();
          // Reload datable
          table_routes.api().ajax.reload(function(){
            hide_loading_message();
            var description = $('#description').val();
            show_message("route '" + description + "' added successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Add request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Add request failed: ' + textStatus, 'error');
      });
    }
  });

  // Edit route button
  $(document).on('click', '.function_edit a', function(e){
    
    e.preventDefault();
    // Get route information from database
    show_loading_message();
    var id      = $(this).data('id');
    var request = $.ajax({
      url:          'route.php?job=get_route',
      cache:        false,
      data:         'id=' + id,
      dataType:     'json',
      contentType:  'application/json; charset=utf-8',
      type:         'get'
    });
    request.done(function(output){
      if (output.result == 'success'){
        showRoute(id);
        $('.lightbox_content h2').text('Edit route');
        $('#form_route #addRoute').text('Edit route');
        $('#form_route').attr('class', 'form edit');
        $('#form_route').attr('data-id', id);
        $('#form_route .field_container label.error').hide();
        $('#form_route .field_container').removeClass('valid').removeClass('error');
        $('#form_route #route_id').val(output.data[0].route_id);
        $('#form_route #description').val(output.data[0].description);
        $('#form_route #duration').val(output.data[0].duration);
        $('#form_route #cost').val(output.data[0].cost);
        $('#form_route #startHour').val(output.data[0].startHour);
        $('#form_route #endHour').val(output.data[0].endHour);
        $('#form_route #handicap').val(output.data[0].handicap);
        $('#form_route #company_id').empty();
        //fill company select 
        var request = $.ajax({
          url:          'route.php?job=fill_companySelect',
          cache:        false,
          dataType:     'json',
          contentType:  'application/json; charset=utf-8',
          type:         'get'
        });
        request.done(function(output){
          if (output.result == 'success'){
            for(i=0;i<output.data.length;i++)
            $('#form_route #company_id').append('<option value='+output.data[i].company_id+'>'+output.data[i].name + "</option>");
            hide_loading_message();
            show_lightbox();
          } else {
            hide_loading_message();
            show_message('Information request failed', 'error');
          }
        });
        request.fail(function(jqXHR, textStatus){
          hide_loading_message();
          show_message('Information request failed: ' + textStatus, 'error');
        });
///////////////////////////////////////////////////////////////////////////

        hide_loading_message();
        show_lightbox();
      } else {
        hide_loading_message();
        show_message('Information request failed', 'error');
      }
    });
    request.fail(function(jqXHR, textStatus){
      hide_loading_message();
      show_message('Information request failed: ' + textStatus, 'error');
    });
  });
  
  // Edit route submit form
  $(document).on('submit', '#form_route.edit', function(e){
    e.preventDefault();
    // Validate form
    if (form_route.valid() == true){
      // Send route information to database
      hide_ipad_keyboard();
      hide_lightbox();
      show_loading_message();
      var id        = $('#form_route').attr('data-id');
      var form_data = $('#form_route').serialize();
      var request   = $.ajax({
        url:          'route.php?job=edit_route&id=' + id,
        cache:        false,
        data:         form_data,
        dataType:     'json',
        contentType:  'application/json; charset=utf-8',
        type:         'get'
      });
      request.done(function(output){
        if (output.result == 'success'){
          updateRoute(id);
          // Reload datable
          table_routes.api().ajax.reload(function(){
            hide_loading_message();
            var route_description = $('#description').val();
            show_message("route '" + route_description + "' edited successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Edit request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Edit request failed: ' + textStatus, 'error');
      });
    }
  });
  
  // Delete route
  $(document).on('click', '.function_delete a', function(e){
    e.preventDefault();
    var route_description = $(this).data('description');
    if (confirm("Are you sure you want to delete '" + route_description + "'?")){
      show_loading_message();
      var id      = $(this).data('id');
      var request = $.ajax({
        url:          'route.php?job=delete_route&id=' + id,
        cache:        false,
        dataType:     'json',
        contentType:  'application/json; charset=utf-8',
        type:         'get'
      });
      request.done(function(output){
        if (output.result == 'success'){
          // Reload datable
          table_routes.api().ajax.reload(function(){
            hide_loading_message();
            show_message("route '" + route_description + "' deleted successfully.", 'success');
          }, true);
        } else {
          hide_loading_message();
          show_message('Delete request failed', 'error');
        }
      });
      request.fail(function(jqXHR, textStatus){
        hide_loading_message();
        show_message('Delete request failed: ' + textStatus, 'error');
      });
    }
  });

  $(document).on('click', '#addPoint', function(e){
    addPointX();
  });

});


