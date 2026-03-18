let slider = document.querySelector('.about-hero .slider');
let items = document.querySelectorAll('.about-hero .slider .items'); // Changed to .items to match HTML
let dots = document.querySelectorAll('.about-hero .dots li');
let prev = document.getElementById('prev');
let next = document.getElementById('next');

let active = 0; 
let lengthItems = items.length - 1;

next.onclick = function() {
    active = (active + 1 > lengthItems) ? 0 : active + 1;
    reloadSlider();
}

prev.onclick = function() {
    active = (active - 1 < 0) ? lengthItems : active - 1;
    reloadSlider();
}

let refreshSlider = setInterval(() => { next.click() }, 5000);

function reloadSlider() {
    let checkLeft = items[active].offsetLeft;
    slider.style.left = -checkLeft + 'px'; 

    let lastActiveDot = document.querySelector('.about-hero .dots li.active');
    if (lastActiveDot) lastActiveDot.classList.remove('active');
    if (dots[active]) dots[active].classList.add('active');
    
    clearInterval(refreshSlider);
    refreshSlider = setInterval(() => { next.click() }, 5000);
}

dots.forEach((li, key) => { // Fixed: ensured 'li' matches
    li.addEventListener('click', function() {
        active = key;
        reloadSlider();
    })
})