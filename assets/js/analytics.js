document.addEventListener('DOMContentLoaded', function() {
    // Configurações de rastreamento
    const analyticsConfig = {
        googleAnalyticsId: 'G-XXXXXXXXXX', // Substitua pelo seu ID
        facebookPixelId: '123456789',     // Substitua pelo seu ID
        debug: true  // Modo de depuração
    };

    // Função para carregar Google Analytics
    function loadGoogleAnalytics() {
        if (!analyticsConfig.googleAnalyticsId) return;

        // Script assíncrono do Google Analytics
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', analyticsConfig.googleAnalyticsId);

        // Adiciona script no head
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${analyticsConfig.googleAnalyticsId}`;
        document.head.appendChild(script);

        if (analyticsConfig.debug) {
            console.log('Google Analytics carregado');
        }
    }

    // Função para carregar Facebook Pixel
    function loadFacebookPixel() {
        if (!analyticsConfig.facebookPixelId) return;

        // Carregamento do Facebook Pixel
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        
        fbq('init', analyticsConfig.facebookPixelId);
        fbq('track', 'PageView');

        if (analyticsConfig.debug) {
            console.log('Facebook Pixel carregado');
        }
    }

    // Rastreamento de eventos personalizados
    function trackCustomEvents() {
        const trackableElements = document.querySelectorAll('[data-analytics-event]');

        trackableElements.forEach(element => {
            element.addEventListener('click', function() {
                const eventName = this.getAttribute('data-analytics-event');
                const eventCategory = this.getAttribute('data-analytics-category') || 'Geral';
                const eventLabel = this.getAttribute('data-analytics-label') || element.textContent;

                // Rastreamento no Google Analytics
                if (window.gtag) {
                    gtag('event', eventName, {
                        'event_category': eventCategory,
                        'event_label': eventLabel
                    });
                }

                // Rastreamento no Facebook Pixel
                if (window.fbq) {
                    fbq('track', eventName, {
                        content_category: eventCategory,
                        content_name: eventLabel
                    });
                }

                if (analyticsConfig.debug) {
                    console.log(`Evento rastreado: ${eventName}`, {
                        category: eventCategory,
                        label: eventLabel
                    });
                }
            });
        });
    }

    // Rastreamento de conversões (formulários)
    function trackConversions() {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                // Rastreamento de lead no Google Analytics
                if (window.gtag) {
                    gtag('event', 'generate_lead', {
                        'event_category': 'Formulário',
                        'event_label': form.getAttribute('name') || 'Lead Genérico'
                    });
                }

                // Rastreamento de lead no Facebook Pixel
                if (window.fbq) {
                    fbq('track', 'Lead', {
                        content_name: form.getAttribute('name') || 'Lead Genérico'
                    });
                }

                if (analyticsConfig.debug) {
                    console.log('Conversão de lead rastreada');
                }
            });
        });
    }

    // Inicialização dos serviços de analytics
    function initAnalytics() {
        loadGoogleAnalytics();
        loadFacebookPixel();
        trackCustomEvents();
        trackConversions();
    }

    // Executa inicialização
    initAnalytics();
});
