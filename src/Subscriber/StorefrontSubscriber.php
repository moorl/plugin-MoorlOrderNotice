<?php declare(strict_types=1);

namespace MoorlOrderNotice\Subscriber;

use Composer\IO\NullIO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoadedEvent;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Content\Product\SalesChannel\ProductAvailableFilter;
use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;

class StorefrontSubscriber implements EventSubscriberInterface
{

    private $systemConfigService;
    private $orderRepository;
    private $connection;
    private $cartService;

    public function __construct(
        CartService $cartService,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $orderRepository,
        Connection $connection

    )
    {
        $this->cartService = $cartService;
        $this->systemConfigService = $systemConfigService;
        $this->orderRepository = $orderRepository;
        $this->connection = $connection;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutOrderPlacedEvent::class => 'onCheckoutOrderPlacedEvent'
        ];
    }

    public function onCheckoutOrderPlacedEvent(CheckoutOrderPlacedEvent $event): void
    {

        $session = new Session();

        $order = $event->getOrder();

        if (!$order) {
            return;
        }

        $notice = $session->get('moorl-order-notice.notice');

        if ($notice) {

            $session->set('moorl-order-notice.notice', null);

            $this->orderRepository->update([[
                'id' => $order->getId(),
                'customFields' => ['order_notice_notice' => $notice]
            ]], $event->getContext());

        }

    }

}
