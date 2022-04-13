'use strict';

class WikiNavigation {
    dd = null;
    onSelectWiki = null;

    constructor(container, onSelectWiki, onClickAddPage){
        let self = this;
        this.container = container;
        this.onSelectWiki = onSelectWiki;
        this.onClickAddPage = onClickAddPage;

        let wikiSelector = container.getElementsByTagName('select')[0];

        let appNavigationMenu = container.getElementsByClassName('app-navigation-entry-menu')[0];
        let menuEntry = {
                addPage:appNavigationMenu.querySelector('[data-id="addPage"]'),
                add:appNavigationMenu.querySelector('[data-id="add"]'),
                rename:appNavigationMenu.querySelector('[data-id="rename"]'),
                delete:appNavigationMenu.querySelector('[data-id="delete"]')
        };
        this.dd = new WikiDropdownHelper(wikiSelector, id=>{
            menuEntry.addPage.disabled = (id==0);
            menuEntry.rename.disabled = (id==0);
            menuEntry.delete.disabled = (id==0);
            self.onSelectWiki(id);
         } );
        this.loadWikis();

        menuEntry.addPage.addEventListener('click', e=>self.onClickAddPage(e) );
        menuEntry.add.addEventListener('click', ()=>self.wikiChooseFolder() );
        menuEntry.rename.addEventListener('click', ()=>self.wikiRename() );
        menuEntry.delete.addEventListener('click', ()=>self.wikiDelete() );
    }

    wikiDelete() {
        let self=this;
        let wiki = this.dd.get();
        OC.dialogs.confirm( t(appName, 'Delete the wiki {text}?', wiki),
                            t(appName, 'Delete Wiki'),
                            (ok)=>{
                                if ( ok ) {
                                    var baseUrl = OC.generateUrl('/apps/mywiki/wikis');
                                    $.ajax({
                                        url: baseUrl+'/'+wiki.value,
                                        type: 'DELETE',
                                        contentType: 'application/json',
                                        data: JSON.stringify({removeFiles:false})
                                    }).done(function (response) {
                                        console.info('JDG :: WikiNavigation.wikiDelete()', response);
                                        self.dd.set('').delete(wiki.value);
                                    }).fail(function (response, code) {
                                        OC.dialogs.alert('Error', t(appName,'Error deleting wiki {text}', wiki));
                                        console.error('JDG :: WikiNavigation.wikiDelete()', response);
                                    }); 
                                } 
                            },
                            false
                        );
    }

    wikiRename() {
        const self=this;
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
                                            url: baseUrl+'/'+wiki.value,
                                            type: 'PUT',
                                            contentType: 'application/json',
                                            data: JSON.stringify({title:value})
                                        }).done(function (response) {
                                            console.info('JDG :: WikiNavigation.wikiRename()', response);
                                            self.dd.rename(response.id, response.title);
                                        }).fail(function (response, code) {
                                            OC.dialogs.alert('Error', t(appName,'Error renaming wiki'));
                                            console.error('JDG :: WikiNavigation.wikiRename()', response);
                                        });                                        
                                    }
                                }
                            },
                            false,
                            t(appName, 'New name'),
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
            console.info('JDG :: WikiNavigation.loadWikis()', response);
            self.dd.clear().add('','');
            response.forEach( x=>self.dd.add(x.title, x.id) );
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'Error getting the list of wikis'));
            console.error('JDG :: WikiNavigation.loadWikis()', response);
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
            console.info('JDG :: WikiNavigation.wikiAdd("'+folderPath+'","'+title+'")', response);
            if ( response.id>0 ) {
                self.dd.add(response.title, response.id, true);
            }
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'It has not been possible to add the new wiki'));
            console.error('JDG :: WikiNavigation.wikiAdd("'+folderPath+'","'+title+'")', response);
        });
    }
}