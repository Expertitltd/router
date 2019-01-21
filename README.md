# router
Router component for bitrix
# use
copy vendor/skalar/router/examples/router folder 
into your namespace (example /local/components/skalar)

configure .htaccess redirects to index.php

call component in index.php:
$APPLICATION->IncludeComponent(
    "skalar:router",
     "",
     [
        'BASE_URL' => '/path'
     ],
     false
 );
example vendor/skalar/router/examples/root/index.php
params:
BASE_URL - routing folder (optional)
you also add rule to urlrewrite.php
array (
    'CONDITION' => '#^/path/#',
    'ID' => NULL,
    'PATH' => '/path/index.php',
    'SORT' => 90,
),

Routing basic on the components symfony "symfony/http-foundation" and "symfony/routing", for more information read documentation for these packages.

Work with templates .default (or another template).
# public
Routes configure config/routes.yaml.
Controllers must extends Skalar\Controller\PublicController and have namespace Skalar\Controller.
They should be located in the folder "controllers".
All controller actions receive 2 params: Symfony\Component\HttpFoundation\Request $request and array $state.
You can get any request arguments: $request->get("param");
All controllers return array $state.
You can set headers in controller use protected property "headers" -  Symfony\Component\HttpFoundation\ResponseHeaderBag.
You can set response status use controller method setStatus()
By default router response is json. You can override controller the getRender() method. It returns a closure that renders content.
Closure get 2 arguments: array $state and string $url, return string content;
# middleware
Middleware calling before any public controller action. Example in folder middleware.
Middleware class must have only one method __invoke(). This method work like controller action.
# api 
api has its own routing. You must extends Skalar\Routing\AbstractApi and implement method getRoutes(). Your class must be located in api folder.
skalar/router have two implemented APIs: rest and graphQl (Skalar/Api/Rest and Skalar/Api/GraphQl). You can take them as a basis for implementing your APIs.
# rest
Work folder rest. All rest controller have two methods get and post. They take 0, 1 or 2 arguments.
Use $request->get('param1') and $request->get('param2'). Route for rest is like /rest/test/{param1}/ where test is name of controller in lowercase.
You can override rest api in the folder api in your class extends Skalar/Api/Rest or directly Skalar\Routing\AbstractApi.
# graphQl
Work folder graphql. Route /graphql/.
In folder graphql located two basic type. Another types must be realized as these.
Type - is class which extends GraphQL\Type\Definition\ObjectType or another. It must have namespace Skalar\Type.
It include in other type as Skalar\GraphQL\Types::catalog() where catalog is name of class type in lowercase.
For more information, read the documentation for the package "webonyx/graphql-php" http://webonyx.github.io/graphql-php/.





