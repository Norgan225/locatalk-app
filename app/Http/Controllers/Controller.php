<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="LocaTalk API",
 *     version="1.0.0",
 *     description="API de communication d'entreprise sécurisée - LocaTalk",
 *     @OA\Contact(
 *         email="support@locatalk.com",
 *         name="Support LocaTalk"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Serveur de développement local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Entrez votre token Bearer obtenu via /api/login"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints d'authentification (login, logout, me)"
 * )
 *
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Statistiques et analytics du dashboard"
 * )
 *
 * @OA\Tag(
 *     name="Profile",
 *     description="Gestion du profil utilisateur"
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="Gestion des utilisateurs"
 * )
 *
 * @OA\Tag(
 *     name="Organizations",
 *     description="Gestion des organisations"
 * )
 *
 * @OA\Tag(
 *     name="Departments",
 *     description="Gestion des départements"
 * )
 *
 * @OA\Tag(
 *     name="Projects",
 *     description="Gestion des projets"
 * )
 *
 * @OA\Tag(
 *     name="Tasks",
 *     description="Gestion des tâches"
 * )
 *
 * @OA\Tag(
 *     name="Messages",
 *     description="Messagerie directe et canaux"
 * )
 *
 * @OA\Tag(
 *     name="Channels",
 *     description="Gestion des canaux de communication"
 * )
 *
 * @OA\Tag(
 *     name="Calls",
 *     description="Gestion des appels"
 * )
 *
 * @OA\Tag(
 *     name="Meetings",
 *     description="Gestion des réunions"
 * )
 *
 * @OA\Tag(
 *     name="Notifications",
 *     description="Gestion des notifications"
 * )
 */
abstract class Controller
{
    //
}
