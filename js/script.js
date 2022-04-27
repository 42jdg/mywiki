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
    let wikiContent = new WikiContent(document.getElementById('app-content-wrapper'));
    let wikiPages = new WikiPages(document.querySelector('li[data-id="pages"]').parentNode, (wikiId, pageId)=>wikiContent.load(wikiId, pageId));
    let wikiNavigation = new WikiNavigation(document.querySelector('li[data-id="wikis"]'), 
                                wikiId => wikiPages.load(wikiId),
                                e=>wikiPages.onClickAdd(e)
                            );

})(window, jQuery, MyWiki);





