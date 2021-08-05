import { Controller } from "stimulus"

export default class extends Controller {

    static targets = ['link'];

    connect() {

        // Go through the navigation links, and based on the href path and current page location apply active nav item class
        this.linkTargets.forEach(function(element, index){
            
            if (window.location.href.includes(element.href)) {

                element.className += ' navigation__item--active'
            }
    
        });
    }
}