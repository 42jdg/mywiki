'use strict';

class WikiNavigation {
    dd = null;
    onSelectWiki = null;

    constructor(container, onSelectWiki){
        let self = this;
        this.container = container;
        this.onSelectWiki = onSelectWiki;

        let wikiSelector = container.getElementsByTagName('select')[0];
        this.dd = new WikiDropdownHelper(wikiSelector);
        wikiSelector.addEventListener('change', e=>{
            if(self.onSelectWiki) {
                self.onSelectWiki(+e.target.value||0);
            }
        });
        this.loadWikis();

        // Popup menu
        let appNavigationMenu = container.getElementsByClassName('app-navigation-entry-menu')[0];
        let button = container.querySelector('.app-navigation-entry-utils-menu-button button');
        button.addEventListener('click', ()=>appNavigationMenu.classList.toggle("open") );
        document.addEventListener('click', e=>{if(e.target!==button)appNavigationMenu.classList.remove("open");})

        appNavigationMenu.querySelector('[data-id="add"]').addEventListener('click', ()=>self.wikiChooseFolder() );
        appNavigationMenu.querySelector('[data-id="rename"]').addEventListener('click', ()=>self.wikiRename() );
        appNavigationMenu.querySelector('[data-id="delete"]').addEventListener('click', ()=>self.wikiDelete() );
    }

    wikiRename() {
        let self=this;
        OC.dialogs.prompt(
                            t(appName, 'This allow you to rename the displayed name for the selected wiki. (The folder will remain unchanged)'),
                            t(appName, 'Rename Wiki'),
                            (ok,value)=>{
                                if(ok) {
                                    value = value.trim();
                                    if(value!='') {
                                        let wiki = self.dd.get();
                                        var baseUrl = OC.generateUrl('/apps/mywiki/wikis');
                                        $.ajax({
                                            url: baseUrl,
                                            type: 'PUT',
                                            contentType: 'application/json',
                                            data: JSON.stringify({id:wiki.value, title:value})
                                        }).done(function (response) {
                                            console.info('JDG :: Wiki renamed', response);
                                            // ToDo :: Rename in the dropdown
                                        }).fail(function (response, code) {
                                            OC.dialogs.alert('Error', t(appName,'Error renaming wiki'));
                                            console.error('JDG :: Error renaming wiki', response);
                                        });                                        
                                    }
                                }
                            },
                            false,
                            t(appName, 'New name:'),
                            false
                        );
    }

    loadWikis() {
        let self = this;

        this.dd.clear().add(t(appName, 'Loading...'), '', true);

        var baseUrl = OC.generateUrl('/apps/mywiki/wikis');
        $.ajax({
            url: baseUrl,
            type: 'GET',
            contentType: 'application/json'
        }).done(function (response) {
            console.info('JDG :: Wikis loaded', response);
            self.dd.clear().add('','');
            response.forEach( x=>self.dd.add(x.title, x.id) );
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'Error getting the list of wikis'));
            console.error('JDG :: Error getting the wikis', response);
            self.dd.clear();
        });
    }    
    
    wikiChooseFolder() {
        let self = this;
        this.dd.set('');
        window.OC.dialogs.filepicker(
                                        t(appName, 'Select Wiki folder'), 
                                        (path, type) => {
                                                            if (type === OC.dialogs.FILEPICKER_TYPE_CHOOSE) {
                                                                self.wikiAdd(path, path.split('/').pop());
                                                            }
                                                        }, 
                                        false, 
                                        ['httpd/unix-directory'], 
                                        true, 
                                        OC.dialogs.FILEPICKER_TYPE_CHOOSE, 
                                        '', // Path
                                        { allowDirectoryChooser: true }
                                    );
    }

    wikiAdd(folderPath, title) {
        let self = this;

        var baseUrl = OC.generateUrl('/apps/mywiki/wikis');
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: JSON.stringify({title:title, folderPath:folderPath}),
            contentType: 'application/json'
        }).done(function (response) {
            console.info('JDG :: wikiAdd :: Wiki added', response);
            if ( response.id>0 ) {
                self.dd.add(response.title, response.id, true);
            }
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'It has not been possible to add the new wiki'));
            console.error('JDG :: wikiAdd :: Error adding the wiki', response);
        });
    }
}