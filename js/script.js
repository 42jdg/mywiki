var MyWiki = MyWiki || {};

(function(window, $, exports, undefined) {
    'use strict';

    $('#MyWiki-test').on('click',test);

    function test() {
        var baseUrl = OC.generateUrl('/apps/mywiki/wikis');
        $.ajax({
            url: baseUrl + '/test',
            type: 'GET',
            contentType: 'application/json'
        }).done(function (response) {
            // handle success
            $('output').html(response);
        }).fail(function (response, code) {
            // handle failure
            $('output').html('<h2>'+response.statusText+'</h2><code>'+response.responseText+'</code>');
        });
    }

/*
    // if this function or object should be global, attach it to the namespace
    exports.myGlobalFunction = function(params) {
        return params;
    };
*/
})(window, jQuery, MyWiki);





