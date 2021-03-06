'use strict';

class WikiContent {
    waitSecondsToAutoSave = 5000;

    constructor(container) {
        const self = this;

        this.container = container;
        this.textarea = document.createElement('TEXTAREA');
        this.container.innerHTML = '';
        this.container.append(this.textarea);

        this._mde_init();
        this.clear();
    }

    // ToDo :: Open to other alternatives
    // ----------------------------------------------------------------------------------
    _mde_init() {
        const self = this;
        const rect = document.getElementById('app').getBoundingClientRect();
        const height = rect.height - rect.top*2;

        // https://github.com/Ionaru/easy-markdown-editor
        this.mde = new EasyMDE({ 
                                    autoDownloadFontAwesome: false,
                                    autofocus:true,
                                    autosave: {
                                                enabled:true,
                                                delay: 10,
                                                uniqueId: appName
                                    },
                                    element: this.textarea,
                                    hideIcons:[],
                                    insertTexts:{
                                        image:['![](', '/index.php/core/preview?fileId=XXXX&x=3840&y=2160&a=true)']
                                    },
                                    minHeight:height+"px",
                                    maxHeight:height+"px",
                                    sideBySideFullscreen: false,
                                    spellChecker: false,
                                    status:false,
                                    forceSync:true,
                                });
        this.mde.toggleSideBySide();

        this.timeout = null;
        this.mde.codemirror.on("change", (instance, changeObj) => {
            if(self.loading) return;

            console.log(changeObj);

            var event = new CustomEvent("myWiki::change", {detail:{ wikiId:self.wikiId,pageId:self.pageId }});
            document.dispatchEvent(event);

            clearTimeout(self.timeout);
            self.timeout = setTimeout(()=>self._mde_save(), this.waitSecondsToAutoSave);
        });
    }
    _mde_save() {
        clearTimeout(this.timeout);
        this.timeout = null;
        this.save(this.mde.value());
    }
    _mde_set(content) {
        this.loading = true;
        this.mde.clearAutosavedValue();
        this.mde.value(content);
        this.loading = false;
    }
    _mde_get() {
        return this.mde.value();
    }
    _mde_saved() {
        return this.timeout === null;
    }
    // ----------------------------------------------------------------------------------




    clear() {
        this.container.style.display="none";
        this.wikiId = null;
        this.pageId = null;
//        this._mde_set('');
    }
    set(content) {
        this.container.style.display="block";
        this._mde_set(content);
    }
    get() {
        return this._mde_get();
    }

    load(wikiId, pageId) {
        const self = this;
        console.info(`JDG :: Loading wiki page ${wikiId}-${pageId}` );

        if (!this._mde_saved()) {
            // ToDo :: we should wait until be sure the page could be save
            this._mde_save();
        }

        this.clear();
        if (wikiId<=0 || pageId<=0) {
            return;
        }

        var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+wikiId);
        $.ajax({
            url: baseUrl+'/'+pageId,
            type: 'GET',
            contentType: 'application/json'
        }).done(function (response) {
            console.info(`JDG :: WikiContent.load(${wikiId}, ${pageId})`, response);
            self.wikiId = wikiId;
            self.pageId = pageId;
            self.set(response.content);
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'Error loading wiki page({wikiId}, {pageId})',{wikiId:wikiId,pageId:pageId}));
            console.error(`JDG :: WikiContent.load(${wikiId}, ${pageId})`, response);
        }); 
    }

    save(content) {
        const wikiId = this.wikiId;
        const pageId = this.pageId;

        console.info(`JDG :: Saving wiki page ${wikiId}-${pageId}`);
        if (wikiId<=0 || pageId<=0) {
            return;
        }

        var baseUrl = OC.generateUrl('/apps/mywiki/wiki/'+wikiId);
        $.ajax({
            url: baseUrl+'/'+pageId,
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({title:null, content:content})
        }).done(function (response) {
            console.info(`JDG :: WikiContent.save(${wikiId}, ${pageId})`, response);
            var event = new CustomEvent("myWiki::saved", {detail:{ wikiId:wikiId,pageId:pageId }});
            document.dispatchEvent(event);
        }).fail(function (response, code) {
            OC.dialogs.alert('Error', t(appName,'Error saving wiki page({wikiId}, {pageId})',{wikiId:wikiId,pageId:pageId}));
            console.error(`JDG :: WikiContent.save(${wikiId}, ${pageId})`, response);
        }); 
    }
}