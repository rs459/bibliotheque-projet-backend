<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

final class CustomOpenApiDecorator implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $paths = $openApi->getPaths();

        // POST /api/login_check - Authentification
        $loginPath = new Model\PathItem(
            ref: 'Login',
            post: new Model\Operation(
                operationId: 'postLogin',
                tags: ['Authentification'],
                summary: 'Se connecter et obtenir un token JWT',
                description: 'Authentifie un utilisateur avec son email et mot de passe, et retourne un token JWT.',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['username', 'password'],
                                'properties' => [
                                    'username' => [
                                        'type' => 'string',
                                        'format' => 'email',
                                        'example' => 'user@example.com',
                                        'description' => 'Adresse email de l\'utilisateur'
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'format' => 'password',
                                        'example' => 'Test123!'
                                    ]
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Authentification réussie',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => ['type' => 'string', 'example' => 'eyJ0eXAiOiJKV1QiLCJhbGc...'],
                                        'refresh_token' => ['type' => 'string', 'example' => 'def50200...']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => ['description' => 'Identifiants invalides ou utilisateur bloqué']
                ]
            )
        );
        $paths->addPath('/api/login_check', $loginPath);

        // POST /api/register - Inscription utilisateur
        $registerPath = new Model\PathItem(
            ref: 'Register',
            post: new Model\Operation(
                operationId: 'postRegister',
                tags: ['Authentification'],
                summary: 'Créer un nouveau compte utilisateur',
                description: 'Permet à un nouvel utilisateur de s\'inscrire avec un email et un mot de passe.',
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['email', 'password'],
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'format' => 'email',
                                        'example' => 'user@example.com'
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'format' => 'password',
                                        'minLength' => 6,
                                        'example' => 'Test123!',
                                        'description' => 'Doit contenir au moins 6 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial'
                                    ]
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '201' => [
                        'description' => 'Utilisateur créé avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'User registration successful']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => ['description' => 'Données invalides']
                ]
            )
        );
        $paths->addPath('/api/register', $registerPath);

        // GET /api/users - Liste des utilisateurs (Admin)
        $usersPath = new Model\PathItem(
            ref: 'Users',
            get: new Model\Operation(
                operationId: 'getUsers',
                tags: ['Utilisateurs'],
                summary: 'Lister tous les utilisateurs',
                description: 'Récupère la liste de tous les utilisateurs (réservé aux administrateurs).',
                security: [['bearerAuth' => []]],
                responses: [
                    '200' => [
                        'description' => 'Liste des utilisateurs',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer', 'example' => 1],
                                            'email' => ['type' => 'string', 'example' => 'admin@example.com'],
                                            'roles' => ['type' => 'array', 'items' => ['type' => 'string'], 'example' => ['ROLE_ADMIN']],
                                            'isBlocked' => ['type' => 'boolean', 'example' => false]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '403' => ['description' => 'Accès refusé (nécessite ROLE_ADMIN)']
                ]
            )
        );
        $paths->addPath('/api/users', $usersPath);

        // DELETE /api/users/me - Suppression du compte
        $deleteMePath = new Model\PathItem(
            ref: 'DeleteMe',
            delete: new Model\Operation(
                operationId: 'deleteUserMe',
                tags: ['Utilisateurs'],
                summary: 'Supprimer son propre compte',
                description: 'Permet à un utilisateur authentifié de supprimer son propre compte.',
                security: [['bearerAuth' => []]],
                responses: [
                    '200' => [
                        'description' => 'Compte supprimé avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'User deleted successfully']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => ['description' => 'Non authentifié']
                ]
            )
        );
        $paths->addPath('/api/users/me', $deleteMePath);

        // PATCH /api/users/{id}/block - Bloquer un utilisateur
        $blockPath = new Model\PathItem(
            ref: 'BlockUser',
            patch: new Model\Operation(
                operationId: 'patchUserBlock',
                tags: ['Utilisateurs'],
                summary: 'Bloquer un utilisateur',
                description: 'Permet à un administrateur de bloquer un utilisateur (empêche la connexion).',
                security: [['bearerAuth' => []]],
                parameters: [
                    new Model\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        schema: ['type' => 'integer'],
                        description: 'ID de l\'utilisateur à bloquer'
                    )
                ],
                responses: [
                    '200' => [
                        'description' => 'Utilisateur bloqué avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'User blocked successfully']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '403' => ['description' => 'Accès refusé ou tentative de bloquer un administrateur'],
                    '404' => ['description' => 'Utilisateur non trouvé']
                ]
            )
        );
        $paths->addPath('/api/users/{id}/block', $blockPath);

        // PATCH /api/users/{id}/unblock - Débloquer un utilisateur
        $unblockPath = new Model\PathItem(
            ref: 'UnblockUser',
            patch: new Model\Operation(
                operationId: 'patchUserUnblock',
                tags: ['Utilisateurs'],
                summary: 'Débloquer un utilisateur',
                description: 'Permet à un administrateur de débloquer un utilisateur.',
                security: [['bearerAuth' => []]],
                parameters: [
                    new Model\Parameter(
                        name: 'id',
                        in: 'path',
                        required: true,
                        schema: ['type' => 'integer'],
                        description: 'ID de l\'utilisateur à débloquer'
                    )
                ],
                responses: [
                    '200' => [
                        'description' => 'Utilisateur débloqué avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'User unblocked successfully']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '403' => ['description' => 'Accès refusé (nécessite ROLE_ADMIN)'],
                    '404' => ['description' => 'Utilisateur non trouvé']
                ]
            )
        );
        $paths->addPath('/api/users/{id}/unblock', $unblockPath);

        // GET /api/google-books/search - Recherche Google Books
        $googleSearchPath = new Model\PathItem(
            ref: 'GoogleBooksSearch',
            get: new Model\Operation(
                operationId: 'getGoogleBooksSearch',
                tags: ['Google Books'],
                summary: 'Rechercher des livres sur Google Books',
                description: 'Recherche des livres via l\'API Google Books.',
                security: [['bearerAuth' => []]],
                parameters: [
                    new Model\Parameter(
                        name: 'q',
                        in: 'query',
                        required: true,
                        schema: ['type' => 'string'],
                        description: 'Terme de recherche'
                    )
                ],
                responses: [
                    '200' => [
                        'description' => 'Résultats de recherche',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'items' => ['type' => 'array', 'items' => ['type' => 'object']]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => ['description' => 'Non authentifié']
                ]
            )
        );
        $paths->addPath('/api/google-books/search', $googleSearchPath);

        // POST /api/google-books/import - Import Google Books
        $googleImportPath = new Model\PathItem(
            ref: 'GoogleBooksImport',
            post: new Model\Operation(
                operationId: 'postGoogleBooksImport',
                tags: ['Google Books'],
                summary: 'Importer un livre depuis Google Books',
                description: 'Importe un livre trouvé sur Google Books dans la bibliothèque.',
                security: [['bearerAuth' => []]],
                requestBody: new Model\RequestBody(
                    required: true,
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['googleBookId'],
                                'properties' => [
                                    'googleBookId' => ['type' => 'string', 'example' => 'zyTCAlFPjgYC']
                                ]
                            ]
                        ]
                    ])
                ),
                responses: [
                    '201' => ['description' => 'Livre importé avec succès'],
                    '400' => ['description' => 'Données invalides'],
                    '401' => ['description' => 'Non authentifié']
                ]
            )
        );
        $paths->addPath('/api/google-books/import', $googleImportPath);

        return $openApi;
    }
}
