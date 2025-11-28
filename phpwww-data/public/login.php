<!DOCTYPE html>
<html lang="es"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Aplicación Web</title>
    <!-- Carga de Tailwind CSS desde CDN para estilos rápidos y responsivos -->
    <script src="Iniciar%20Sesi%C3%B3n%20-%20Aplicaci%C3%B3n%20Web_archivos/3.4.17"></script>
    <link href="Iniciar%20Sesi%C3%B3n%20-%20Aplicaci%C3%B3n%20Web_archivos/css2.css" rel="stylesheet">
    <style>
        /* Estilo para usar la fuente Inter */
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Gradiente de fondo atractivo */
        .bg-login-gradient {
            background: linear-gradient(135deg, #4c51bf 0%, #7c3aed 100%);
        }
    </style>
<style>*, ::before, ::after{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }::backdrop{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:rgb(59 130 246 / 0.5);--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }/* ! tailwindcss v3.4.17 | MIT License | https://tailwindcss.com */*,::after,::before{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}::after,::before{--tw-content:''}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:transparent;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0;padding:0}legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]:where(:not([hidden=until-found])){display:none}.mx-auto{margin-left:auto;margin-right:auto}.mb-1{margin-bottom:0.25rem}.mb-8{margin-bottom:2rem}.mt-2{margin-top:0.5rem}.mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.block{display:block}.flex{display:flex}.h-12{height:3rem}.min-h-screen{min-height:100vh}.w-12{width:3rem}.w-full{width:100%}.max-w-md{max-width:28rem}.appearance-none{-webkit-appearance:none;appearance:none}.items-center{align-items:center}.justify-center{justify-content:center}.space-y-6 > :not([hidden]) ~ :not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem * var(--tw-space-y-reverse))}.rounded-lg{border-radius:0.5rem}.rounded-xl{border-radius:0.75rem}.border{border-width:1px}.border-gray-300{--tw-border-opacity:1;border-color:rgb(209 213 219 / var(--tw-border-opacity, 1))}.border-transparent{border-color:transparent}.bg-indigo-600{--tw-bg-opacity:1;background-color:rgb(79 70 229 / var(--tw-bg-opacity, 1))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity, 1))}.p-4{padding:1rem}.p-8{padding:2rem}.px-4{padding-left:1rem;padding-right:1rem}.py-3{padding-top:0.75rem;padding-bottom:0.75rem}.text-center{text-align:center}.text-3xl{font-size:1.875rem;line-height:2.25rem}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:0.875rem;line-height:1.25rem}.font-extrabold{font-weight:800}.font-medium{font-weight:500}.text-gray-600{--tw-text-opacity:1;color:rgb(75 85 99 / var(--tw-text-opacity, 1))}.text-gray-700{--tw-text-opacity:1;color:rgb(55 65 81 / var(--tw-text-opacity, 1))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39 / var(--tw-text-opacity, 1))}.text-indigo-600{--tw-text-opacity:1;color:rgb(79 70 229 / var(--tw-text-opacity, 1))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255 / var(--tw-text-opacity, 1))}.placeholder-gray-400::placeholder{--tw-placeholder-opacity:1;color:rgb(156 163 175 / var(--tw-placeholder-opacity, 1))}.shadow-2xl{--tw-shadow:0 25px 50px -12px rgb(0 0 0 / 0.25);--tw-shadow-colored:0 25px 50px -12px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-lg{--tw-shadow:0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);--tw-shadow-colored:0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.shadow-sm{--tw-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05);--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)}.transition{transition-property:color, background-color, border-color, fill, stroke, opacity, box-shadow, transform, filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;transition-property:color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter, -webkit-text-decoration-color, -webkit-backdrop-filter;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1);transition-duration:150ms}.duration-150{transition-duration:150ms}.duration-300{transition-duration:300ms}.ease-in-out{transition-timing-function:cubic-bezier(0.4, 0, 0.2, 1)}.hover\:bg-indigo-700:hover{--tw-bg-opacity:1;background-color:rgb(67 56 202 / var(--tw-bg-opacity, 1))}.hover\:text-indigo-500:hover{--tw-text-opacity:1;color:rgb(99 102 241 / var(--tw-text-opacity, 1))}.focus\:border-indigo-500:focus{--tw-border-opacity:1;border-color:rgb(99 102 241 / var(--tw-border-opacity, 1))}.focus\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}.focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000)}.focus\:ring-indigo-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(99 102 241 / var(--tw-ring-opacity, 1))}.focus\:ring-offset-2:focus{--tw-ring-offset-width:2px}@media (min-width: 768px){.md\:p-10{padding:2.5rem}}</style></head>
<body class="min-h-screen flex items-center justify-center bg-login-gradient p-4">

    <!-- Contenedor principal del formulario de login -->
    <div class="w-full max-w-md bg-white p-8 md:p-10 rounded-xl shadow-2xl transition-all duration-300">
        
        <!-- Encabezado -->
        <div class="text-center mb-8">
            <svg class="mx-auto h-12 w-12 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            <h1 class="mt-4 text-3xl font-extrabold text-gray-900">
                Bienvenido
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Inicia sesión para acceder a tu cuenta.
            </p>
        </div>

          <!-- Formulario -->
        <form action="http://10.74.27.31/public/login.php#" method="POST" class="space-y-6">
            <!-- Campo de Usuario/Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Usuario
                </label>
                <input id="user" name="user" type="email" required="" placeholder="ejemplo" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-base">
            </div>

            <!-- Campo de Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Contraseña
                </label>
                <input id="password" name="password" type="password" required="" placeholder="••••••••" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-base">
            </div>

            <!-- Botón de Iniciar Sesión -->
            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-lg font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        <!-- Enlace de ayuda o registro -->
        <div class="mt-6 text-center">
            <a href="http://10.74.27.31/public/login.php#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                ¿Olvidaste tu contraseña?
            </a>
            <p class="mt-4 text-sm text-gray-600">
                ¿No tienes cuenta?
                <a href="http://10.74.27.31/public/login.php#" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Regístrate aquí
                </a> (PPROXIMAMENTE...)
            </p>
        </div>
    </div>


</body>
</html>