<?php
/**
 * logo.php — Componente da Logo Nexum Games
 * Exibe a imagem oficial da logo (assets/logo.png) com suporte a tamanho padrão e reduzido.
 */
$basePath = $basePath ?? (defined('IN_ADMIN') && IN_ADMIN ? '../' : '');
$isSmall = !empty($logoSmall);
$customClass = $logoClass ?? '';
?>
<span class="nexum-logo <?= $isSmall ? 'nexum-logo--small' : '' ?> <?= htmlspecialchars($customClass) ?>">
    <img src="<?= $basePath ?>assets/logo.png" alt="Nexum" class="nexum-logo__img">
    <span class="logo__games">Games</span>
</span>
