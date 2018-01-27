<?php

use Slim\Http\Request;
use Slim\Http\Response;
use ChurchCRM\dto\SystemURLs;
use Slim\Views\PhpRenderer;
use ChurchCRM\UserQuery;
use ChurchCRM\Slim\Middleware\AdminRoleAuthMiddleware;

$app->group('/admin/user', function () {
    $this->get('/{id}/apikey', 'viewAPIKey');
    $this->get('/{id}/apikey/', 'viewAPIKey');
    $this->get('/not-found', 'viewUserNotFound');
})->add(new AdminRoleAuthMiddleware());

function viewUserNotFound(Request $request, Response $response, array $args)
{
    $renderer = new PhpRenderer('templates/common/');

    $pageArgs = [
        'sRootPath' => SystemURLs::getRootPath(),
        'memberType' => "User",
        'id' => $request->getParam("id")
    ];

    return $renderer->render($response, 'not-found-view.php', $pageArgs);
}


function viewAPIKey(Request $request, Response $response, array $args)
{
    $renderer = new PhpRenderer('templates/admin/');

    $userId = $args["id"];
    $user = UserQuery::create()->findPk($userId);

    if (empty($user)) {
        return $response->withRedirect(SystemURLs::getRootPath() . "/v2/admin/user/not-found?id=".$args["id"]);
    }

    $pageArgs = [
        'sRootPath' => SystemURLs::getRootPath(),
        'user' => $user,
    ];

    return $renderer->render($response, 'user-api-key.php', $pageArgs);

}

