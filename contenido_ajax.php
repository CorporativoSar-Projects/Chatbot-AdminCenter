<?php
// contenido_ajax.php
// Este archivo contiene SOLO el contenido principal (sin el chat)

// Determinar qué pestaña está activa
$tab_activa = isset($_GET['tab']) ? $_GET['tab'] : 'candidatos';
?>

<!-- Pestañas -->
<div class="tabs-container">
    <button class="tab-btn <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>"
        onclick="cambiarTabConChat('candidatos')">
        👥 Candidatos
    </button>
    <button class="tab-btn <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>"
        onclick="cambiarTabConChat('vacantes')">
        📋 Vacantes
    </button>
</div>

<!-- Tab de CANDIDATOS -->
<div id="candidatos-content" class="tab-content <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>">
    <!-- Header de la página -->
    <div class="page-header">
        <h2>Lista de Candidatos</h2>
        <div class="stats-info">
            <strong>Total: <?php echo $total_candidatos; ?> candidatos</strong> |
            Página <?php echo $pagina_actual_candidatos; ?> de <?php echo $total_paginas_candidatos; ?>
        </div>
    </div>

    <!-- Contenedor de la tabla de candidatos -->
    <div class="table-container">
        <table id="tablaCandidatos" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>CV</th>
                    <th>Acciones IA</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($candidatos) > 0): ?>
                    <?php foreach ($candidatos as $candidato): ?>
                        <tr>
                            <td>
                                <span class="candidate-id">
                                    #<?php echo str_pad($candidato['id_candidate'], 3, '0', STR_PAD_LEFT); ?>
                                </span>
                            </td>
                            <td>
                                <span class="candidate-name">
                                    <?php echo htmlspecialchars($candidato['nombre_candidate'] . ' ' . $candidato['apellidop_candidate']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($candidato['tel_candidate']); ?></td>
                            <td>
                                <?php if (!empty($candidato['CV_candidate'])): ?>
                                    <a href="<?php echo htmlspecialchars($candidato['CV_candidate']); ?>" target="_blank" class="btn btn-sm btn-info">
                                        📄 Ver CV
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-select-candidate"
                                    onclick="seleccionarParaAnalisis(<?php echo $candidato['id_candidate']; ?>)"
                                    style="display: none;">
                                    ✅ Seleccionar para Análisis
                                </button>
                                <button class="btn btn-improve-job"
                                    onclick="seleccionarParaMejoraPuesto(<?php echo $candidato['id_candidate']; ?>)"
                                    style="display: none;">
                                    ✏️ Mejorar Descripción
                                </button>
                                <button class="btn btn-compare-candidate"
                                    onclick="compararCVConSAP(<?php echo $candidato['id_candidate']; ?>)"
                                    style="display: none;">
                                    🔍 Comparar CV con SAP
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <h5>No hay candidatos registrados</h5>
                                <p>Los candidatos aparecerán aquí cuando se registren.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación para Candidatos -->
    <?php if ($total_paginas_candidatos > 1): ?>
        <div class="pagination-container">
            <ul class="pagination-custom">
                <!-- Botón Anterior -->
                <?php if ($pagina_actual_candidatos > 1): ?>
                    <li class="page-item-custom">
                        <a class="page-link-custom" href="?pagina_candidatos=<?php echo $pagina_actual_candidatos - 1; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            &laquo; Anterior
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item-custom">
                        <span class="page-link-custom disabled">&laquo; Anterior</span>
                    </li>
                <?php endif; ?>

                <!-- Números de página -->
                <?php for ($i = 1; $i <= $total_paginas_candidatos; $i++): ?>
                    <li class="page-item-custom <?php echo $i == $pagina_actual_candidatos ? 'active' : ''; ?>">
                        <a class="page-link-custom" href="?pagina_candidatos=<?php echo $i; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Botón Siguiente -->
                <?php if ($pagina_actual_candidatos < $total_paginas_candidatos): ?>
                    <li class="page-item-custom">
                        <a class="page-link-custom" href="?pagina_candidatos=<?php echo $pagina_actual_candidatos + 1; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            Siguiente &raquo;
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item-custom">
                        <span class="page-link-custom disabled">Siguiente &raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- Tab de VACANTES -->
<div id="vacantes-content" class="tab-content <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>">
    <!-- Header de vacantes -->
    <div class="page-header">
        <h2>📊 Vacantes Activas</h2>
        <div class="stats-info">
            <strong>Total: <?php echo $total_vacantes; ?> vacantes</strong> |
            Página <?php echo $pagina_actual_vacantes; ?> de <?php echo $total_paginas_vacantes; ?>
        </div>
    </div>

    <!-- Contenedor de la tabla de vacantes -->
    <div class="table-container">
        <table id="tablaVacantes" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Requisición</th>
                    <th>Puesto</th>
                    <th>Categoría</th>
                    <th>Ubicación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vacantes_paginadas)): ?>
                    <?php foreach ($vacantes_paginadas as $vacante):
                        $idRequisicion = $vacante['reqId_ix'] ?? $vacante['ID de requisición de personal'] ?? '';
                        $titulo = $vacante['title_ix'] ?? $vacante['Titulo'] ?? '';
                        $categoria = $vacante['category_ix'] ?? $vacante['Categoría'] ?? '';
                        $ubicacion = $vacante['location_ix'] ?? $vacante['Ubicación'] ?? '';
                        $link = $vacante['link'] ?? '#';
                    ?>
                        <tr>
                            <td>
                                <span class="requisicion-id">#<?php echo htmlspecialchars($idRequisicion); ?></span>
                            </td>
                            <td>
                                <div class="puesto-title"><?php echo htmlspecialchars($titulo); ?></div>
                            </td>
                            <td>
                                <span class="categoria-badge"><?php echo htmlspecialchars($categoria); ?></span>
                            </td>
                            <td>
                                <span class="ubicacion-badge">📍 <?php echo htmlspecialchars($ubicacion); ?></span>
                            </td>
                            <td>
                                <div class="actions-container">
                                    <?php if (!empty($link) && $link != '#'): ?>
                                        <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="btn-link">
                                            🔗 Ver Vacante
                                        </a>
                                    <?php endif; ?>

                                    <a href="detalle_candidatos_vacante.php?id_requisicion=<?php echo urlencode($idRequisicion); ?>&titulo=<?php echo urlencode($titulo); ?>&_preserve_chat=1" class="btn btn-candidates">
                                        👥 Ver Candidatos
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="text-muted">
                                <h5>No hay vacantes disponibles</h5>
                                <p>No se pudieron cargar las vacantes desde el archivo CSV.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación para Vacantes -->
    <?php if ($total_paginas_vacantes > 1): ?>
        <div class="pagination-container">
            <ul class="pagination-custom">
                <!-- Botón Anterior -->
                <?php if ($pagina_actual_vacantes > 1): ?>
                    <li class="page-item-custom">
                        <a class="page-link-custom" href="?pagina_vacantes=<?php echo $pagina_actual_vacantes - 1; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            &laquo; Anterior
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item-custom">
                        <span class="page-link-custom disabled">&laquo; Anterior</span>
                    </li>
                <?php endif; ?>

                <!-- Números de página -->
                <?php for ($i = 1; $i <= $total_paginas_vacantes; $i++): ?>
                    <li class="page-item-custom <?php echo $i == $pagina_actual_vacantes ? 'active' : ''; ?>">
                        <a class="page-link-custom" href="?pagina_vacantes=<?php echo $i; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Botón Siguiente -->
                <?php if ($pagina_actual_vacantes < $total_paginas_vacantes): ?>
                    <li class="page-item-custom">
                        <a class="page-link-custom" href="?pagina_vacantes=<?php echo $pagina_actual_vacantes + 1; ?>&tab=<?php echo $tab_activa; ?>&_preserve_chat=1">
                            Siguiente &raquo;
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item-custom">
                        <span class="page-link-custom disabled">Siguiente &raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>