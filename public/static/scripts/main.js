bootbox.loading = function() {
    return bootbox.dialog({
        closeButton: false,
        message: '<div class="progress progress-striped active" style="margin:0"><div class="progress-bar progress-bar-success" style="width:100%"></div></div>'
    });
}
bootbox.buildJson = function(form) {
    var o = {};
    var a = form.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}
jQuery("#btn-service-restart").bind('click', function() {
    var loading = bootbox.loading();
    jQuery.ajax({
        type:'GET',
        url: '/ajax/mockconsole/restart',
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

jQuery("#btn-service-start").bind('click', function() {
    var loading = bootbox.loading();
    jQuery.ajax({
        type:'GET',
        url: '/ajax/mockconsole/start',
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