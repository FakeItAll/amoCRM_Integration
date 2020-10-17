<?php

require_once 'vendor/autoload.php';
require_once 'lib/functions.php';
require_once 'lib/api_client.php';
require_once 'lib/handlers.php';

//use AmoCRM\Exceptions\AmoCRMApiException;

function main()
{
    init();

    [$apiClient, $accessToken] = getApiClient();
    handlersExecute($apiClient);

    $leadsService = $apiClient->leads();
    $usersService = $apiClient->users();

    $view = [];
    $view['leads'] = [];
    $view['users'] = [];
    foreach ($leadsService->get()->all() as $lead) {
        $leadArr = [];
        $leadArr['id'] = $lead->id;
        $leadArr['name'] = $lead->name;
        $view['leads'][] = $leadArr;
    }

    foreach ($usersService->get()->all() as $user) {
        $userArr = [];
        $userArr['id'] = $user->id;
        $userArr['name'] = $user->name;
        $view['users'][] = $userArr;
    }

    return $view;
}

$view = main();

?>

<html>
<head>
    <title>Добавление</title>
    <meta charset="utf-8"/>
    <style>
        form {
            display: inline-block;
            border: 1px solid #000;
            margin: 0;
            padding: .5rem;
        }
        div {
            margin: .5rem 0;
        }
    </style>
</head>
<body>
<h1>Добавить</h1>
<a href="/">Все</a>
<div>
    <form method="post">
        <h4>Добавить лид</h4>
        <div><label>Название: <input name="name" /></label></div>
        <div>
            <label>Ответственный:
                <select name="user_id">
                    <?php foreach ($view['users'] as $user) { ?>
                        <option value="<?=$user['id']?>"><?=$user['name']?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div><button name="add_lead" value="1">Создать</button></div>
    </form>
    <form method="post">
        <h4>Добавить описание к лиду</h4>
        <div>
            <label>Лид:
                <select name="lead_id">
                    <?php foreach ($view['leads'] as $lead) { ?>
                        <option value="<?=$lead['id']?>"><?=$lead['name']?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div><label>Описание: <textarea name="description"></textarea></label></div>
        <div><button name="add_desc" value="1">Применить</button></div>
    </form>
</div>
<div>
    <form method="post">
        <h4>Добавить компанию</h4>
        <div><label>Название: <input name="name" /></label></div>
        <div><label>Город: <input name="city" /></label></div>
        <div>
            <label>Лид:
                <select name="lead_id">
                    <?php foreach ($view['leads'] as $lead) { ?>
                        <option value="<?=$lead['id']?>"><?=$lead['name']?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div><button name="add_company" value="1">Добавить</button></div>
    </form>
    <form method="post">
        <h4>Добавить контакт</h4>
        <div><label>Имя: <input name="name" /></label></div>
        <div><label>Телефон: <input name="phone" /></label></div>
        <div><label>Email: <input name="email" /></label></div>
        <div>
            <label>Лид:
                <select name="lead_id">
                    <?php foreach ($view['leads'] as $lead) { ?>
                        <option value="<?=$lead['id']?>"><?=$lead['name']?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div><button name="add_contact" value="1">Добавить</button></div>
    </form>
</div>
<div>
    <form method="post">
        <h4>Создать тестовую задачу</h4>
        <div>
            <label>Лид:
                <select name="lead_id">
                    <?php foreach ($view['leads'] as $lead) { ?>
                        <option value="<?=$lead['id']?>"><?=$lead['name']?></option>
                    <?php } ?>
                </select>
            </label>
        </div>
        <div><button name="add_task" value="1">Добавить</button></div>
    </form>
</div>
</body>
</html>
