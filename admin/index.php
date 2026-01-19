<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../inc/Database.php';

/* ===== Admin auth model ===== */
require_once __DIR__ . '/modelAdmin/modelAdmin.php';

/* ===== Admin domain models ===== */
require_once __DIR__ . '/modelAdmin/modelAdminMovies.php';
require_once __DIR__ . '/modelAdmin/modelAdminUsers.php';
require_once __DIR__ . '/modelAdmin/modelAdminComments.php';
require_once __DIR__ . '/modelAdmin/modelAdminFavorites.php';
require_once __DIR__ . '/modelAdmin/modelAdminGenres.php';
require_once __DIR__ . '/modelAdmin/modelAdminRatings.php';

/* ===== Admin controllers ===== */
require_once __DIR__ . '/controllerAdmin/controllerAdmin.php';
require_once __DIR__ . '/controllerAdmin/controllerAdminMovies.php';
require_once __DIR__ . '/controllerAdmin/controllerAdminUsers.php';

/* ===== Routing ===== */
require_once __DIR__ . '/routeAdmin/routingAdmin.php';
