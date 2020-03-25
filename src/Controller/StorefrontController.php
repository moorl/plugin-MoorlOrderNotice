<?php declare(strict_types=1);

namespace MoorlOrderNotice\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Symfony\Component\HttpFoundation\Session\Session;
use Shopware\Storefront\Framework\Cache\Annotation\HttpCache;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @RouteScope(scopes={"storefront"})
 */
class StorefrontController extends \Shopware\Storefront\Controller\StorefrontController
{

    /**
     * @Route("moorl-order-notice/checkout/notice", name="moorl.order-notice.checkout.notice", methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */

    public function notice(RequestDataBag $requestDataBag, SalesChannelContext $salesChannelContext): Response
    {

        $session = new Session();

        $notice = $requestDataBag->get('notice');

        $session->set('moorl-order-notice.notice', $notice);

        // Möglichkeit 1
        //$cartData = $cart->getData();
        //$cartData->set('notice', $notice);
        //$cart->setData($cartData);

        // Möglichkeit 2
        //$cart->addExtension('moorlOrderNotice', new ArrayEntity(['notice' => $notice]));
        //$this->cartService->setCart($cart);
        //$this->cartService->recalculate($cart, $salesChannelContext);

        //dump($cart); exit;

        $this->addFlash('success', $this->trans('moorl-order-notice.noticeSaved'));

        return $this->redirectToRoute('frontend.checkout.confirm.page');

    }

}
