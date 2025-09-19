document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(contactForm);
            const submitButton = contactForm.querySelector('button[type="submit"]');

            // Desabilitar botão durante envio
            submitButton.disabled = true;
            submitButton.textContent = 'Enviando...';

            fetch('/forms/contact-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Limpar formulário
                    contactForm.reset();
                    
                    // Mensagem de sucesso
                    alert(data.message);
                } else {
                    // Mensagens de erro
                    if (data.errors) {
                        alert(data.errors.join('\n'));
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao enviar formulário. Tente novamente.');
            })
            .finally(() => {
                // Reabilitar botão
                submitButton.disabled = false;
                submitButton.textContent = 'Enviar Mensagem';
            });
        });
    }
});
