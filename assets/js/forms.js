document.addEventListener('DOMContentLoaded', function() {
    // Função para máscara de telefone
    function maskPhone(input) {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            
            if (value.length > 10) {
                // Celular com 9 dígitos
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else if (value.length > 6) {
                // Telefone fixo
                value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d+)/, '($1) $2');
            }
            
            this.value = value;
        });
    }

    // Validação de email
    function validateEmail(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
    }

    // Validação avançada de formulário
    function advancedFormValidation(form) {
        const inputs = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            // Remove erros anteriores
            input.classList.remove('is-invalid');

            // Validações específicas
            if (input.hasAttribute('required') && input.value.trim() === '') {
                input.classList.add('is-invalid');
                isValid = false;
            }

            // Validação de email
            if (input.type === 'email' && !validateEmail(input.value)) {
                input.classList.add('is-invalid');
                isValid = false;
            }

            // Validação de telefone
            if (input.getAttribute('name') === 'telefone' && input.value.replace(/\D/g, '').length < 10) {
                input.classList.add('is-invalid');
                isValid = false;
            }
        });

        return isValid;
    }

    // Envio de formulário via AJAX
    function ajaxFormSubmit(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!advancedFormValidation(form)) {
                return;
            }

            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');

            // Desabilita botão durante envio
            submitButton.disabled = true;
            submitButton.innerHTML = 'Enviando...';

            fetch('/forms/lead-capture.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Limpa formulário
                    form.reset();
                    
                    // Mostra mensagem de sucesso
                    const successMessage = document.createElement('div');
                    successMessage.classList.add('alert', 'alert-success');
                    successMessage.textContent = data.message || 'Formulário enviado com sucesso!';
                    form.prepend(successMessage);

                    // Remove mensagem após 5 segundos
                    setTimeout(() => {
                        successMessage.remove();
                    }, 5000);
                } else {
                    // Mostra erro
                    const errorMessage = document.createElement('div');
                    errorMessage.classList.add('alert', 'alert-danger');
                    errorMessage.textContent = data.message || 'Erro ao enviar formulário.';
                    form.prepend(errorMessage);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                const errorMessage = document.createElement('div');
                errorMessage.classList.add('alert', 'alert-danger');
                errorMessage.textContent = 'Erro de conexão. Tente novamente.';
                form.prepend(errorMessage);
            })
            .finally(() => {
                // Reabilita botão
                submitButton.disabled = false;
                submitButton.innerHTML = 'Enviar';
            });
        });
    }

    // Inicialização
    function initForms() {
        // Máscara de telefone em todos os inputs de telefone
        const phoneInputs = document.querySelectorAll('input[name="telefone"]');
        phoneInputs.forEach(maskPhone);

        // Adiciona validação AJAX em todos os formulários
        const forms = document.querySelectorAll('form');
        forms.forEach(ajaxFormSubmit);
    }

    // Executa inicialização
    initForms();
});
