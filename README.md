# router
Router component for bitrix
# use
copy vendor/skalar/router/components/skalar/router folder 
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

work with templates .default (or another template)
routes configure config/routes.yaml
rest path must have /rest as first part and name controller as second
rest action is method request (get, post, ...)
all action have two arguments: Symfony\Component\HttpFoundation\Request $request, array $state; return array $state

params:
BASE_URL - routing folder (optional)
you also add rule to urlrewrite.php
array (
    'CONDITION' => '#^/path/#',
    'ID' => NULL,
    'PATH' => '/path/index.php',
    'SORT' => 90,
),





