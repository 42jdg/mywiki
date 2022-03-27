const appName = 'MyWiki';

class WikiPages {
    constructor(container){
        this.wikiId = null;
    }
    load(wikiId) {
        console.info('JDG :: Loading wiki', wikiId );
        this.wikiId = wikiId;
    }
    getWikiId() {
        return this.wikiId;
    }
    add(parentPageId, title) {

    }
    delete() {

    }
    rename() {

    }
}
class WikiEditor {
    load(wikiId, wikiPageId) {
        console.info(`JDG :: Loading Wiki ${wikiId}/${wikiPageId}`);

    }
}

var MyWiki = MyWiki || {};

(function(window, $, exports, undefined) {
    'use strict';

    let wikiNavigation = new WikiNavigation(document.querySelector('li[data-id="wikis"]'), onSelectWiki);
    let wikiPages = new WikiPages(document.querySelector('li[data-id="pages"]'), onSelectWikiPage);
    function onSelectWiki(wikiId) {
        console.info(`JDG :: WikiList selected ${wikiId}` );
        if ( wikiId > 0 ) {
            wikiPages.load(wikiId);
        }
    }
    function onSelectWikiPage(wikiPageId) {
        console.info(`JDG :: WikiPage selected ${wikiPageId}` );
        if ( wikiPageId > 0 ) {
            // wikiEditor.load(wikiPage.getWikiId(), wikiPageId );
        }
    }

    // ---------------------------------------------------------------------------------
    $(`#${appName}-test`).on('click',test);
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
    // ---------------------------------------------------------------------------------

})(window, jQuery, MyWiki);





