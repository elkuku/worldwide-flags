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
    var items = $('#jstree').jstree(true).get_selected(true);
    items.forEach(function(item) {
        // If the item has an icon, it's a directory...
        if(!item.icon) {
            console.log(item.id);

        }
    });
});

$(function() {
    $('#tree-container').height($(window).height() - 90);
});

$(window).resize(function() {
    $('#tree-container').height($(window).height() - 90);
});