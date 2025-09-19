<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/config.php';

echo "<h2>Teste CSRF Implementation</h2>";

// Gerar token
$token = generateCSRFToken();
echo "<p><strong>Token gerado:</strong> " . $token . "</p>";

// Renderizar campo
echo "<p><strong>Campo HTML:</strong></p>";
echo "<pre>" . htmlspecialchars(renderCSRFField()) . "</pre>";

// Teste de validação
echo "<p><strong>Validação do token:</strong> ";
if (validateCSRF($token)) {
    echo "<span style='color: green;'>✅ VÁLIDO</span>";
} else {
    echo "<span style='color: red;'>❌ INVÁLIDO</span>";
}
echo "</p>";

// Teste com token inválido
echo "<p><strong>Teste token inválido:</strong> ";
if (validateCSRF('token_falso')) {
    echo "<span style='color: red;'>❌ FALHA DE SEGURANÇA</span>";
} else {
    echo "<span style='color: green;'>✅ PROTEÇÃO FUNCIONANDO</span>";
}
echo "</p>";
?>
