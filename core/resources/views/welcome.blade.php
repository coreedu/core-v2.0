<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>CORE — Controle de Recursos Educacionais</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/templatemo-ebook-landing.css" rel="stylesheet">
    <link href="css/corrousel-landing.css" rel="stylesheet">
</head>

<body>
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="#hero">
                    <img src="images/logo.svg" alt="CORE" class="navbar-logo me-2">
                </a>
                <div class="d-lg-none ms-auto me-3">
                    <a href="/central" class="btn custom-btn custom-border-btn btn-naira btn-inverted">
                        <i class="btn-icon bi-box-arrow-in-right"></i>
                        <span>Entrar</span>
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-auto me-lg-4">
                        <li class="nav-item">
                            <a class="nav-link click-scroll active" href="#hero">Início</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#sobre">Sobre</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#recursos">Recursos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#devs">Equipe</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#contato">Contato</a>
                        </li>
                    </ul>
                    <div class="d-none d-lg-block">
                        <a href="/central" class="btn custom-btn custom-border-btn btn-naira btn-inverted">
                            <i class="btn-icon bi-box-arrow-in-right"></i>
                            <span>Entrar</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <section class="hero-section d-flex justify-content-center align-items-center w-100 bg-danger" id="hero"> 
            <div class="d-flex flex-row w-100 bg-primary overflow-auto gap-4 px-5 justify-content-center">
                @include('filament.resources.schedule-resource.partials.land-schedule-table', [
                    'schedule' => $schedule
                ])
            </div>
        </section>

        <section class="book-section section-padding" id="sobre">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <img src="images/core-visao-geral.png" class="img-fluid" alt="Gestão educacional Core">
                    </div>

                    <div class="col-lg-6 col-12">
                        <div class="book-section-info">
                            <h6>Organização e Controle</h6>
                            <h2 class="mb-4">Sobre a Core</h2>
                            <p>
                                A <strong>CORE — Controle de Recursos Educacionais</strong> é uma plataforma criada para facilitar a gestão acadêmica
                                de instituições de ensino. O sistema permite a <strong>criação, edição e gerenciamento de horários de aula</strong>,
                                além do <strong>cadastro de cursos, professores, disciplinas e salas</strong>.
                            </p>
                            <p>
                                Com uma interface moderna e intuitiva, a Core ajuda equipes administrativas e coordenações a manterem
                                os processos organizados e centralizados, garantindo mais eficiência na gestão escolar.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="recursos">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-12 text-center">
                        <h6>Principais Recursos</h6>
                        <h2 class="mb-5">O que a Core oferece</h2>
                    </div>
                    <div class="col-lg-4 col-12">
                        <nav id="navbar-example3" class="h-100 flex-column align-items-stretch">
                            <nav class="nav nav-pills flex-column">
                                <a class="nav-link smoothscroll" href="#item-1">Visão Geral</a>
                                <a class="nav-link smoothscroll" href="#item-2"><strong>Gerenciamento de Horários</strong></a>
                                <a class="nav-link smoothscroll" href="#item-3"><strong>Cadastro Acadêmico</strong></a>
                                <a class="nav-link smoothscroll" href="#item-4">Relatórios de Salas</a>
                            </nav>
                        </nav>
                    </div>
                    <div class="col-lg-8 col-12">
                        <div data-bs-spy="scroll" data-bs-target="#navbar-example3" data-bs-smooth-scroll="true" class="scrollspy-example-2" tabindex="0">

                            <div class="scrollspy-example-item" id="item-1">
                                <h5>Visão Geral</h5>
                                <p>
                                    A <strong>CORE</strong> é uma plataforma voltada à gestão acadêmica, desenvolvida para organizar e centralizar o controle de horários, cursos, professores e salas.
                                    Seu principal objetivo é oferecer praticidade e eficiência na administração da rotina escolar.
                                </p>
                                <blockquote class="blockquote">Organização e controle para uma gestão acadêmica mais eficiente.</blockquote>
                                <p>
                                    Com uma interface moderna e intuitiva, a CORE permite que coordenações e secretarias mantenham tudo sob controle — de horários a recursos físicos — em um único sistema.
                                </p>
                            </div>

                            <div class="scrollspy-example-item" id="item-2">
                                <h5>Gerenciamento de Horários</h5>
                                <p>
                                    Crie, edite e gerencie grades horárias de forma simples e rápida.
                                    A CORE permite associar cursos, professores, disciplinas, turnos e salas em uma grade organizada e livre de conflitos.
                                </p>
                                <p>
                                    Ideal para instituições que buscam otimizar a rotina acadêmica e manter uma visão clara da estrutura de aulas e turmas.
                                </p>
                                <div class="row">
                                    <div class="col-lg-6 col-12 mb-3">
                                        <img src="images/portrait-mature-smiling-authoress-sitting-desk.jpg" class="scrollspy-example-item-image img-fluid" alt="">
                                    </div>
                                    <div class="col-lg-6 col-12 mb-3">
                                        <img src="images/businessman-sitting-by-table-cafe.jpg" class="scrollspy-example-item-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>

                            <div class="scrollspy-example-item" id="item-3">
                                <h5>Cadastro Acadêmico</h5>
                                <p>
                                    Gerencie todas as informações essenciais da instituição em um só lugar.
                                    Cadastre cursos, disciplinas, professores e salas de aula de forma integrada, garantindo consistência e fácil acesso aos dados.
                                </p>
                                <p>
                                    Essa centralização facilita o planejamento, reduz erros e oferece uma visão completa da estrutura acadêmica.
                                </p>
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-12">
                                        <img src="images/core-visao-geral.png" class="img-fluid" alt="">
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <p>Controle completo da estrutura educacional</p>
                                        <p><strong>Todos os dados acadêmicos organizados e conectados.</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="scrollspy-example-item" id="item-4">
                                <h5>Relatórios de Salas</h5>
                                <p>
                                    Gere relatórios detalhados sobre o estado e uso das salas de aula, facilitando o acompanhamento da disponibilidade e auxiliando na tomada de decisões administrativas.
                                </p>
                                <p>
                                    A ferramenta oferece uma visão clara da ocupação dos espaços, ajudando a planejar melhor o uso dos recursos físicos da instituição.
                                </p>
                                <img src="images/portrait-mature-smiling-authoress-sitting-desk.jpg" class="scrollspy-example-item-image img-fluid mb-3" alt="">
                                <p>
                                    Tudo isso mantendo a interface intuitiva e prática que define a CORE.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="author-section section-padding" id="devs">
            <div class="container">
                <div class="row text-center mb-5">
                    <h2 class="section-title">Equipe de Desenvolvimento CORE</h2>
                    <p class="section-subtitle">Responsável pela criação, evolução e manutenção do sistema CORE</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="box">
                            <img src="images/devs/dev-direnzi.jpeg" class="author-image img-fluid" alt="Foto de Guilherme Direnzi — Desenvolvedor da CORE">
                            <div class="share">
                                <a href="https://www.linkedin.com/in/guilhermedirenzi/" target="_blank"><i class="bi bi-linkedin"></i></a>
                                <a href="https://github.com/Direnzi002" target="_blank"><i class="bi bi-github"></i></a>
                            </div>
                            <h3 class="dev-name">Guilherme Direnzi</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="box">
                            <img src="images/devs/dev-matheus.jpeg" class="author-image img-fluid" alt="Foto de Matheus Edivaldo — Desenvolvedor da CORE">
                            <div class="share">
                                <a href="https://www.linkedin.com/in/matheus-silva-8297a320a/" target="_blank"><i class="bi bi-linkedin"></i></a>
                                <a href="https://github.com/matheusedivaldo" target="_blank"><i class="bi bi-github"></i></a>
                            </div>
                            <h3 class="dev-name">Matheus Edivaldo</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="box">
                            <img src="images/devs/dev-rai.jpeg" class="author-image img-fluid" alt="Foto de Rai Felippe — Desenvolvedor da CORE">
                            <div class="share">
                                <a href="https://www.linkedin.com/in/rai-felippe-miranda-65127a237/" target="_blank"><i class="bi bi-linkedin"></i></a>
                                <a href="https://github.com/RaiFM" target="_blank"><i class="bi bi-github"></i></a>
                            </div>
                            <h3 class="dev-name">Rai Felippe</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="box">
                            <img src="images/devs/dev-vitoria.jpeg" class="author-image img-fluid" alt="Foto de Vitória Silva — Desenvolvedora da CORE">
                            <div class="share">
                                <a href="https://www.linkedin.com/in/vitoria-silva-alves-33170a20a/" target="_blank"><i class="bi bi-linkedin"></i></a>
                                <a href="https://github.com/vitorii" target="_blank"><i class="bi bi-github"></i></a>
                            </div>
                            <h3 class="dev-name">Vitória Silva</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section section-padding" id="contato">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-6 col-md-6 col-12 text-center text-md-start mb-5 mb-md-0">
                        <img src="images/logo.svg" alt="Logo do sistema CORE — Controle de Recursos Educacionais" class="footer-logo mb-3">
                        <p class="text-white mb-4"><strong>CORE — Controle de Recursos Educacionais</strong><br>Gestão acadêmica simplificada e organizada.</p>
                        <p class="footer-location mb-3"><i class="bi bi-geo-alt me-2"></i>Sede em Carapicuíba — São Paulo, Brasil</p>
                    </div>
                    <div class="col-lg-5 col-md-6 col-12 text-center text-md-start">
                        <h6 class="site-footer-title mb-3 text-white">Fale com nossa equipe</h6>
                        <p class="text-white mb-3">Dúvidas, suporte ou parcerias? Entre em contato:</p>
                        <ul class="social-icon mb-4 d-flex justify-content-center justify-content-md-start">
                            <li class="social-icon-item">
                                <a href="mailto:controlederecursoseducacionais@gmail.com" target="_blank" class="social-icon-link">
                                    <i class="bi bi-envelope-fill"></i>
                                </a>
                            </li>
                            <li class="social-icon-item">
                                <a href="https://www.instagram.com/core.2408" target="_blank" class="social-icon-link">
                                    <i class="bi bi-instagram"></i>
                                </a>
                            </li>
                        </ul>
                        <p class="copyright-text text-white mb-0">© 2025 <strong>CORE</strong> — Controle de Recursos Educacionais<br><small>Todos os direitos reservados.</small></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>