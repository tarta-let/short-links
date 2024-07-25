const currentUrl = window.location.href;
const url = {
    url: currentUrl
};

const btn_submit = document.querySelector('.button-submit');
const btn_copy = document.querySelector('.button-copy');
var link = document.querySelector('.input-link');
var copyText = document.querySelector('.input-copy');

controller(url, response => {
    if(response !== null){
        window.location.href = response;
    }
})

function controller(body, cb) {
    const request = new XMLHttpRequest();
    request.open('POST', 'controller.php');
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.addEventListener('load', () => {
        const response = JSON.parse(request.responseText);

        cb(response);
    });

    request.send(JSON.stringify(body));
}

btn_submit.addEventListener('click', () => {
    const newLink = {
        link: link.value
    };
    controller(newLink, response => {
        link.value = '';
        copyText.value = response;
    })
})

btn_copy.addEventListener("click", () => {
    let textarea = document.createElement('textarea');
	textarea.id = 'temp';
	textarea.style.height = 0;
	document.body.appendChild(textarea);
	textarea.value = copyText.value;
	let selector = document.querySelector('#temp');
	selector.select();
	document.execCommand('copy');
	document.body.removeChild(textarea);
})