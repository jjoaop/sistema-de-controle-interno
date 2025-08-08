const menuToggle = document.getElementById('menuToggle');
const menuLinks = document.querySelector('.menu-links');

menuToggle.addEventListener('click', () => {
    menuToggle.classList.toggle('active');
    menuLinks.classList.toggle('active');
});

