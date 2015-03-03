
jQuery("#btn-add-request-query").bind('click', function() {
    var $group = jQuery("<div />").addClass("form-group");
    var $input_key = jQuery('<input type="text" name="request_query_key[]" placeholder="Key" />').addClass("form-control");
    var $input_value = jQuery('<input type="text" name="request_query_value[]" placeholder="Value" />').addClass("form-control");
    $group.append( jQuery("<div />").addClass("col-sm-2") );
    $group.append( jQuery("<div />").addClass("col-sm-2").append($input_key) );
    $group.append( jQuery("<div />").addClass("col-sm-4").append($input_value) );
    jQuery("#div-request-query").after($group);
});


jQuery("#btn-add-request-post").bind('click', function() {
    var $group = jQuery("<div />").addClass("form-group");
    var $input_key = jQuery('<input type="text" name="request_post_key[]" placeholder="Key" />').addClass("form-control");
    var $input_value = jQuery('<input type="text" name="request_post_value[]" placeholder="Value" />').addClass("form-control");
    $group.append( jQuery("<div />").addClass("col-sm-2") );
    $group.append( jQuery("<div />").addClass("col-sm-2").append($input_key) );
    $group.append( jQuery("<div />").addClass("col-sm-4").append($input_value) );
    jQuery("#div-request-post").after($group);
});

jQuery("#btn-add-response-header").bind('click', function() {
    var $group = jQuery("<div />").addClass("form-group");
    var $input_key = jQuery('<input type="text" name="response_header_key[]" placeholder="Key" />').addClass("form-control");
    var $input_value = jQuery('<input type="text" name="response_header_value[]" placeholder="Value" />').addClass("form-control");
    $group.append( jQuery("<div />").addClass("col-sm-2") );
    $group.append( jQuery("<div />").addClass("col-sm-2").append($input_key) );
    $group.append( jQuery("<div />").addClass("col-sm-4").append($input_value) );
    jQuery("#div-response-header").after($group);
});

jQuery("#btn-submit-create").bind('click', function() {
    var loading = bootbox.loading();
    jQuery.ajax({
        type:'POST',
        data: bootbox.buildJson( jQuery('#form-mock') ),
        url: '/ajax/mockhandler/create',
        dataType: 'json',
        success: function(response) {
            if (0 == response.code) {
                bootbox.alert(response.message, function() {
                    window.location.href='/mock/list?id=' + response.data.uri_id;
                });
            } else {
                bootbox.alert(response.message);
            }
        },
        error: function() {
            bootbox.alert('Service 500');
        },
        complete:function() {
            loading.modal('hide');
        }
    });
});

jQuery("#btn-submit-save").bind('click', function() {
    var loading = bootbox.loading();
    jQuery.ajax({
        type:'POST',
        data: bootbox.buildJson( jQuery('#form-mock') ),
        url: '/ajax/mockhandler/save',
        dataType: 'json',
        success: function(response) {
            if (0 == response.code) {
                bootbox.alert(response.message, function() {
                    window.location.reload();
                });
            } else {
                bootbox.alert(response.message);
            }
        },
        error: function() {
            bootbox.alert('Service 500');
        },
        complete:function() {
            loading.modal('hide');
        }
    });
});