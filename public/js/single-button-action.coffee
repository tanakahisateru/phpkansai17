###
クリック1発でPOSTやPUTなどのリクエストを

Attributes:
data-url required
data-method optional
data-confirm optional

<button data-url="/path/to/action" data-method="DELETE" data-confirm="Are you sure?">Delete</button>

$buttons.singleButtonAction({
    // optional
    prepare: function() {
        return { ... extra params to send };
    }
});
###
$.fn.singleButtonAction = (config)->
    $(this).on 'click', (event)->
        event.preventDefault()

        exec = =>
            url = $(this).data 'url'
            method = $(this).data 'method' ? 'post'
            data = if config?.prepare? then config.prepare.call this else null

            form = $('<form action="' + url + '">')
            switch method.toLowerCase()
                when 'get'
                    form.attr 'method', 'get'
                when 'post'
                    form.attr 'method', 'post'
                else
                    form.attr 'method', 'post'
                    form.append(
                            $('<input type="hidden" name="_method">').val method.toUpperCase()
                    )
            if data?
                for k, v in data
                    form.append(
                            $('<input type="hidden">').attr('name', k).val v
                    )
            form.submit()

        if $(this).data 'confirm'
            bootbox.confirm ($(this).data 'confirm'), (ok)->
                if ok
                    exec()
        else
            exec()

###
クリック1発でGET以外のリクエストを

Attributes:
data-url required
data-method optional
data-confirm optional

<button data-url="/path/to/action" data-method="DELETE" data-confirm="Are you sure?">Delete</button>

$buttons.singleButtonAjaxAction({
    // optional
    prepare: function() {
        return { ... extra params to send };
    },
    // optional
    done: function(data) {
        // succeeded handler
    },
    // optional
    fail: function() {
        // failed handler
    }
});
###
$.fn.singleButtonAjaxAction = (config)->
    $(this).on 'click', (event)->
        event.preventDefault()

        exec = =>
            url = $(this).data 'url'
            method = $(this).data 'method'
            data = if config?.prepare? then config.prepare.call this else null

            context = {}
            context.type = method ? 'POST'
            if data?
                context.data = data

            $.ajax url, context
            .done (data)=>
                    if config?.done then config.done.call this, data
            .fail =>
                    if config?.fail then config.fail.call()

        if $(this).data 'confirm'
            bootbox.confirm ($(this).data 'confirm'), (ok)->
                if ok
                    exec()
        else
            exec()