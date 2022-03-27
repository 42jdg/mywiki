'use strict';

class WikiDropdownHelper {
    constructor(element) {
        this.dd = element;
    }
    clear() {
        while (this.dd.hasChildNodes()) {
            this.dd.removeChild(this.dd.firstChild);
        }
        return this;
    }
    add(text, value, set=false) {
        var option = document.createElement("option");
        option.text = text;
        option.value = value;
        this.dd.appendChild(option);
        if ( set ) {
            this.set(value);
        }
        return this;
    }
    rename(value, text) {
        this.dd.querySelector(`option[value="${value}"]`).innerHTML=text;
        return this;
    }
    get() {
        return {
                 text:this.dd.parentElement.value,
                 value:this.dd.parentElement.text
        };
    }
    set(value) {
        this.dd.parentElement.value=value;
        // this.onSelectWiki(value);
    }
}