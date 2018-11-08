<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\CModule::IncludeModule("expertit.site");

$APPLICATION->IncludeComponent(
    "skalar:router",
    "",
    []
);