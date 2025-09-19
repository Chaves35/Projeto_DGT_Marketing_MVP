document.addEventListener('DOMContentLoaded', function() {
    // Função para rolagem suave
    function smoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // Função para animações de entrada
    function scrollAnimations() {
        const elements = document.querySelectorAll('.feature, .testimonial, .cta');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, {
            threshold: 0.1
        });

        elements.forEach(element => {
            observer.observe(element);
        });
    }

    // Validação básica de formulário
    function formValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const inputs = form.querySelectorAll('input, textarea');
                
                inputs.forEach(input => {
                    if (input.hasAttribute('required') && input.value.trim() === '') {
                        isValid = false;
                        input.classList.add('error');
                    } else {
                        input.classList.remove('error');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });
        });
    }

    // Função para rastreamento de eventos
    function trackEvents() {
        const trackableElements = document.querySelectorAll('[data-track]');
        
        trackableElements.forEach(element => {
            element.addEventListener('click', function() {
                const eventName = this.getAttribute('data-track');
                console.log(`Evento rastreado: ${eventName}`);
                // Aqui você pode integrar com Google Analytics, Facebook Pixel, etc.
            });
        });
    }

    // Inicialização das funções
    smoothScroll();
    scrollAnimations();
    formValidation();
    trackEvents();
});
