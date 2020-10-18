<?php

require_once 'vendor/autoload.php';
require_once 'lib/functions.php';
require_once 'lib/api_client.php';

use AmoCRM\Exceptions\AmoCRMApiException;

function main()
{
    init();

    [$apiClient, $accessToken] = getApiClient();

    $leadsService = $apiClient->leads();
    $companiesService = $apiClient->companies();
    $contactsService = $apiClient->contacts();
    $usersService = $apiClient->users();

    $view = [];

    $ownerDetails = $apiClient->getOAuthClient()->getResourceOwner($accessToken);
    $view['user'] = $ownerDetails->getName();
    $view['leads'] = [];
    $descFieldId = _env('DESC_FIELD_ID');
    try {
        foreach ($leadsService->get()->all() as $lead) {
            $leadArr = [];
            $leadArr['name'] = $lead->name;
            $leadArr['price'] = $lead->price;
            $leadArr['company'] = [];
            $leadArr['contacts'] = [];
            $leadArr['user'] = $usersService->getOne($lead->responsibleUserId)->name;
            $leadArr['desc'] = getCustomFieldValueById($lead, $descFieldId);
            foreach ($leadsService->getLinks($lead)->all() as $link) {
                $linkType = $link->toEntityType;
                $linkId = $link->toEntityId;
                if ($linkType == 'companies') {
                    $company = $companiesService->getOne($linkId);
                    $companyArr = [];
                    $companyArr['name'] = $company->name;
                    $companyArr['city'] = getCustomFieldValue($company, 'ADDRESS');
                    $leadArr['company'] = $companyArr;
                } elseif ($linkType == 'contacts') {
                    $contact = $contactsService->getOne($linkId);
                    $contactArr = [];
                    $contactArr['name'] = $contact->name;
                    $contactArr['phone'] = getCustomFieldValue($contact, 'PHONE');
                    $contactArr['email'] = getCustomFieldValue($contact, 'EMAIL');
                    $leadArr['contacts'][] = $contactArr;
                }
            }
            $view['leads'][] = $leadArr;
        }
    }
    catch (AmoCRMApiException $e) {
        printError($e);
    }

    return $view;
}

$view = main();

?>
<html>
<head>
    <title>Лиды</title>
    <meta charset="utf-8"/>
</head>
<body>
<h1>Лиды пользователя <?=$view['user']?>:</h1>
<a href="/add.php">Добавить</a>
<?php foreach ($view['leads'] as $lead) { ?>
    <div style="border: 1px solid #000; margin-bottom: 1rem; padding: 1rem">
        <div><b><?=$lead['name']?></b> (<?=$lead['user']?>)</div>
        <div><?=$lead['price']?> руб.</div>
        <div style="margin: .75rem 0">
            <?php foreach ($lead['contacts'] as $contact) { ?>
                <span style="border: 1px solid #000; padding: .75rem; display: inline-block">
                    <div><b><?=$contact['name']?></b></div>
                    <span><?=$contact['phone']?></span>
                    <span><?=$contact['email']?></span>
                </span>
            <?php } ?>
        </div>
        <div><i><?=$lead['desc']?></i></div>
        <?php if (!empty($company = $lead['company'])) { ?>
            <div style="border-top: 1px solid #333; margin-top: 1rem">
                <span><?=$company['name']?> (<?=$company['city']?>)</span>
            </div>
        <?php } ?>
    </div>
<?php } ?>
</body>
</html>
