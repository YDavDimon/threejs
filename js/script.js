function check_switch_label(event ) {
    let labels = document.getElementsByClassName('label');
    if (on_off_label.checked==false) {
        
    for(let elem of labels) {
        elem.innerHTML='';
    }

    }

    
}