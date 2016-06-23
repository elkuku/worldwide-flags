var baseUri = window.location.href.split(/[?#]/)[0];

$('#jstree').jstree({
    'plugins' : [ 'wholerow', 'checkbox', 'sort' ],
    'core' : {
        'dblclick_toggle': false,
        'data': {
            'url': function (node) {
                return node.id === '#' ?
                    'flags.json' :
                    'flags_children.json';
            },
            'data': function (node) {
                return {'id': node.id};
            }
        }
    }
});

$('#createit').click(function() {
    var selectedItems = $('#jstree').jstree(true).get_selected(true);
    var container = $('#selectionContainer');
    var errorMessage = $('#errorMessage');
    container.html('');
    errorMessage.html('');
    var items = [];

    $('#selectionMessage').html('You selected ' + selectedItems.length + ' items:<br />');

    selectedItems.forEach(function(item) {
        if(!item.icon) {
            // If the item has an icon, it's a directory...
            container.append(item.id + "<br />");
            items.push(item.id);
        }
    });

    var flagString = '"' + items.join('" "') + '"';

    //$('#permalinkD').html(baseUri + '?flags="' + flagString);
    $('#permalink').attr('href', baseUri + '?flags="' + flagString);

    $.ajax({
        method: 'post',
        url: baseUri,
        data: {
            flags: flagString,
            action: 'build'
        },
        dataType: 'json',
        success: function (result) {
            console.log(result);
            if(result.error) {
                errorMessage.html(result.error);
            } else {
                $('#resultCss').html(result.css)
                $("#resultImage").attr('src', 'data:image/png;base64,' + result.image);
            }
        }
    });
});

$(function() {
    $('#tree-container').height($(window).height() - 90);
});

$(window).resize(function() {
    $('#tree-container').height($(window).height() - 90);
});