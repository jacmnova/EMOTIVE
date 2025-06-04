
<div class="card">
    <div class="p-3 control-sidebar-content">
        <h5>Personalizar Layout</h5>
        <hr class="mb-2">

        <div class="mb-2">
            <input type="checkbox" id="toggleDarkMode" class="mr-1">
            <label for="toggleDarkMode">Modo Escuro</label>
        </div>

        <div class="mb-2">
            <input type="checkbox" id="toggleSidebarMini" class="mr-1">
            <label for="toggleSidebarMini">Sidebar Mini</label>
        </div>

        <div class="mb-2">
            <input type="checkbox" id="toggleSidebarCollapse" class="mr-1">
            <label for="toggleSidebarCollapse">Recolher Sidebar</label>
        </div>

        <div class="mb-3">
            <label>Cor da Navbar</label>
            <input type="color" id="navbarCustomColor" class="form-control form-control-color" value="#3c8dbc">
            <label class="mt-1">Cor da fonte da Navbar</label>
            <input type="color" id="navbarTextColor" class="form-control form-control-color" value="#ffffff">
        </div>

        <div class="mb-3">
            <label>Cor da Sidebar</label>
            <input type="color" id="sidebarCustomColor" class="form-control form-control-color" value="#343a40">
            <label class="mt-1">Cor da fonte da Sidebar</label>
            <input type="color" id="sidebarTextColor" class="form-control form-control-color" value="#ffffff">
        </div>

        <div class="mb-3">
            <label>Cor do Brand</label>
            <input type="color" id="brandCustomColor" class="form-control form-control-color" value="#1f2d3d">
            <label class="mt-1">Cor da fonte do Brand</label>
            <input type="color" id="brandTextColor" class="form-control form-control-color" value="#ffffff">
        </div>

        <div class="mb-3">
            <input type="checkbox" id="toggleFooterFixed" class="mr-1">
            <label for="toggleFooterFixed">Rodapé Fixo</label>
        </div>


        <hr class="mb-2">

        <div class="mb-3">
            <label for="fontSelector">Fonte do Sistema</label>
            <select id="fontSelector" class="form-control">
                <option value="">Padrão (Sans-serif)</option>
                <option value="Roboto, sans-serif">Roboto</option>
                <option value="'Open Sans', sans-serif">Open Sans</option>
                <option value="'Lato', sans-serif">Lato</option>
                <option value="'Poppins', sans-serif">Poppins</option>
                <option value="'Montserrat', sans-serif">Montserrat</option>
                <option value="'Raleway', sans-serif">Raleway</option>
                <option value="'Nunito', sans-serif">Nunito</option>
                <option value="'Merriweather', serif">Merriweather</option>
                <option value="'Playfair Display', serif">Playfair Display</option>
                <option value="'Courier New', monospace">Courier New</option>
                <option value="'Fira Code', monospace">Fira Code</option>
            </select>
        </div>

        <hr class="mb-2">
        
        <button class="btn btn-sm btn-danger w-100 mb-3" id="resetLayout">Resetar Preferências</button>

    </div>
</div>


<style>
    .sidebar-custom, .navbar-custom, .brand-custom {
        transition: all 0.3s ease;
    }
</style>

