<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;

function init()
{
    session_start();
    (new Dotenv())->loadEnv('.env');
}

function getCustomFieldValue($model, $code)
{
    $customFields = $model
        ->getCustomFieldsValues();
    if (!$customFields) {
        return null;
    }
    $field = $customFields
        ->getBy('fieldCode', $code);
    if (!$field) {
        return null;
    }
    $value = $field
        ->getValues()
        ->first()
        ->value;
    return $value;
}

function getCustomFieldValueById($model, $id)
{
    $customFields = $model
        ->getCustomFieldsValues();
    if (!$customFields) {
        return null;
    }
    $field = $customFields
        ->getBy('fieldId', (int)$id);
    if (!$field) {
        return null;
    }
    $value = $field
        ->getValues()
        ->first()
        ->value;
    return $value;
}

function setCustomFieldValue($model, $code, $value)
{
    $customFieldsValues = $model->getCustomFieldsValues() ?? new CustomFieldsValuesCollection();
    $textCustomFieldValueModel = new TextCustomFieldValuesModel();
    $textCustomFieldValueModel->setFieldCode($code);
    $textCustomFieldValueModel->setValues(
        (new TextCustomFieldValueCollection())
            ->add((new TextCustomFieldValueModel())->setValue($value))
    );
    $customFieldsValues->add($textCustomFieldValueModel);
    $model->setCustomFieldsValues($customFieldsValues);
    return $model;
}

function setCustomFieldValueById($model, $id, $value)
{
    $customFieldsValues = $model->getCustomFieldsValues() ?? new CustomFieldsValuesCollection();
    $textCustomFieldValueModel = new TextCustomFieldValuesModel();
    $textCustomFieldValueModel->setFieldId($id);
    $textCustomFieldValueModel->setValues(
        (new TextCustomFieldValueCollection())
            ->add((new TextCustomFieldValueModel())->setValue($value))
    );
    $customFieldsValues->add($textCustomFieldValueModel);
    $model->setCustomFieldsValues($customFieldsValues);
    return $model;
}

function printError(AmoCRMApiException $e)
{
    $errorTitle = $e->getTitle();
    $code = $e->getCode();
    $debugInfo = var_export($e->getLastRequestInfo(), true);

    $error = <<<EOF
Error: $errorTitle
Code: $code
Debug: $debugInfo
EOF;

    echo '<pre>' . $error . '</pre>';
}

function _env($key, $default = null)
{
    return !empty($_ENV[$key]) ? $_ENV[$key] : $default;
}

function dd($arr)
{
    var_dump($arr);
    die();
}