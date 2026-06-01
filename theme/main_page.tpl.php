<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новогодние мероприятия</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #ffce85;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .header {
            position: relative;
            height: 100vh;
            min-height: 600px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: rgba(255, 159, 15, 0.7);
            position: relative;
            z-index: 100;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: crimson;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            position: relative;
            margin-left: 30px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #ff9900;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #c9974d;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 4px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .nav-links li:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            color: #333;
            display: block;
            padding: 12px 20px;
            border-bottom: 1px solid #eee;
        }

        .dropdown-menu a:hover {
            background-color: #f5f5f5;
            color: #ff9900;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            right: -300px;
            width: 300px;
            height: 100%;
            background-color: #fff;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            z-index: 1001;
            padding: 80px 20px 20px;
            overflow-y: auto;
        }

        .mobile-menu.active {
            right: 0;
        }

        .mobile-menu-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .mobile-menu-links {
            list-style: none;
        }

        .mobile-menu-links li {
            margin-bottom: 15px;
        }

        .mobile-menu-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            display: block;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 0 20px;
        }

        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-content p {
            font-size: 20px;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-block;
            background-color: crimson;
            color: #fff;
            padding: 14px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #e68a00;
        }

        .projects {
            padding: 80px 0;
            background-color: #c9974d;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 50px;
            color: #333;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
            gap: 40px;
            justify-items: center;
        }

        .projects-card {
            background-color: white;
            border-radius: 8px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .projects-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .projects-icon {
            font-size: 40px;
            color: #ff9900;
            margin-bottom: 20px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .projects-card h3 {
            color: black;
            text-shadow: 0 0 0, 0 0 1em #ff9900, 0 0 0.2em #ff9900;
            font-size: 22px;
            margin-bottom: 20px;
        }

        #project-5 {
            margin-top: 30px;
        }

        .slider-section {
            padding: 80px 0;
            background-color: #c9974d;
        }

        .slider-container {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            overflow: hidden;
        }

        .slider-wrapper {
            position: relative;
            overflow: hidden;
        }

        .slider {
            display: flex;
            transition: transform 0.5s ease;
        }

        .slide {
            min-width: 100%;
            padding: 0 15px;
        }

        .slide-content {
            background-color: #fff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
        }

        .slide-content img {
            width: 50%;
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
        }

        .slide-content h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .slider-nav {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .slider-btn {
            background: none;
            border: none;
            font-size: 24px;
            color: #ff9900;
            cursor: pointer;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .slider-btn:hover {
            color: #e68a00;
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .dot.active {
            background-color: #ff9900;
        }

        .footer {
            background-color: rgba(255, 159, 15, 1);
            color: #fff;
            padding: 50px 0 20px;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .footer-column {
            flex: 1;
            min-width: 250px;
            margin-bottom: 30px;
            padding-right: 20px;
        }

        .footer-column h3 {
            font-size: 20px;
            margin-bottom: 20px;
            color: white;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
        }

        .footer-links a:hover {
            color: crimson;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid white;
            color: white;
            font-size: 14px;
        }

        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 40px;
            }
            
            .projects-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .hero-content h1 {
                font-size: 32px;
            }
            
            .hero-content p {
                font-size: 18px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .projects-grid {
                grid-template-columns: 1fr;
            }
            
            .slide-content {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 28px;
            }
            
            .footer-content {
                flex-direction: column;
            }
            
            .footer-column {
                padding-right: 0;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="video-background">
            <video autoplay muted loop playsinline>
                <source src="video.mp4" type="video/mp4">
                Видео не поддерживается в вашем браузере
            </video>
            <div class="video-overlay"></div>
        </div>

        <nav class="nav">
            <a href="#" class="logo">НОВОГОДНИЕ ПРАЗДНИКИ и МЕРОПРИЯТИЯ</a>
            
            <ul class="nav-links">
                <li><a href="#home">Главная</a></li>
                <li>
                    <a href="#projects">Проекты</a>
                    <div class="dropdown-menu">
                        <a href="#project-5">Утренники</a>
                        <a href="#project-2">Корпоративы</a>
                        <a href="#project-3">Квесты</a>
                        <a href="#project-1">Мастер-классы</a>
                        <a href="#project-4">Балы и концерты</a>
                    </div>
                </li>
                <li><a href="#slider">Архив мероприятий</a></li>
                <li><a href="?q=form">Заявка</a></li>
            </ul>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
        <div class="mobile-menu" id="mobileMenu">
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fas fa-times"></i>
            </button>
            <ul class="mobile-menu-links">
                <li><a href="#home">Главная</a></li>
                <li><a href="#projects">Проекты</a></li>
                <li><a href="#slider">Архив</a></li>
                <li><a href="?q=form">Заявка</a></li>
            </ul>
        </div>
        
        <section class="hero" id="home">
            <div class="hero-content">
                <h1 style="color: crimson;">ОРГАНИЗАЦИЯ НОВОГОДНИХ МЕРОПРИЯТИЙ</h1>
                <p style="text-shadow: 1px 1px 2px crimson, 0 0 1em black, 0 0 0.2em black;">Планирование и проведение новогодних праздников любого масштаба с профессиональной командой и современным оборудованием!</p>
                <a href="?q=form" class="btn">Оставить заявку</a>
            </div>
        </section>
    </header>

    <section class="projects" id="projects">
        <div class="container">
            <h2 class="section-title">Наши проекты</h2>
            <div class="projects-grid">
                <div class="projects-card" id="project-1">
                    <div class="projects-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Мастер-классы</h3>
                    <p>Уроки рукоделия от мастеров своего дела</p>
                </div>

                <div class="projects-card" id="project-2">
                    <div class="projects-icon">
                        <i class="fas fa-beer"></i>
                    </div>
                    <h3>Корпоративы</h3>
                    <p>Интересные конкурсы и необычные форматы помогут отдохнуть от рабочих будней</p>
                </div>

                <div class="projects-card" id="project-3">
                    <div class="projects-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Квесты</h3>
                    <p>Интересные приключения с веселыми головоломками</p>
                </div>

                <div class="projects-card" id="project-4">
                    <div class="projects-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Балы и концерты</h3>
                    <p>Классика и новые форматы для культурного обогащения</p>
                </div>
            </div>
            <div class="projects-card" id="project-5">
                <div class="projects-icon">
                    <i class="fas fa-child"></i>
                </div>
                <h3>Утренники</h3>
                <p>Веселый праздник для самых маленьких</p>
            </div>
        </div>        
    </section>

    <section class="slider-section" id="slider">
        <div class="container">
            <h2 class="section-title">Архив мероприятий</h2>
            <div class="slider-container">
                <div class="slider-wrapper">
                    <div class="slider" id="jquery-slider">
                        <div class="slide">
                            <div class="slide-content">
                                <img src="image1.png" alt="Концерт" onerror="this.src='https://placehold.co/200x150?text=Image+1'">
                                <h3>Концерт в театре "Мастерская" г.Санкт-Петербург</h3>
                                <p>Гости отлично провели время наслаждаясь композициями всемирно известных русских классиков</p>
                                <p>Дата проведения: 30.12.2020</p>
                            </div>
                        </div>
                        <div class="slide">
                            <div class="slide-content">
                                <img src="image2.png" alt="Мастер-класс" onerror="this.src='https://placehold.co/200x150?text=Image+2'">
                                <h3>Мастер-класс по изготовлению украшений г.Краснодар</h3>
                                <p>Студенты научились новому и наполнили коридоры вуза новогодним настроением</p>
                                <p>Дата проведения: 15.12.2022</p>
                            </div>
                        </div>
                        <div class="slide">
                            <div class="slide-content">
                                <img src="image3.png" alt="Утренник" onerror="this.src='https://placehold.co/200x150?text=Image+3'">
                                <h3>Утренник в младшей школе г.Анапа</h3>
                                <p>Дата проведения: 29.12.2022</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slider-nav">
                    <button class="slider-btn" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-btn" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="slider-dots" id="sliderDots"></div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>НОВОГОДНИЕ МЕРОПРИЯТИЯ</h3>
                    <p>Планирование и проведение новогодних праздников любого масштаба с профессиональной командой и современным оборудованием.</p>
                </div>
                <div class="footer-column">
                    <h3>Контакты</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> г. Санкт-Петербург, ул. Новогодняя, 26</li>
                        <li><i class="fas fa-phone"></i> +7 (918) 063-00-19</li>
                        <li><i class="fas fa-envelope"></i> info@newYearProjects.ru</li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Ссылки</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Главная</a></li>
                        <li><a href="#projects">Проекты</a></li>
                        <li><a href="#slider">Архив</a></li>
                        <li><a href="?q=form">Заявка</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 НОВОГОДНИЕ МЕРОПРИЯТИЯ. Абсолютно все права максимально защищены.</p>
            </div>
        </div>
    </footer>

    <script>
    // Mobile menu functionality
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuClose = document.getElementById('mobileMenuClose');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    }

    const mobileMenuLinks = document.querySelectorAll('.mobile-menu-links a');
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Slider functionality
    $(document).ready(function() {
        const slider = $('#jquery-slider');
        const slides = $('.slide');
        const totalSlides = slides.length;
        let currentSlide = 0;
        let slideInterval;
        
        function createDots() {
            const dotsContainer = $('#sliderDots');
            dotsContainer.empty();
            for (let i = 0; i < totalSlides; i++) {
                const dot = $('<span class="dot"></span>');
                dot.data('slide', i);
                dotsContainer.append(dot);
            }
            $('.dot').eq(0).addClass('active');
        }
        
        function updateSlider() {
            const translateX = -(currentSlide * 100);
            slider.css('transform', `translateX(${translateX}%)`);
            $('.dot').removeClass('active');
            $('.dot').eq(currentSlide).addClass('active');
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }
        
        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
        }
        
        createDots();
        
        $('#nextBtn').click(function() {
            nextSlide();
            resetInterval();
        });
        
        $('#prevBtn').click(function() {
            prevSlide();
            resetInterval();
        });
        
        $(document).on('click', '.dot', function() {
            currentSlide = $(this).data('slide');
            updateSlider();
            resetInterval();
        });
        
        function startInterval() {
            slideInterval = setInterval(nextSlide, 12000);
        }
        
        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }
        
        startInterval();
        
        $('.slider-container').hover(
            function() { clearInterval(slideInterval); },
            function() { startInterval(); }
        );
    });
    </script>
</body>
</html>