<script>
    
    document.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        const sidebar = document.querySelector('.main-sidebar');
        const navbar = document.querySelector('.main-header');
        const brand = document.querySelector('.brand-link');
        const footer = document.querySelector('footer.main-footer');
        const fontSelector = document.getElementById('fontSelector');

        const elements = {
            darkMode: document.getElementById('toggleDarkMode'),
            sidebarMini: document.getElementById('toggleSidebarMini'),
            sidebarCollapse: document.getElementById('toggleSidebarCollapse'),
            sidebarColor: document.getElementById('sidebarCustomColor'),
            sidebarText: document.getElementById('sidebarTextColor'),
            navbarColor: document.getElementById('navbarCustomColor'),
            navbarText: document.getElementById('navbarTextColor'),
            brandColor: document.getElementById('brandCustomColor'),
            brandText: document.getElementById('brandTextColor'),
            footerFixed: document.getElementById('toggleFooterFixed'),
        };

        function applyColor(target, bgColor, textColor, className) {
            target.classList.add(className);
            target.style.backgroundColor = bgColor;
            target.style.color = textColor;
            target.querySelectorAll('*').forEach(el => {
                el.style.color = textColor;
            });
        }

        // Load saved preferences
        if (localStorage.getItem('darkMode') === 'true') {
            body.classList.add('dark-mode');
            elements.darkMode.checked = true;
        }
        if (localStorage.getItem('sidebarMini') === 'true') {
            body.classList.add('sidebar-mini');
            elements.sidebarMini.checked = true;
        }
        if (localStorage.getItem('sidebarCollapse') === 'true') {
            body.classList.add('sidebar-collapse');
            elements.sidebarCollapse.checked = true;
        }
        if (localStorage.getItem('sidebarColor')) {
            applyColor(sidebar, localStorage.getItem('sidebarColor'), localStorage.getItem('sidebarTextColor'), 'sidebar-custom');
            elements.sidebarColor.value = localStorage.getItem('sidebarColor');
            elements.sidebarText.value = localStorage.getItem('sidebarTextColor');
        }
        if (localStorage.getItem('navbarColor')) {
            applyColor(navbar, localStorage.getItem('navbarColor'), localStorage.getItem('navbarTextColor'), 'navbar-custom');
            elements.navbarColor.value = localStorage.getItem('navbarColor');
            elements.navbarText.value = localStorage.getItem('navbarTextColor');
        }
        if (localStorage.getItem('brandColor')) {
            applyColor(brand, localStorage.getItem('brandColor'), localStorage.getItem('brandTextColor'), 'brand-custom');
            elements.brandColor.value = localStorage.getItem('brandColor');
            elements.brandText.value = localStorage.getItem('brandTextColor');
        }
        if (localStorage.getItem('footerFixed') === 'true') {
            footer.classList.add('fixed-bottom');
            elements.footerFixed.checked = true;
        }
        if (localStorage.getItem('fontFamily')) {
            document.body.style.fontFamily = localStorage.getItem('fontFamily');
            fontSelector.value = localStorage.getItem('fontFamily');
        }

        // Event listeners
        elements.darkMode.addEventListener('change', () => {
            body.classList.toggle('dark-mode', elements.darkMode.checked);
            localStorage.setItem('darkMode', elements.darkMode.checked);
        });
        elements.sidebarMini.addEventListener('change', () => {
            body.classList.toggle('sidebar-mini', elements.sidebarMini.checked);
            localStorage.setItem('sidebarMini', elements.sidebarMini.checked);
        });
        elements.sidebarCollapse.addEventListener('change', () => {
            body.classList.toggle('sidebar-collapse', elements.sidebarCollapse.checked);
            localStorage.setItem('sidebarCollapse', elements.sidebarCollapse.checked);
        });
        elements.sidebarColor.addEventListener('input', () => {
            applyColor(sidebar, elements.sidebarColor.value, elements.sidebarText.value, 'sidebar-custom');
            localStorage.setItem('sidebarColor', elements.sidebarColor.value);
        });
        elements.sidebarText.addEventListener('input', () => {
            applyColor(sidebar, elements.sidebarColor.value, elements.sidebarText.value, 'sidebar-custom');
            localStorage.setItem('sidebarTextColor', elements.sidebarText.value);
        });
        elements.navbarColor.addEventListener('input', () => {
            applyColor(navbar, elements.navbarColor.value, elements.navbarText.value, 'navbar-custom');
            localStorage.setItem('navbarColor', elements.navbarColor.value);
        });
        elements.navbarText.addEventListener('input', () => {
            applyColor(navbar, elements.navbarColor.value, elements.navbarText.value, 'navbar-custom');
            localStorage.setItem('navbarTextColor', elements.navbarText.value);
        });
        elements.brandColor.addEventListener('input', () => {
            applyColor(brand, elements.brandColor.value, elements.brandText.value, 'brand-custom');
            localStorage.setItem('brandColor', elements.brandColor.value);
        });
        elements.brandText.addEventListener('input', () => {
            applyColor(brand, elements.brandColor.value, elements.brandText.value, 'brand-custom');
            localStorage.setItem('brandTextColor', elements.brandText.value);
        });
        elements.footerFixed.addEventListener('change', () => {
            footer.classList.toggle('fixed-bottom', elements.footerFixed.checked);
            localStorage.setItem('footerFixed', elements.footerFixed.checked);
        });
        fontSelector.addEventListener('change', () => {
            document.body.style.fontFamily = fontSelector.value;
            localStorage.setItem('fontFamily', fontSelector.value);
        });

        document.getElementById('resetLayout').addEventListener('click', () => {
            localStorage.clear();
            location.reload();
        });
    });

</script>


<link href="https://fonts.googleapis.com/css2?family=Fira+Code&family=Roboto&family=Open+Sans&family=Lato&family=Poppins&family=Montserrat&family=Raleway&family=Nunito&family=Merriweather&family=Playfair+Display&display=swap" rel="stylesheet">
<style>
    .main-sidebar {
        min-height: 100vh;
    }

    ::-webkit-scrollbar {
        width: 0px;
        background: transparent;
    }

    body {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    body::-webkit-scrollbar {
        display: none;
    }
</style>

