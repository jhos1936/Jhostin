<?php


$navLinks = ['Inicio', 'Sobre mí', 'Habilidades', 'Proyectos', 'Contacto'];
$navIds   = ['inicio', 'sobre', 'habilidades', 'proyectos', 'contacto'];

$skills = [
    ['name' => 'HTML5',            'icon' => '🌐', 'desc' => 'Estructura semántica y accesible para la web.'],
    ['name' => 'CSS3',  'icon' => '🎨', 'desc' => 'Estilos responsivos y diseño de interfaces modernas.'],
    ['name' => 'JavaScript',       'icon' => '⚡', 'desc' => 'Interactividad y lógica en el lado del cliente.'],
    ['name' => 'PHP',              'icon' => '🐘', 'desc' => 'Desarrollo backend y lógica de negocio del lado del servidor.'],
    ['name' => 'MySQL',            'icon' => '🗄️', 'desc' => 'Diseño y gestión de bases de datos relacionales.'],
    ['name' => 'Python', 'icon' => '🐍', 'desc' => 'Automatización, scripting y desarrollo backend versátil.'],
    ['name' => 'Bootstrap',        'icon' => '🅱️', 'desc' => 'Componentes y grillas para interfaces rápidas y limpias.'],
    ['name' => 'Patrón MVC',       'icon' => '🏗️', 'desc' => 'Arquitectura de software para código ordenado y escalable.'],
];

$techs = [
    ['icon' => '🌐', 'label' => 'HTML5'],
    ['icon' => '🎨', 'label' => 'CSS3'],
    ['icon' => '⚡', 'label' => 'JavaScript'],
    ['icon' => '🐘', 'label' => 'PHP'],
    ['icon' => '🗄️', 'label' => 'MySQL'],
    ['icon' => '📦', 'label' => 'Git'],
    ['icon' => '🅱️', 'label' => 'Bootstrap'],
    ['icon' => '🌊', 'label' => 'Tailwind'],
];

$stats = [
    ['icon' => '📅', 'value' => '2+',   'label' => 'Años de experiencia'],
    ['icon' => '🚀', 'value' => '2+',   'label' => 'Proyectos completados'],
    ['icon' => '😊', 'value' => '10+',  'label' => 'Clientes satisfechos'],
    ['icon' => '💯', 'value' => '100%', 'label' => 'Compromiso'],
];

$projects = [
    [
        'num'   => '04',
        'title' => 'Sistema Académica',
        'tags'  => ['Sistema Web', 'Gestión Escolar'],
        'stack' => 'PHP · MySQL · MVC · Bootstrap 5',
        'desc'  => 'Plataforma académica desarrollada en PHP y MySQL para centralizar la gestión escolar y el seguimiento de estudiantes, profesores, cursos, matrículas y calificaciones.',
        'features' => [
            'Acceso diferenciado para administrador, profesor y estudiante',
            'Gestión de estudiantes, profesores, cursos y grados',
            'Matrículas y asignación de secciones',
            'Registro y consulta de calificaciones con promedios',
        ],
        'link' => 'https://jeremysenati.infinityfree.me/login.php?i=1',
        'img'  => 'assets/sistema-academico.svg',
    ],
    [
        'num'   => '02',
        'title' => 'Sistema de Gestión',
        'tags'  => ['Full Stack', 'MVC'],
        'stack' => 'PHP · MySQL · MVC · FPDF',
        'desc'  => 'Sistema web de gestión desarrollado en PHP con arquitectura MVC para administrar clientes, proyectos, usuarios y reportes de manera centralizada.',
        'features' => [
            'Inicio de sesión, registro y recuperación de contraseña',
            'Gestión de clientes y proyectos',
            'Panel administrativo con navegación por módulos',
            'Generación y descarga de reportes en PDF',
        ],
        'link' => 'https://jhostinsenati.gamer.free/?i=1',
        'img'  => 'assets/sistema-gestion.svg',
    ],
        [
        'num'   => '03',
        'title' => 'Tienda Tecnológica',
        'tags'  => ['E-commerce', 'MVC'],
        'stack' => 'PHP · MySQL · PDO · Bootstrap',
        'desc'  => 'Tienda online orientada a productos tecnológicos, con catálogo de productos y una interfaz moderna basada en PHP y arquitectura MVC.',
        'features' => [
            'Catálogo de productos tecnológicos',
            'Tarjetas de productos con diseño responsive',
            'Conexión a MySQL mediante PDO',
            'Arquitectura MVC para separar responsabilidades',
        ],
        'link' => 'https://tienda-tecno.free.je/',
        'img'  => 'assets/tienda.svg',
    ],
];

