<?php

namespace Crm\SubscriptionsModule\Components;

use Crm\ApplicationModule\Components\Graphs\GoogleBarGraphControlFactoryInterface;
use Crm\ApplicationModule\Widget\BaseLazyWidget;
use Crm\ApplicationModule\Widget\LazyWidgetManager;
use Nette\Database\Explorer;
use Nette\Localization\Translator;

/**
 * This widget fetches user source from subscriptions and renders
 * google graph with interval controls.
 *
 * @package Crm\SubscriptionsModule\Components
 */
class ActualSubscribersRegistrationSourceStatsWidget extends BaseLazyWidget
{
    private $templateName = 'actual_subscribers_registration_source_stats_widget.latte';

    private $factory;

    private $translator;

    private $database;

    public function __construct(
        LazyWidgetManager $lazyWidgetManager,
        GoogleBarGraphControlFactoryInterface $factory,
        Translator $translator,
        Explorer $database
    ) {
        parent::__construct($lazyWidgetManager);

        $this->factory = $factory;
        $this->translator = $translator;
        $this->database = $database;
    }

    public function render($params)
    {
        $this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . $this->templateName);
        $this->template->asyncLoad = $params['asyncLoad'] ?? true;
        $this->template->render();
    }

    public function createComponentGoogleUserSubscribersRegistrationSourceStatsGraph()
    {
        $this->getPresenter()->getSession()->close();
        $control = $this->factory->create();

        $results = $this->database->table('subscriptions')
            ->where('subscriptions.start_time < ?', $this->database::literal('NOW()'))
            ->where('subscriptions.end_time > ?', $this->database::literal('NOW()'))
            ->group('user.source')
            ->select('user.source, count(*) AS count')
            ->order('count DESC')
            ->fetchAll();

        $data = [];

        foreach ($results as $row) {
            $data[$row['source']] = $row['count'];
        }

        $control->addSerie($this->translator->translate('dashboard.users.active_sub_registrations.serie'), $data);

        return $control;
    }
}
