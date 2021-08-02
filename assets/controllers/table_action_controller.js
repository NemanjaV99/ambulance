import { Controller } from "stimulus"

export default class extends Controller {

    static targets = ['deleteBtn']
  
    appendToDelete() {

        let patient = this.deleteBtnTarget.getAttribute('data-patient');

        let form = document.getElementById('deleteForm')

        if (form.querySelector("input[type='hidden']")) {
            form.querySelector("input[type='hidden']").remove();
        }

        let input = document.createElement('input');
        input.setAttribute('type', 'hidden');
        input.setAttribute('name', 'identity');
        input.setAttribute('value', patient);

        form.appendChild(input);
    }
}