$socials = [
    ['icon' => '🐙', 'label' => 'GitHub',       'handle' => 'github.com/Jhosting',        'href' => '#'],
    ['icon' => '💼', 'label' => 'LinkedIn',     'handle' => 'linkedin.com/in/Jhosting',   'href' => '#'],
    ['icon' => '🐦', 'label' => 'Twitter / X',  'handle' => '@Jhosting',                  'href' => '#'],
    ['icon' => '📸', 'label' => 'Instagram',    'handle' => '@Jhosting',                  'href' => '#'],
    ['icon' => '✉️', 'label' => 'Email',        'handle' => 'Jhostin4550@email.com',               'href' => 'mailto:tu@email.com'],
    ['icon' => '📞', 'label' => 'Teléfono',     'handle' => '+51 929 643 454',             'href' => 'tel:+51929643454'],
];

$codeLines = [
    '<span class="kw">const</span> <span class="prop">developer</span> = {',
    '&nbsp;&nbsp;<span class="prop">name</span>: <span class="str">"Jhosting"</span>,',
    '&nbsp;&nbsp;<span class="prop">role</span>: <span class="str">"Full Stack Dev"</span>,',
    '&nbsp;&nbsp;<span class="prop">skills</span>: [',
    '&nbsp;&nbsp;&nbsp;&nbsp;<span class="str">"PHP"</span>, <span class="str">"MySQL"</span>,',
    '&nbsp;&nbsp;&nbsp;&nbsp;<span class="str">"JavaScript"</span>, <span class="str">"Git"</span>',
    '&nbsp;&nbsp;],',
    '&nbsp;&nbsp;<span class="prop">passion</span>: <span class="str">"Clean code"</span>,',
    '&nbsp;&nbsp;<span class="prop">available</span>: <span class="val">true</span>,',
    '};',
    '',
    '<span class="cm">// Listo para colaborar 🚀</span>',
];


