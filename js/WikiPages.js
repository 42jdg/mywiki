'use strict';

class WikiPages {
    /*
    * The container is the <ul> for the navigation panel
    */
    constructor(container) {
        this.ul = container;
        this.wikiId = null;
    }

    clear() {
        this.ul.querySelectorAll('[data-wiki-id]').forEach( x=>x.remove() );
    }

    getWikiId() {
        return this.wikiId;
    }

    load(wikiId) {
        const self = this;
        console.info('JDG :: Loading wiki', self.getWikiId() );
        this.wikiId = null;
        if (wikiId<=0) {
            this.clear();
            return;
        }

        var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+wikiId);
        $.ajax({
            url: baseUrl,
            type: 'GET',
            contentType: 'application/json'
        }).done(function (response) {
            console.info('JDG :: WikiPages.load('+wikiId+')', response);
            self.wikiId = wikiId;
            self.draw(response.pages, 0, response.pages[0].id);
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'Error loading wiki('+wikiId+')'));
            console.error('JDG :: WikiPages.load('+wikiId+')', response);
        }); 
    }


    draw(pages, lvl=0, pid=0) {
        let self=this;
        if (lvl==0) {
            this.clear();
        }

        // pages = [{"id":880,"pid":0,"title":"WikiTest","sort":1},...]
        pages
            .filter( x=>x.pid==pid )
            .sort( (a,b)=>a.sort - b.sort )
            .forEach( x => {
                self.treeAdd(x.pid, x.id, x.title);
                self.draw(pages, lvl+1, x.id);
        });

        if (lvl==0) {
            this.ul.querySelectorAll('button[data-id="add"]').forEach(x => x.addEventListener('click', e=>self.onClickAdd(e)) );
            this.ul.querySelectorAll('button[data-id="delete"]').forEach(x => x.addEventListener('click', e=>self.onClickDelete(e)) );
            this.ul.querySelectorAll('button[data-id="rename"]').forEach(x => x.addEventListener('click', e=>self.onClickEdit(e)) );
            this.ul.querySelectorAll('.icon-close').forEach(x => x.addEventListener('click', e=>self.onClickClose(e)) );
            this.ul.querySelectorAll('.icon-checkmark').forEach(x => x.addEventListener('click', e=>self.onClickRename(e)) );
        }
    }

    // -----------------------------------------------------------------------------------------
    onClickEdit(e) {
        const li = e.target.closest("li[data-wiki-id]");
        li.querySelector("input").value = li.querySelector("a").innerText; 
        li.classList.add("editing");
    }
    onClickClose(e) {
        const li = e.target.closest("li[data-wiki-id]");
        li.classList.remove("editing");
    }
    onClickRename(e) {
        const li = e.target.closest("li[data-wiki-id]");
        li.classList.remove("editing");

        let pageId = li.dataset.wikiId;
        let value = li.querySelector('input').value;
        this.rename(pageId, value);
    }

    onClickAdd(e) {
        const li = e.target.closest("li[data-wiki-id]");
        this.newPage(li?li.dataset.wikiId:0);
    }

    onClickDelete(e) {
        const li = e.target.closest("li[data-wiki-id]");
        let pageId = li.dataset.wikiId;
        let pageTitle = li.querySelector('a').innerHTML;

        OC.dialogs.confirm( t(appName, 'Delete the wiki page "{title}"?', {title:pageTitle}),
                            t(appName, 'Delete Wiki Page'),
                            (ok)=>{        
                                if ( ok ) {
                                    self.delete(pageId);
                                }
                            },
                            false
                        );

    }

    newPage(pid) {
        const self = this;
        OC.dialogs.prompt(
            t(appName, 'Please type a title for the new page'),
            t(appName, 'New Page'),
            (ok,value)=>{
                if(ok) {
                    value = value.trim();
                    if(value!='') {
                        self.add(pid, value);                                    
                    }
                }
            },
            false,
            t(appName, 'Page Title'),
            false
        );
    }


    // -----------------------------------------------------------------------------------------
    treeDelete(pageId) {
        const x = this.ul.querySelector(`[data-wiki-id="${pageId}"]`);
        const pid = x.dataset.pid;
        x.parentNode.remove(x);
        this.treeDeleteChildren(pageId);
    }
    treeDeleteChildren(pageId) {
        const self = this;
        this.ul
            .querySelectorAll(`[data-pid="${pageId}"]`)
            .forEach(x=>{
                        self.treeDeleteBranch( x.dataset.wikiId );
                        x.parentNode.remove(x);
                    }
                );
    }

    treeRename(pageId, title) {
        this.ul.querySelector(`[data-wiki-id="${pageId}"] a`).innerHTML = title;
    }
    
    treeAdd(pid, pageId, title) {
        let lvl = 0;        
        let nextNode, lastNode, parent = this.ul.querySelector(`[data-wiki-id="${pid}"]`);
        if ( parent===null ) {
            lastNode = this.ul.lastChild;
        } else {
            lvl = (+parent.dataset.lvl + 1);
            nextNode = parent;
            do {
                lastNode = nextNode;
                nextNode = lastNode.nextSibling;
            } while(nextNode && nextNode.dataset.pid!=parent.dataset.pid);
        }


        const bullet = ' - ';
        let li = document.createElement("li");
        // li.classList.add("editing");
        li.dataset.wikiId = pageId;
        li.dataset.pid = pid||this.wikiId;
        li.dataset.lvl = lvl;
        li.innerHTML = `<a href="#">${bullet.repeat(lvl)} ${title}</a>
        <div class="app-navigation-entry-utils">
            <ul>
                <li class="app-navigation-entry-utils-menu-button">
                    <button></button>
                </li>
            </ul>
        </div>
        <div class="app-navigation-entry-edit">
            <div data-form>
                <input type="text" value="">
                <input type="submit" value="" class="icon-close">
                <input type="submit" value="" class="icon-checkmark">
            </div>
        </div>
		<div class="app-navigation-entry-menu">
		<ul>
            <li>
                <button data-id="openFolder" class="icon-folder">Open Folder</button>
            </li>
			<li>
				<button data-id="add" class="icon-add">Add Page</button>
			</li>
			<li>
                <button data-id="rename" class="icon-rename">Rename Page</button>
			</li>
			<li>
				<button data-id="delete" class="icon-delete">Delete Page</button>
			</li>
		</ul>
		</div>        
        `;

        lastNode.parentNode.insertBefore(li, lastNode.nextSibling)
    }

    // -----------------------------------------------------------------------------------------
    delete(pageId) {
        const self = this;
        console.info(`WikiPages.delete("${this.wikiId}-${pageId}")`);
            var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+this.wikiId);
            $.ajax({
                url: baseUrl+'/'+pageId,
                type: 'DELETE',
                contentType: 'application/json'
            }).done(function (response) {
                console.info(`WikiPages.delete("${this.wikiId}-${pageId}")`, response);
                self.treeDelete(pageId);
            }).fail(function (response, code) {
                OC.dialogs.alert('Error', t(appName,'Error deleting wiki {text}', wiki));
                console.error(`WikiPages.delete("${this.wikiId}-${pageId}")`, response);
            }); 
    } 

    rename(pageId, title) {
        const self = this;
        console.info(`WikiPages.rename("${this.wikiId}-${pageId}","${title}")`);
        var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+this.wikiId);
        $.ajax({
            url: baseUrl+'/'+pageId,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({title:title, content:null})
        }).done(function (response) {
            console.info(`WikiPages.rename("${self.wikiId}-${pageId}","${title}")`, response);
            self.treeRename(pageId, title);
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,`Error renaming wiki page ${self.wikiId}-${pageId}`));
            console.error(`WikiPages.rename("${self.wikiId}-${pageId}","${title}")`, response);
        }); 
    }

    add(pid, title) {
        const self = this;
        console.info(`WikiPages.add("${this.wikiId}-${pid}","${title}")`);
        var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+this.wikiId);
        $.ajax({
            url: baseUrl,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({pid:pid, title:title, content:null})
        }).done(function (response) {
            console.info(`WikiPages.add("${self.wikiId}-${pid}","${title}")`, response);
            if ( response.pageId > 0 ) {
                self.treeAdd(pid, response.pageId, title);
            }
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,`Error adding wiki page "${self.wikiId}-${pid}": "${title}"`));
            console.error(`WikiPages.add("${self.wikiId}-${pid}","${title}")`, response);
        }); 
    }
}