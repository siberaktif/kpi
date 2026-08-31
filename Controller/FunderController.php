<?php
namespace Kanboard\Plugin\KPI\Controller;

use Kanboard\Controller\BaseController;

class FunderController extends BaseController
{
    public function index()
    {
        $funders = $this->multiProjectModel->getAllFunderInfo();

        $this->response->html(
            $this->helper->layout->app(
                'KPI:funder/funder_list',
                [
                    'title'   => t('Projects'),
                    'funders' => $funders,
                ]
            )
        );
    }

    public function create()
    {
        $this->response->html(
            $this->template->render('KPI:funder/create', [
                'values' => [],
                'errors' => [],
            ])
        );
    }

    public function save()
    {
        $values = $this->request->getValues();
        $errors = [];

        if (empty($values['project_name'])) {
            $errors['project_name'][] = t('Project name is required.');
        }

        if (empty($values['funder_name'])) {
            $errors['funder_name'][] = t('Funder name is required.');
        }

        if (!empty($errors)) {
            return $this->response->html(
                $this->template->render('KPI:funder/create', [
                    'values' => $values,
                    'errors' => $errors,
                ])
            );
        }

        // Tarihleri güvenli şekilde integer'a çevir (boşsa NULL yap)
        $values['date_started']   = !empty($values['date_started']) ? strtotime($values['date_started'] . ' 00:00:00') : null;
        $values['date_completed'] = !empty($values['date_completed']) ? strtotime($values['date_completed'] . ' 00:00:00') : null;

        // year_duration integer olmalı; boşsa NULL
        $values['year_duration']  = isset($values['year_duration']) && $values['year_duration'] !== '' ? (int) $values['year_duration'] : null;

        $this->multiProjectModel->addFunder($values);

        $this->flash->success(t('Funder created successfully.'));

        return $this->response->redirect(
            $this->helper->url->to('FunderController', 'index', []),
            true
        );
    }

    public function edit()
    {
        $id = $this->request->getIntegerParam('funder_id');
        $funder = $this->multiProjectModel->getFunderInfoById($id);

        $this->response->html(
            $this->template->render('KPI:funder/edit', [
                'values' => $funder,
                'errors' => [],
            ])
        );
    }

    public function update()
    {
        $values = $this->request->getValues();
        $errors = [];

        if (empty($values['project_name'])) {
            $errors['project_name'][] = t('Project name is required.');
        }

        if (empty($values['funder_name'])) {
            $errors['funder_name'][] = t('Funder name is required.');
        }

        if (!empty($errors)) {
            return $this->response->html(
                $this->template->render('KPI:funder/edit', [
                    'values' => $values,
                    'errors' => $errors,
                ])
            );
        }

        // Tarihleri güvenli şekilde integer'a çevir (boşsa NULL yap)
        $values['date_started']   = !empty($values['date_started']) ? strtotime($values['date_started'] . ' 00:00:00') : null;
        $values['date_completed'] = !empty($values['date_completed']) ? strtotime($values['date_completed'] . ' 00:00:00') : null;
        $values['year_duration']  = isset($values['year_duration']) && $values['year_duration'] !== '' ? (int) $values['year_duration'] : null;

        $this->multiProjectModel->updateFunder($values['id'], $values);

        $this->flash->success(t('Funder updated successfully.'));

        return $this->response->redirect(
            $this->helper->url->to('FunderController', 'index', []),
            true
        );
    }

    public function remove()
    {
    }
}
