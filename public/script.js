const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenuClose = document.getElementById('mobileMenuClose');
const mobileMenu = document.getElementById('mobileMenu');
const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

mobileMenuBtn.addEventListener('click', () => {
    mobileMenu.classList.add('active');
    mobileMenuOverlay.classList.add('active');
    document.body.style.overflow = 'hidden';
});

mobileMenuClose.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    mobileMenuOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
});

mobileMenuOverlay.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    mobileMenuOverlay.classList.remove('active');
    document.body.style.overflow = 'auto';
});

const mobileMenuLinks = document.querySelectorAll('.mobile-menu-links a');
mobileMenuLinks.forEach(link => {
    link.addEventListener('click', () => {
        mobileMenu.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
});

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
        const slideWidth = 100; 
        const translateX = -(currentSlide * slideWidth);
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
    
    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
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
        const slideIndex = $(this).data('slide');
        goToSlide(slideIndex);
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
        function() {
            clearInterval(slideInterval);
        },
        function() {
            startInterval();
        }
    );
    
    function checkCompetenciesAnimation() {
        const windowBottom = $(window).scrollTop() + $(window).height();
        const competencyCards = $('.competency-card');
        
        competencyCards.each(function() {
            const cardTop = $(this).offset().top;
            
            if (cardTop < windowBottom - 50) {
                $(this).css({
                    'opacity': '1',
                    'transform': 'translateY(0)'
                });
            }
        });
    }
    
    $('.competency-card').css({
        'opacity': '0',
        'transform': 'translateY(20px)',
        'transition': 'opacity 0.5s ease, transform 0.5s ease'
    });
    
    $(window).on('scroll', checkCompetenciesAnimation);
    $(window).on('load', checkCompetenciesAnimation);
    
    checkCompetenciesAnimation();
});

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            formMessage.textContent = 'Отправка формы...';
            formMessage.className = 'form-message';
            formMessage.style.display = 'block';
            formMessage.style.backgroundColor = '#fff3cd';
            formMessage.style.color = '#856404';
            
            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !phone || !email) {
                formMessage.textContent = 'Пожалуйста, заполните все обязательные поля (имя, телефон, email).';
                formMessage.className = 'form-message error';
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                formMessage.textContent = 'Пожалуйста, введите корректный email адрес.';
                formMessage.className = 'form-message error';
                return;
            }
            
            const formData = {
                name: name,
                phone: phone,
                email: email,
                message: message || 'Нет сообщения',
                source: 'СТРОЙМАРКЕТЫ сайт',
                timestamp: new Date().toLocaleString('ru-RU')
            };
            
            try {
                const formCarryKey = 'WQI21-Aaf30';
                
                const response = await fetch(`https://formcarry.com/s/${formCarryKey}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (response.ok && (result.code === 200 || result.status === 'success')) {
                    formMessage.textContent = 'Спасибо! Ожидайте ответа.';
                    formMessage.className = 'form-message success';
                    
                    contactForm.reset();
                    
                    setTimeout(() => {
                        formMessage.style.display = 'none';
                    }, 7000);
                } else {
                    let errorMessage = 'Произошла ошибка при отправке. Пожалуйста, попробуйте еще раз.';
                    
                    if (result.message) {
                        errorMessage = result.message;
                    }
                    
                    formMessage.textContent = errorMessage;
                    formMessage.className = 'form-message error';
                    
                    setTimeout(() => {
                        formMessage.style.display = 'none';
                    }, 7000);
                    
                    console.error('Ошибка FormCarry:', result);
                }
            } catch (error) {
                formMessage.textContent = 'Ошибка сети. Пожалуйста, проверьте подключение к интернету и попробуйте еще раз.';
                formMessage.className = 'form-message error';
                
                setTimeout(() => {
                    formMessage.style.display = 'none';
                }, 7000);
                
                console.error('Ошибка сети:', error);
            }
        });
    }
});
