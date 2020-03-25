<?php declare(strict_types=1);

namespace MoorlOrderNotice;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Doctrine\DBAL\Connection;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class MoorlOrderNotice extends Plugin
{

    public function install(InstallContext $installContext): void
    {
        $shopwareContext = $installContext->getContext();
        $this->addCustomFields($shopwareContext);

        parent::install($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
    }


    private function addCustomFields(Context $context): void
    {
        $repo = $this->container->get('custom_field.repository');

        $customFieldIds = $this->getCustomFieldIds($repo, $context);

        if ($customFieldIds->getTotal() !== 0) {
            return;
        }

        $repo = $this->container->get('custom_field_set.repository');

        $repo->create([[
            'name' => 'order_notice',
            'config' => [
                'label' => [
                    'en-GB' => 'Order Notice',
                    'de-DE' => 'Order Notice',
                ],
            ],
            'relations' => [
                ['entityName' => 'order']
            ],
            'customFields' => [
                [
                    'name' => 'order_notice_notice',
                    'type' => 'text',
                    'config' => [
                        'componentName' => 'sw-text-editor',
                        'customFieldType' => 'textEditor',
                        'customFieldPosition' => 1,
                        'label' => [
                            'en-GB' => 'Order Notice',
                            'de-DE' => 'Bestellnotiz',
                        ],
                    ]
                ]
            ]
        ]], $context);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        $shopwareContext = $uninstallContext->getContext();
        $this->removeCustomFields($shopwareContext);

        parent::uninstall($uninstallContext);
    }

    private function removeCustomFields(Context $context)
    {
        $repo = $this->container->get('custom_field.repository');

        $customFieldIds = $this->getCustomFieldIds($repo, $context);

        if ($customFieldIds->getTotal() == 0) {
            return;
        }

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $customFieldIds->getIds());
        $repo->delete($ids, $context);

        $repo = $this->container->get('custom_field_set.repository');

        $customFieldSetIds = $this->getCustomFieldSetIds($repo, $context);

        if ($customFieldSetIds->getTotal() == 0) {
            return;
        }

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $customFieldSetIds->getIds());
        $repo->delete($ids, $context);
    }

    private function getCustomFieldIds(EntityRepositoryInterface $customFieldRepository, Context $context): IdSearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new ContainsFilter('name', 'order_notice'));

        return $customFieldRepository->searchIds($criteria, $context);
    }

    private function getCustomFieldSetIds(EntityRepositoryInterface $customFieldSetRepository, Context $context): IdSearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', 'order_notice'));

        return $customFieldSetRepository->searchIds($criteria, $context);
    }

}
