<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\BonusSale;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Enums\User\UserRoles;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\OrderPaymentMethod;
use App\Enums\Order\OrderShippingMethod;
use App\Services\Order\OrderServiceInterface;
use App\Repositories\Product\ProductRepository;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Repositories\BonusPolicy\BonusPolicyRepositoryInterface;
use App\Admin\Repositories\DiscountAgent\DiscountAgentRepositoryInterface;
use App\Admin\Repositories\DiscountSeller\DiscountSellerRepositoryInterface;
use App\Admin\Repositories\BonusPolicyDetail\BonusPolicyDetailRepositoryInterface;
use App\Admin\Repositories\BonusSale\BonusSaleRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Models\User;

class OrderService implements OrderServiceInterface
{
    protected $productRepository;
    protected $disCountAgent;
    protected $repoBonusPolicy;
    protected $repoBonusDetailPolicy;
    protected $repoOrderDetail;
    protected $repoBonusSales;
    protected $repoOrder;
    protected $repoDiscoutSeller;
    protected $repoCart;
    public function __construct(
        ProductRepository $productRepository,
        DiscountAgentRepositoryInterface $disCountAgent,
        BonusPolicyRepositoryInterface $repoBonusPolicy,
        BonusPolicyDetailRepositoryInterface $repoBonusDetailPolicy,
        OrderDetailRepositoryInterface $repoOrderDetail,
        BonusSaleRepositoryInterface $repoBonusSales,
        OrderRepositoryInterface $repoOrder,
        DiscountSellerRepositoryInterface $repoDiscoutSeller,
        ShoppingCartRepositoryInterface $repoCart,
    ) {
        $this->productRepository = $productRepository;
        $this->disCountAgent = $disCountAgent;
        $this->repoBonusPolicy = $repoBonusPolicy;
        $this->repoBonusDetailPolicy  = $repoBonusDetailPolicy;
        $this->repoOrderDetail = $repoOrderDetail;
        $this->repoBonusSales = $repoBonusSales;
        $this->repoOrder = $repoOrder;
        $this->repoDiscoutSeller = $repoDiscoutSeller;
        $this->repoCart = $repoCart;
    }

