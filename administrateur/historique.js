const modeToggle = document.getElementById('modeToggle');
const modeIcon = document.getElementById('modeIcon');
const currentMode = localStorage.getItem('theme') || 'light';

// Ajout de la classe mode initial
document.body.classList.add(`${currentMode}-mode`);
if (modeIcon) {
    modeIcon.classList.add(currentMode === 'light' ? 'fa-sun' : 'fa-moon');
}

if (modeToggle) {
    modeToggle.addEventListener('click', () => {
        const isDarkMode = document.body.classList.contains('dark-mode');
        if (isDarkMode) {
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
            if (modeIcon) {
                modeIcon.classList.remove('fa-moon');
                modeIcon.classList.add('fa-sun');
            }
            localStorage.setItem('theme', 'light');
        } else {
            document.body.classList.remove('light-mode');
            document.body.classList.add('dark-mode');
            if (modeIcon) {
                modeIcon.classList.remove('fa-sun');
                modeIcon.classList.add('fa-moon');
            }
            localStorage.setItem('theme', 'dark');
        }
    });
}

function toggleMenu() {
    const menu = document.querySelector('.menu');
    const menuIcon = document.querySelector('.menu-icon');
    const menuI = document.querySelector('.menu-i');
    const menuIc = document.querySelector('.menu-ic');

    if (menu) {
        menu.classList.toggle('open');
    }

    if (menuI && menuIc) {
        menuI.style.display = 'inline-block';  
        menuIc.style.display = 'none'; 
    }
}

function closeMenu(event) {
    const menu = document.querySelector('.menu');

    if (menu && (!event.relatedTarget || !menu.contains(event.relatedTarget))) {
        menu.classList.remove('open');
    }
}