$formSent   = false;
$formError  = '';
$formValues = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $formValues['name']    = trim($_POST['name'] ?? '');
    $formValues['email']   = trim($_POST['email'] ?? '');
    $formValues['subject'] = trim($_POST['subject'] ?? '');
    $formValues['message'] = trim($_POST['message'] ?? '');

    if ($formValues['name'] === '' || $formValues['email'] === '' || $formValues['message'] === '') {
        $formError = 'Por favor completa los campos obligatorios (nombre, email y mensaje).';
    } elseif (!filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)) {
        $formError = 'Ingresa un correo electrónico válido.';
    } else {
        // Aquí se enviaría el correo real, por ejemplo con mail() o PHPMailer:
        //
        // $to      = 'tu@email.com';
        // $subject = '[Portfolio] ' . ($formValues['subject'] !== '' ? $formValues['subject'] : 'Nuevo mensaje de contacto');
        // $body    = "Nombre: {$formValues['name']}\nEmail: {$formValues['email']}\n\n{$formValues['message']}";
        // $headers = "From: {$formValues['email']}\r\nReply-To: {$formValues['email']}\r\n";
        // mail($to, $subject, $body, $headers);

        $formSent   = true;
        $formValues = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DevPortfolio · Desarrollador Web Full Stack</title>
    <link rel="stylesheet" href="assets/style.css" />
</head>
<body>

<!-- ─── Navbar ─────────────────────────────────────────────────────────── -->
<nav>
    <div class="nav-inner">
        <div class="nav-logo">&lt;<span>Dev</span>Portfolio /&gt;</div>
        <ul class="nav-links">
            <?php foreach ($navLinks as $i => $label): ?>
                <li><a href="#<?= e($navIds[$i]) ?>"><?= e($label) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <a href="#contacto" class="btn-primary nav-cta" style="padding:8px 20px;font-size:13px;">Contáctame</a>
        <button class="nav-hamburger" aria-label="Menú">☰</button>
    </div>
</nav>
<div class="mobile-menu">
    <ul>
        <?php foreach ($navLinks as $i => $label): ?>
            <li><a href="#<?= e($navIds[$i]) ?>"><?= e($label) ?></a></li>
        <?php endforeach; ?>
        <li><a href="#contacto" class="btn-primary" style="width:100%;justify-content:center;">Contáctame</a></li>
    </ul>
</div>

<main>
    <!-- ─── Hero ───────────────────────────────────────────────────────── -->
    <section id="inicio" style="min-height:100vh;display:flex;align-items:center;padding-top:80px;position:relative;overflow:hidden;">
        <div class="blob" style="width:480px;height:480px;background:rgba(124,58,237,0.18);top:-100px;left:-160px;"></div>
        <div class="blob" style="width:340px;height:340px;background:rgba(59,130,246,0.14);bottom:40px;right:-100px;"></div>
        <div class="dot-grid" style="position:absolute;inset:0;opacity:0.6;"></div>

        <div class="container" style="position:relative;z-index:1;width:100%;">
            <div class="hero-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;">
                <div class="reveal">
                    <div style="display:inline-block;background:rgba(124,58,237,0.15);border:1px solid rgba(124,58,237,0.3);border-radius:20px;padding:5px 14px;font-size:12px;font-weight:700;letter-spacing:0.1em;color:#a78bfa;margin-bottom:24px;">
                        DESARROLLADOR WEB · FULL STACK
                    </div>
                    <div style="display:flex;align-items:center;gap:28px;flex-wrap:nowrap;margin-bottom:20px;">
                        <h1 style="font-size:clamp(28px,4.5vw,64px);font-weight:900;line-height:1.1;margin:0;">
                            Hola, soy <span class="gradient-text">Jhosting
                            Morales F.</span>
                        </h1>
                        <div style="width:150px;height:200px;flex-shrink:0;border-radius:50%/40%;padding:5px;background:var(--gradient);box-shadow:0 8px 32px rgba(124,58,237,0.35);">
                            <img src="assets/img/perfil.jpg" alt="Foto de perfil" style="width:100%;height:100%;object-fit:cover;border-radius:50%/40%;border:4px solid var(--bg-primary);display:block;" />
                        </div>
                    </div>
                    <p style="font-size:18px;color:var(--text-secondary);margin-bottom:12px;font-weight:500;">
                        Construyo soluciones web completas, del frontend al backend.
                    </p>
                    <p style="color:var(--text-secondary);margin-bottom:32px;line-height:1.7;max-width:480px;">
                        Desarrollador apasionado por crear experiencias digitales funcionales y atractivas. Me especializo en PHP, JavaScript y MySQL, con enfoque en arquitecturas limpias y código mantenible.
                    </p>
                    <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:40px;">
                        <a href="#proyectos" class="btn-primary"><span>🚀</span> Ver mi trabajo</a>
                        <a href="assets/cv/CV_Jhosting.pdf" class="btn-outline" download="CV_Jhosting.pdf"><span>📄</span> Descargar CV</a>
                    </div>
                    <div class="tech-row">
                        <?php foreach ($techs as $t): ?>
                            <div class="tech-chip"><span><?= $t['icon'] ?></span> <?= e($t['label']) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="reveal animate-float" style="animation-delay:0.3s;">
                    <div class="code-panel" style="animation:float 4s ease-in-out infinite;">
                        <div class="code-panel-header">
                            <div class="code-dot" style="background:#ff5f57;"></div>
                            <div class="code-dot" style="background:#ffbd2e;"></div>
                            <div class="code-dot" style="background:#28c840;"></div>
                            <span style="margin-left:8px;color:var(--text-secondary);font-size:12px;">developer.js</span>
                        </div>
                        <div class="code-body">
                            <?php foreach ($codeLines as $i => $line): ?>
                                <div class="code-line">
                                    <span class="ln"><?= $i + 1 ?></span>
                                    <span><?= $line ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div style="position:relative;margin-top:-20px;display:flex;justify-content:flex-end;">
                        <div style="background:var(--gradient);border-radius:12px;padding:10px 18px;display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;box-shadow:0 8px 24px rgba(124,58,237,0.4);">
                            <span style="font-size:18px;">✅</span> Disponible para proyectos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── Sobre mí ───────────────────────────────────────────────────── -->
    <section id="sobre" style="background:var(--bg-secondary);">
        <div class="container">
            <div class="about-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;">
                <div class="reveal">
                    <div class="section-label">Sobre mí</div>
                    <h2 class="section-title">Soy un desarrollador full stack apasionado por crear <span class="gradient-text">soluciones digitales</span></h2>
                    <p style="color:var(--text-secondary);margin-bottom:16px;line-height:1.8;">
                        Con más de 2 años de experiencia en desarrollo web, me especializo en construir aplicaciones robustas utilizando PHP con arquitectura MVC, bases de datos MySQL y tecnologías frontend modernas.
                    </p>
                    <p style="color:var(--text-secondary);margin-bottom:32px;line-height:1.8;">
                        Mi enfoque está en el código limpio, la arquitectura escalable y la experiencia del usuario final. Disfruto resolver problemas complejos y convertir ideas en productos funcionales.
                    </p>
                    <a href="#proyectos" class="btn-primary">Conóceme más →</a>
                </div>
                <div class="reveal" style="animation-delay:0.15s;">
                    <div class="stats-grid">
                        <?php foreach ($stats as $s): ?>
                            <div class="card" style="text-align:center;padding:24px 16px;">
                                <div style="font-size:28px;margin-bottom:8px;"><?= $s['icon'] ?></div>
                                <div style="font-size:28px;font-weight:900;background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;"><?= e($s['value']) ?></div>
                                <div style="color:var(--text-secondary);font-size:12px;margin-top:4px;font-weight:500;"><?= e($s['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="habilidades">
        <div class="container">
            <div class="reveal" style="text-align:center;margin-bottom:56px;">
                <div class="section-label">Habilidades</div>
                <h2 class="section-title">Tecnologías que <span class="gradient-text">domino</span></h2>
                <p class="section-sub" style="margin:0 auto;">Herramientas y tecnologías con las que construyo proyectos de principio a fin.</p>
            </div>
            <div class="skills-grid">
                <?php foreach ($skills as $i => $s): ?>
                    <div class="card reveal" style="animation-delay:<?= $i * 60 ?>ms;text-align:center;padding:28px 18px;">
                        <div style="width:56px;height:56px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:28px;line-height:1;border-radius:14px;background:rgba(124,58,237,0.12);">
                            <?= $s['icon'] ?>
                        </div>
                        <div style="font-weight:600;font-size:15px;"><?= e($s['name']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="proyectos" style="background:var(--bg-secondary);">
        <div class="container">
            <div class="reveal" style="text-align:center;margin-bottom:56px;">
                <div class="section-label">Proyectos</div>
                <h2 class="section-title">Proyectos <span class="gradient-text">destacados</span></h2>
                <p class="section-sub" style="margin:0 auto;">Una selección de los proyectos en los que he trabajado.</p>
            </div>
            <div class="projects-grid">
                <?php foreach ($projects as $i => $p): ?>
                    <div class="card reveal" style="padding:0;overflow:hidden;animation-delay:<?= $i * 100 ?>ms;">
                        <div style="position:relative;height:180px;overflow:hidden;background:#1a1a2e;">
                            <img src="<?= e($p['img']) ?>" alt="<?= e($p['title']) ?>" style="width:100%;height:100%;object-fit:cover;opacity:0.7;" loading="lazy" />
                            <div style="position:absolute;inset:0;background:linear-gradient(to bottom, transparent 40%, rgba(10,10,18,0.9));"></div>
                            <div style="position:absolute;top:14px;left:14px;font-size:11px;font-weight:800;color:var(--accent);letter-spacing:0.12em;">#<?= e($p['num']) ?></div>
                            <div style="position:absolute;top:10px;right:10px;display:flex;gap:6px;">
                                <?php foreach ($p['tags'] as $tag): ?>
                                    <span style="background:rgba(124,58,237,0.7);border-radius:6px;padding:2px 8px;font-size:10px;font-weight:700;color:#fff;"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div style="padding:22px;">
                            <h3 style="margin:0 0 8px;font-size:18px;font-weight:700;"><?= e($p['title']) ?></h3>
                            <p style="color:var(--text-secondary);font-size:13px;margin:0 0 14px;line-height:1.6;"><?= e($p['desc']) ?></p>
                            <ul style="margin:0 0 16px;padding:0 0 0 16px;color:var(--text-secondary);font-size:12px;line-height:1.8;">
                                <?php foreach ($p['features'] as $f): ?>
                                    <li><?= e($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <span style="font-size:11px;color:var(--accent);font-weight:600;"><?= e($p['stack']) ?></span>
                                <a href="<?= e($p['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-primary" style="padding:7px 16px;font-size:12px;">Ver proyecto →</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section style="padding:64px 0;">
        <div class="container">
            <div class="reveal" style="max-width:680px;margin:0 auto;">
                <div class="card" style="text-align:center;position:relative;">
                    <div style="font-size:48px;color:var(--accent);line-height:1;margin-bottom:16px;">"</div>
                    <p style="font-size:18px;line-height:1.8;color:var(--text-primary);font-style:italic;margin-bottom:28px;">
                        Un desarrollador excepcional que entregó el proyecto a tiempo y superó todas las expectativas. Su atención al detalle y dominio técnico son notables.
                    </p>
                    <div style="display:flex;align-items:center;justify-content:center;gap:14px;">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--gradient);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">👤</div>
                        <div style="text-align:left;">
                            <div style="font-weight:700;font-size:15px;">Gustavo R. Morales</div>
                            <div style="color:var(--text-secondary);font-size:13px;">Cargo · Empresa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contacto" style="background:var(--bg-secondary);position:relative;overflow:hidden;">
        <div class="blob" style="width:380px;height:380px;background:rgba(124,58,237,0.12);top:-80px;right:-80px;"></div>
        <div class="container" style="position:relative;z-index:1;">
            <div class="reveal" style="text-align:center;margin-bottom:56px;">
                <div class="section-label">Contacto</div>
                <h2 class="section-title">¿Tienes un <span class="gradient-text">proyecto en mente?</span></h2>
                <p class="section-sub" style="margin:0 auto;">Estoy disponible para proyectos freelance y oportunidades de trabajo. ¡Hablemos!</p>
            </div>
            <div class="contact-grid">
                <div class="reveal">
                    <form method="post" action="#contacto">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div class="form-field">
                                <label for="name">Nombre</label>
                                <input type="text" id="name" name="name" placeholder="Jhosting" value="<?= e($formValues['name']) ?>" required />
                            </div>
                            <div class="form-field">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="tu@email.com" value="<?= e($formValues['email']) ?>" required />
                            </div>
                        </div>
                        <div class="form-field">
                            <label for="subject">Asunto</label>
                            <input type="text" id="subject" name="subject" placeholder="Asunto del mensaje" value="<?= e($formValues['subject']) ?>" />
                        </div>
                        <div class="form-field">
                            <label for="message">Mensaje</label>
                            <textarea id="message" name="message" rows="5" placeholder="Cuéntame sobre tu proyecto..." required><?= e($formValues['message']) ?></textarea>
                        </div>
                        <button type="submit" name="contact_submit" value="1" class="btn-primary" style="width:100%;justify-content:center;">
                            🚀 Enviar mensaje
                        </button>

                        <?php if ($formSent): ?>
                            <div class="form-status ok">✅ Mensaje enviado. ¡Gracias por escribirme!</div>
                        <?php elseif ($formError !== ''): ?>
                            <div class="form-status err">⚠️ <?= e($formError) ?></div>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="reveal" style="display:flex;flex-direction:column;gap:12px;animation-delay:0.15s;">
                    <p style="color:var(--text-secondary);margin-bottom:8px;line-height:1.7;">
                        Prefiero el contacto directo. Puedes encontrarme en las siguientes plataformas o escribirme directamente.
                    </p>
                    <?php foreach ($socials as $s): ?>
                        <a href="<?= e($s['href']) ?>" class="social-link" target="_blank" rel="noreferrer">
                            <span style="font-size:22px;"><?= $s['icon'] ?></span>
                            <div>
                                <div style="font-weight:600;color:var(--text-primary);font-size:13px;"><?= e($s['label']) ?></div>
                                <div style="color:var(--text-secondary);font-size:12px;"><?= e($s['handle']) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

<footer style="background:var(--bg-primary);border-top:1px solid rgba(124,58,237,0.15);padding:32px 0;">
    <div class="container" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div style="font-weight:800;font-size:16px;">&lt;<span class="gradient-text">Dev</span>Portfolio /&gt;</div>
        <p style="color:var(--text-secondary);font-size:13px;margin:0;">© <?= date('Y') ?> Jhosting · Todos los derechos reservados</p>
        <div style="display:flex;gap:20px;">
            <?php foreach (['Inicio', 'Sobre mí', 'Proyectos', 'Contacto'] as $l): ?>
                <a href="#" class="footer-link" style="color:var(--text-secondary);font-size:13px;text-decoration:none;transition:color 0.2s;"><?= e($l) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</footer>

<script src="assets/main.js"></script>
</body>
</html>