    public function createOrder($productId, $qty, $request)
    {

        $roles = auth()->user()->roles;
        if ($roles === UserRoles::Agent) {

            $data = $request->all();
            $data['payment_method'] = $request->input('payment_method') === 'cod'
                ? OrderPaymentMethod::COD
                : OrderPaymentMethod::BankTransfer;
            $data['shipping_method'] = $request->input('shipping_method') === 'road'
                ? OrderShippingMethod::Road
                : OrderShippingMethod::Air;

            $product = $this->productRepository->find($productId);
            $unit = $product->unit;
            $unitName = Str::lower($unit->name);
            $typeUnit = $unit->value;
            $findBonus = $this->repoBonusPolicy->find($typeUnit);
            $idBonus = $findBonus->id;
            $detailBonus = $this->repoBonusDetailPolicy->latest($idBonus);

            if ($qty === 0) {

                $subTotal = $product->price_promotion * 1;
            } else {

                $subTotal = $product->price_promotion * $qty;
            }

            $lever = 0;

            if ($subTotal > 10000000) {
                $lever = 1;
            } elseif ($subTotal > 5000000) {
                $lever = 2;
            } else {

                $data['sub_total'] = $product->price_promotion;
                $data['discount'] = 0;
                $data['total'] = $subTotal;
                $data['status'] = OrderStatus::Unprocessed;
                $data['user_id'] = auth()->user()->id;
                $data['customer_role'] = auth()->user()->roles;
                $data['note'] = $request->input('note');

                $order = $this->repoOrder->create($data);
                $orderDetail = [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'qty' => $qty,
                    'unit_price' => $product->price_promotion,
                    'unit' => $product->unit,
                    'detail' => $product,
                ];

                OrderDetail::create($orderDetail);
                $orders = Order::where('user_id', auth()->user()->id)->get();
                $totalQtyUnit1 = 0;
                $totalQtyUnit2 = 0;
                $reward = 0;
                foreach ($orders as $order) {
                    $orderDetails = $order->details()->get(['qty', 'unit']);
                    foreach ($orderDetails as $item) {
                        if ($item->unit === 1) {
                            $totalQtyUnit1 += $item->qty;
                        } else {
                            $totalQtyUnit2 += $item->qty;
                        }
                    }
                }

                $bonusPointPairs = [];
                foreach ($detailBonus as $item) {
                    $bonusPointPairs[] = ['bonus' => $item->bonus, 'point' => $item->point];
                }


                foreach ($bonusPointPairs as $pair) {
                    if ($totalQtyUnit1 >= $pair['point']) {
                        $reward += $pair['bonus'];
                    } elseif ($totalQtyUnit2 >= $pair['point']) {
                        $reward += $pair['bonus'];
                    }
                }

                $bonusData = [
                    'user_id' => auth()->user()->id,
                    'qty_pail' => $totalQtyUnit2,
                    'qty_bottle' => $totalQtyUnit1,
                    'month' => now(),
                    'reward' => $reward
                ];

                $existingBonusData = $this->repoBonusSales->getBonusSaleOnUserID(auth()->user()->id);

                if (!$existingBonusData) {
                    $resultData = $this->repoBonusSales->create($bonusData);
                } else {
                    $bonusData['reward'] += $existingBonusData->reward;
                    $resultData = $this->repoBonusSales->update($existingBonusData->id, $bonusData);
                }

                return $order;
            }


            $discountAgent = $this->disCountAgent->findLevel($lever);
            $discountData = $discountAgent->discount_data;


            $priceWithDiscount = 0;
            if ($unitName === 'pail') {

                $quantity = max($qty, 1);
                $priceWithDiscount = ($discountData['pail'] / 100) * $product->price_promotion * $quantity;
            } else {
                $quantity = max($qty, 1);
                $priceWithDiscount = ($discountData['bottle'] / 100) * $product->price_promotion * $quantity;
            }



            $priceInShow = $subTotal - $priceWithDiscount;

            $data['sub_total'] = $product->price_promotion;
            $data['discount'] = $priceWithDiscount;
            $data['total'] = $priceInShow;
            $data['status'] = OrderStatus::Unprocessed;
            $data['user_id'] = auth()->user()->id;
            $data['customer_role'] = auth()->user()->roles;
            $data['note'] = $request->input('note');

            $order = Order::create($data);

            $orderDetail = [
                'order_id' => $order->id,
                'product_id' => $productId,
                'qty' => $qty,
                'unit_price' => $product->price_promotion,
                'unit' => $product->unit,
                'detail' => $product,
            ];

            OrderDetail::create($orderDetail);
            return $order;

            /// seller
        } else {
            $data = $request->all();
            $data['payment_method'] = $request->input('payment_method') === 'cod'
                ? OrderPaymentMethod::COD
                : OrderPaymentMethod::BankTransfer;
            $data['shipping_method'] = $request->input('shipping_method') === 'road'
                ? OrderShippingMethod::Road
                : OrderShippingMethod::Air;
            $product = $this->productRepository->find($productId);
            $discountSeller = $this->repoDiscoutSeller->getBonus($productId);
            $sl = 0;
            $qty_donate = 0;
            $discout = 0;
            $slUpdate = 0;
            foreach ($discountSeller as $item) {
                $sl = $item->qty;
                $qty_donate = $item->qty_donate;
            };


            if ($qty >= $sl) {
                // $discout += $qty_donate * $qty;
                $slUpdateRounded = round($qty / 100);
                $qty_donate = intval($qty_donate * $slUpdateRounded);
                $totprice = $product->price_promotion  * $qty;
                $data['sub_total'] = $product->price_promotion;
                $data['discount'] = 0;
                $data['total'] = $totprice;
                $data['bonus'] = $qty_donate;
                $data['status'] = OrderStatus::Unprocessed;
                $data['user_id'] = auth()->user()->id;
                $data['customer_role'] = auth()->user()->roles;
                $data['note'] = $request->input('note');
                $order = $this->repoOrder->create($data);

                $oderDetail = [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'unit_price' => $product->price_promotion,
                    'unit' => $product->unit,
                    'detail' => $product,
                    'qty' => $qty,
                    'qty_donate' => $qty_donate
                ];
                $orderDetailData = $this->repoOrderDetail->create($oderDetail);

                return $order;
            } else {
                $data['sub_total'] = $product->price_promotion;
                $data['discount'] = 0;
                $data['total'] = $product->price_promotion;
                $data['status'] = OrderStatus::Unprocessed;
                $data['bonus'] = $qty_donate;
                $data['user_id'] = auth()->user()->id;
                $data['customer_role'] = auth()->user()->roles;
                $data['note'] = $request->input('note');
                $order = $this->repoOrder->create($data);

                $oderDetail = [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'unit_price' => $product->price_promotion,
                    'unit' => $product->unit,
                    'detail' => $product,
                    'qty' => $qty,
                    'qty_donate' => 0
                ];
                $orderDetailData = $this->repoOrderDetail->create($oderDetail);

                return $order;
            }
        }
    }





    //// thanh toan gio Cart

