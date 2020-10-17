<?php

require_once 'functions.php';

use AmoCRM\Models\LeadModel;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\TaskModel;
use AmoCRM\Collections\LinksCollection;
use AmoCRM\Helpers\EntityTypesInterface;

function handlersExecute($apiClient)
{
    $leadsService = $apiClient->leads();
    $companiesService = $apiClient->companies();
    $contactsService = $apiClient->contacts();
    $tasksService = $apiClient->tasks();

    if (!empty($_POST['add_lead'])) {
        $lead = new LeadModel();
        $lead->setName($_POST['name']);
        $lead->setResponsibleUserId($_POST['user_id']);
        $leadsService->addOne($lead);
    }
    elseif (!empty($_POST['add_desc'])) {
        $descFieldId = _env('DESC_FIELD_ID');
        $lead = $leadsService->getOne($_POST['lead_id']);
        setCustomFieldValueById($lead, $descFieldId, $_POST['description']);
        $leadsService->updateOne($lead);
    }
    elseif (!empty($_POST['add_company'])) {
        $company = new CompanyModel();
        $company->setName($_POST['name']);
        setCustomFieldValue($company, 'ADDRESS', $_POST['city']);
        $companiesService->addOne($company);

        $lead = $leadsService->getOne($_POST['lead_id']);
        $leadsService->link($lead, (new LinksCollection())->add($company));
    }
    elseif (!empty($_POST['add_contact'])) {
        $contact = new ContactModel();
        $contact->setName($_POST['name']);
        setCustomFieldValue($contact, 'PHONE', $_POST['phone']);
        setCustomFieldValue($contact, 'EMAIL', $_POST['email']);
        $contactsService->addOne($contact);
        $lead = $leadsService->getOne($_POST['lead_id']);
        $leadsService->link($lead, (new LinksCollection())->add($contact));
    }
    elseif (!empty($_POST['add_task'])) {
        $name = 'Тестовая задача';
        $type = TaskModel::TASK_TYPE_ID_CALL;
        $dataTime = strtotime(date('Y-m-d 23:59:59'));

        $lead = $leadsService->getOne($_POST['lead_id']);
        $task = new TaskModel();

        $task->setTaskTypeId($type)
            ->setText($name)
            ->setCompleteTill($dataTime)
            ->setEntityType(EntityTypesInterface::LEADS)
            ->setEntityId($lead->id)
            ->setResponsibleUserId($lead->responsibleUserId);
        $tasksService->addOne($task);

    }

}