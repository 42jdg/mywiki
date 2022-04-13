const appName = 'MyWiki';


class WikiEditor {
    load(wikiId, wikiPageId) {
        console.info(`JDG :: Loading Wiki ${wikiId}/${wikiPageId}`);

    }
}

var MyWiki = MyWiki || {};

(function(window, $, exports, undefined) {
    'use strict';

    // Navigation menu --------------------------------
    function appNavigationEntryMenuClose() {
        document.querySelectorAll('.app-navigation-entry-menu').forEach(e=>e.classList.remove("open"));
    }
    document.addEventListener('click', e=>{
        if (e.target.tagName === 'BUTTON' ) {
            const li = e.target.parentNode?.parentNode?.closest('li');
            if (!li) return;
            const menu = li.querySelector(".app-navigation-entry-menu");
            if (!menu) return;
            if ( menu.classList.contains("open") ) {
                menu.classList.remove("open");
            } else {
                appNavigationEntryMenuClose();        
                menu.classList.add("open");
            }
            return;
        }
        appNavigationEntryMenuClose();        
    })
    // ------------------------------------------------


    let wikiPages = new WikiPages(document.querySelector('li[data-id="pages"]').parentNode, onSelectWikiPage);
    let wikiNavigation = new WikiNavigation(document.querySelector('li[data-id="wikis"]'), 
                                wikiId => wikiPages.load(wikiId),
                                e=>wikiPages.onClickAdd(e)
                            );
    
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





