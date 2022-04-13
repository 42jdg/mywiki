'use strict';

class WikiDropdownHelper {
    constructor(element, onChange) {
        this.onSelectWiki = onChange;
        this.dd = element;
        this.dd.addEventListener('change', e=>onChange(+e.target.value||0));
    }
    clear() {
        while (this.dd.hasChildNodes()) {
            this.dd.removeChild(this.dd.firstChild);
        }
        return this;
    }
    add(text, value, set=false) {
        if (this.find(value)<=0) {
            var option = document.createElement("option");
            option.text = text;
            option.value = value;
            this.dd.appendChild(option);
        }
        if ( set ) {
            this.set(value);
        }
        return this;
    }
    find(value) {
        return Array.from(this.dd.options).findIndex(option=>option.value==value);
    }
    delete(value) {
        let index = this.find(value);
        if (index>0) {
            this.dd.remove(index);  
        }
        return this;
    }
    rename(value, newText) {
        this.dd.querySelector(`option[value="${value}"]`).innerHTML=newText;
        return this;
    }
    get() {
        return {
                 text:this.dd.options[this.dd.selectedIndex].innerHTML,
                 value:this.dd.value
        };
    }
    set(value) {
        this.dd.value=value;
        this.onSelectWiki(value);
        return this;
    }
}