    public function payCart($request, $repoCart, $repoOrders, $repoProduct)
    {

        $roles = auth()->user()->roles;
        if ($roles === UserRoles::Agent) {

            $data = $request->all();
            $data['payment_method'] = $request->input('payment_method') == 'cod' ? OrderPaymentMethod::COD : OrderPaymentMethod::BankTransfer;
            $data['shipping_method'] = $request->input('shipping_method') == 'road' ? OrderShippingMethod::Road : OrderShippingMethod::Air;
            $cartProducts = $this->repoCart->findByUserId(auth()->user()->id);
            $orderDetailData = [];
            $subTotal = 0;
            $qty = [];
            $qty_donate_total = 0;
            $lever = 0;
            foreach ($cartProducts as $cartProduct) {
                $productId = $cartProduct->product_id;
                $product = $this->productRepository->find($productId);

                $priceCart = $cartProduct->pluck('price_selling')->sum();
                if ($priceCart > 10000000) {
                    $lever = 1;
                } elseif ($priceCart > 5000000 && $lever !== 1) {
                    $lever = 2;
                } else {
                    $discount = 0;
                }
                $discount = $this->calculateDiscount($lever, $product->unit, $cartProduct->price_selling);
                $finalPrice = $priceCart - $discount;
                $orderDetailData[] = [
                    'product_id' => $productId,
                    'unit_price' => $product->price_promotion,
                    'unit' => $product->unit,
                    'detail' => $product,
                    'qty' => $cartProduct->quantity,
                    'qty_donate' => 0,
                ];
            }

            $data['sub_total']  = $product->price_promotion;
            $data['discount']  = $discount;
            $data['bonus'] = $qty_donate_total;
            $data['total'] = $finalPrice;
            $data['status'] = OrderStatus::Unprocessed;
            $data['user_id'] = auth()->user()->id;
            $data['customer_role'] = auth()->user()->roles;
            $data['note'] = $request->input('note');


            $order = $this->repoOrder->create($data);
            foreach ($orderDetailData as $orderDetail) {
                $orderDetail['order_id'] = $order->id;
                $this->repoOrderDetail->create($orderDetail);
            }

            $repoCart->clear();

            return $order;
        } else {
            /// day la seller
            $data = $request->all();
            $data['payment_method'] = $request->input('payment_method') == 'cod' ? OrderPaymentMethod::COD : OrderPaymentMethod::BankTransfer;
            $data['shipping_method'] = $request->input('shipping_method') == 'road' ? OrderShippingMethod::Road : OrderShippingMethod::Air;
            $cartProducts = $repoCart->getAll();
            $orderDetailData = [];
            $subTotal = 0;
            $qty = 0;
            $qty_donate_total = 0;

            foreach ($cartProducts as $cartProduct) {
                $productId = $cartProduct->product_id;
                $product = $this->productRepository->find($productId);
                $qty += $cartProduct->quantity;
                $subTotal += $product->price_promotion * $cartProduct->quantity;
                $discountSeller = $this->repoDiscoutSeller->getBonus($productId);
                $qty_donate = 0;


                foreach ($discountSeller as $value) {
                    if ($cartProduct->quantity > $value->qty) {
                        $qty_donate = round($cartProduct->quantity / 100) * $value->qty_donate;
                    } else {
                        $qty_donate = $value->qty_donate;
                    }
                }

                $orderDetailData[] = [
                    'product_id' => $productId,
                    'unit_price' => $product->price_promotion,
                    'unit' => $product->unit,
                    'detail' => $product,
                    'qty' => $cartProduct->quantity,
                    'qty_donate' => $qty_donate,
                ];
                $qty_donate_total += intval($qty_donate);

            }

            $data['sub_total']  = $subTotal;
            $data['discount']  = $qty_donate;
            $data['bonus'] = $qty_donate_total;
            $data['total'] = $subTotal;
            $data['status'] = OrderStatus::Unprocessed;
            $data['user_id'] = auth()->user()->id;
            $data['customer_role'] = auth()->user()->roles;
            $data['note'] = $request->input('note');


            $order = $this->repoOrder->create($data);
            foreach ($orderDetailData as $orderDetail) {
                $orderDetail['order_id'] = $order->id;
                $this->repoOrderDetail->create($orderDetail);
            }

            $repoCart->clear();

            return $order;
        }
    }






    public function calculateDiscount($lever, $unitName, $price)
    {
        $discountAgent = $this->disCountAgent->findLevel((int)$lever);
        if (!$discountAgent) {
            return 0;
        }

        $discountData = $discountAgent->discount_data ?? null;

        if (!$discountData) {
            return 0;
        }

        if ($lever === 1 || $lever === 2) {
            $discountPercent = ($unitName === 'pail') ? $discountData['pail'] / 100 : $discountData['bottle'] / 100;
        } else {
            $discountPercent = 0;
        }

        return $discountPercent * $price;
    }
}
