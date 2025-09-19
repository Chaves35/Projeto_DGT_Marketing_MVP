<?php
use DGTMarketing\Security\CSRFProtection;

$csrfProtection = new CSRFProtection();
?>
<section id="contato" class="contact-form">
    <div class="container">
        <h2>Entre em Contato</h2>
        <form id="contact-form" method="POST" action="/forms/contact-handler.php">
            <?= $csrfProtection->renderTokenField() ?>
            
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input 
                    type="text" 
                    id="nome" 
                    name="nome" 
                    required 
                    placeholder="Seu nome completo"
                >
            </div>
            
            <div class="form-group">
                <label for="email">E-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="seu@email.com"
                >
            </div>
            
            <div class="form-group">
                <label for="telefone">Telefone</label>
                <input 
                    type="tel" 
                    id="telefone" 
                    name="telefone" 
                    required 
                    placeholder="(00) 90000-0000"
                >
            </div>
            
            <div class="form-group">
                <label for="mensagem">Mensagem</label>
                <textarea 
                    id="mensagem" 
                    name="mensagem" 
                    required 
                    placeholder="Sua mensagem"
                ></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
        </form>
    </div>
</section>
