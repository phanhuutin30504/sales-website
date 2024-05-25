<?php

namespace App\Http\Controllers\Checkout;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\Order\OrderRequest;
use App\Admin\Http\Controllers\Controller;
use App\Services\Order\OrderServiceInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Admin\Repositories\DiscountAgent\DiscountAgentRepositoryInterface;
use App\Admin\Repositories\BonusPolicy\BonusPolicyRepositoryInterface;
use App\Admin\Repositories\BonusPolicyDetail\BonusPolicyDetailRepositoryInterface;
use App\Enums\User\UserRoles;
use App\Admin\Repositories\DiscountSeller\DiscountSellerRepositoryInterface;

class CheckoutController extends Controller
{


    protected $repoProduct;
    protected $repoCart;
    protected $repoOrders;
    protected $repoDetail;
    protected $serviceOrder;
    protected $repoDiscountAgent;
    protected $repoBonusPolicy;
    protected $repoBonusDetailPolicy;
    protected $repoDiscountSeller;
    public function __construct(
        ProductRepositoryInterface $repoProduct,
        UserRepositoryInterface $repository,
        OrderRepositoryInterface $repoOrders,
        ShoppingCartRepositoryInterface $repoCart,
        OrderDetailRepositoryInterface $repoDetail,
        OrderServiceInterface $serviceOrder,
        DiscountAgentRepositoryInterface $repoDiscountAgent,
        BonusPolicyRepositoryInterface $repoBonusPolicy,
        BonusPolicyDetailRepositoryInterface $repoBonusDetailPolicy,
        DiscountSellerRepositoryInterface $repoDiscountSeller,
    ) {
        parent::__construct();
        $this->repository =  $repository;
        $this->repoOrders = $repoOrders;
        $this->repoCart = $repoCart;
        $this->repoProduct = $repoProduct;
        $this->repoDetail = $repoDetail;
        $this->serviceOrder = $serviceOrder;
        $this->repoDiscountAgent = $repoDiscountAgent;
        $this->repoBonusPolicy = $repoBonusPolicy;
        $this->repoBonusDetailPolicy  = $repoBonusDetailPolicy;
        $this->repoDiscountSeller = $repoDiscountSeller;
    }


    public function getView()
    {
        return [
            'index' => 'public.checkout.index',
            'total' => 'public.checkout.fullcart',
            'item' => 'public.auth.orders.partials.item'
        ];
    }

    public function getRoute()
    {
        return [
            'home' => 'home.index',
            'login' => 'login.index',
        ];
    }

    //// gio hang
    public function totalPrice()
    {
        $roles = auth()->user()->roles;
        if ($roles === UserRoles::Agent) {

            $userId = auth()->user()->id;
            $cart = $this->repoCart->findByUserId($userId);

            $totalPrice = 0;
            $totalQty = 0;
            $discountByProduct = [];
            $lever = 0;
            foreach ($cart as $item) {
                $totalPrice += $item->price_selling;
                $totalQty += $item->quantity;

                if ($item->price_selling > 10000000) {
                    $lever = 1;
                } elseif ($item->price_selling > 5000000 && $lever !== 1) {
                    $lever = 2;
                }
            }
            foreach ($cart as $item) {
                $productId = $item->product_id;
                $product = $this->repoProduct->find($productId);
                $unit = $product->unit;
                $unitName = $unit->name;

                $discount = $this->calculateDiscount($lever, $unitName, $item->price_selling);
                $discountByProduct[$productId] = $discount;
            }
            $totalDiscount = array_sum($discountByProduct);

            $priceWithDiscount = $totalPrice - $totalDiscount;

            return view($this->view['total'], [
                'priceWithDiscount' => $priceWithDiscount,
                'priceWithoutDiscount' => $totalPrice,
                'discountByProduct' => $discountByProduct,
                'discount' => $totalDiscount,

            ]);
        }
        //// day la seller
        else {
            $userId = auth()->user()->id;
            $cart = $this->repoCart->findByUserId($userId);

            $quantities = [];
            $qty_donate = [];
            foreach ($cart as $item) {
                $productId = $item->product_id;
                $product = $this->repoProduct->find($productId);
                $promotion = $product->price_promotion;
                $discount = $this->repoDiscountSeller->getBonus($productId);
                $discountProduct_id = $discount->pluck('product_id');
                if ($discount) {
                    if ($discount instanceof \Illuminate\Support\Collection) {
                        foreach ($discount as $d) {
                            $quantities[$productId] = $d->qty;

                            $qty_donate[$productId] = round($item->quantity / 100) * $d->qty_donate;
                        }
                    } else {
                        $quantities[$productId] = $discount->qty;

                        $qty_donate[$productId] = round($item->quantity / 100) * $discount->qty_donate;
                    }
                }
            }




            return view($this->view['total'], [
                'qty_donate' => $qty_donate,
            ]);
        }
    }



    public function calculateDiscount($lever, $unitName, $price)
    {
        $discountAgent = $this->repoDiscountAgent->findLevel((int)$lever);
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






    public function index(Request $request)
    {
        if(auth()->check()){
            $roles = auth()->user()->roles;
            if ($roles === UserRoles::Agent) {
                $id = $request->input('product_id');
                $data = $this->repoProduct->find($id);
                $unit = $data->unit;
                $nameUnit = Str::lower($unit->name);
    
    
    
                if (!$data) {
                    return view('not_found');
                }
                $quantity = $request->input('qty');
    
                if (!is_numeric($quantity) || $quantity <= 0) {
                    return redirect()->back()->with('error', 'Số lượng không hợp lệ.');
                }
    
                $pricePromotion = $data['price_promotion'];
                $priceSelling = $pricePromotion * $quantity;
                $data['price_selling'] = $priceSelling;
                $lever = 0;
                if ($priceSelling > 10000000) {
                    $lever = 1;
                } elseif ($priceSelling > 5000000) {
                    $lever = 2;
                } else {
                    return view($this->view['index'], [
                        'idds' => $id,
                        'qtye' => $quantity,
                        'data' => $data,
                        'quantity' => $quantity,
                        'discount' => 0,
                        'Total' => 0,
                    ]);
                }
    
    
    
                $discountAgent = $this->repoDiscountAgent->findLevel($lever);
                $discountData = $discountAgent->discount_data;
                $priceWithDiscount = 0;
                $priceWithDiscount = 0;
                if ($nameUnit === 'pail') {
                    $quantity = max($quantity, 1);
                    $priceWithDiscount = ($discountData['pail'] / 100 ) * $data->price_promotion * $quantity;
                } else {
                    $quantity = max($quantity, 1);
                    $priceWithDiscount = ($discountData['bottle'] / 100) * $data->price_promotion * $quantity;
                }
    
    
                $priceInShow = $priceSelling - $priceWithDiscount;
    
    
    
                return view($this->view['index'], [
                    'idds' => $id,
                    'qtye' => $quantity,
                    'data' => $data,
                    'quantity' => $quantity,
                    'Total' => $priceInShow,
                    'discount' => $priceWithDiscount
                ]);
            } else {
    
                $id = $request->input('product_id');
                $quantity = $request->input('qty');
                $discoutSeller = $this->repoDiscountSeller->getBonus($id);
                $sl = [];
                $qty_donate = [];
                $discout = 0;
                $slUpdate = 0;
    
                foreach ($discoutSeller as $item) {
                    $sl = $item->qty;
                    $qty_donate = $item->qty_donate;
                }
                if ($quantity >= $sl) {
                    $slUpdate = round($quantity / 100);
    
                    $discout = intval($qty_donate * $slUpdate);
                } else {
                    $discout = 0;
                }
                $data = $this->repoProduct->find($id);
                if (!$data) {
                    return view('not_found');
                }
    
    
                if (!is_numeric($quantity) || $quantity <= 0) {
                    return redirect()->back()->with('error', 'Số lượng không hợp lệ.');
                }
                $price = $data['price_promotion'];
                $total = $price * $quantity;
    
                return view(
                    $this->view['index'],
                    [
                        'idds' => $id,
                        'data' => $data,
                        'qtye' => $quantity,
                        'price' => $price,
                        'discout' => $discout,
                        'total' => $total,
                    ]
                );
            }
        }else{
            return to_route($this->route['login'])->with('error', 'Vui lòng đăng nhập trước khi mua!');
        }
       
    }







    public function pay($id, $qtyy, OrderRequest $request)
    {
        $order = $this->serviceOrder->createOrder($id, $qtyy, $request);

        return to_route($this->route['home'])->with('success', 'Đã Lên Đơn Thành Công');
    }


    public function payCart(OrderRequest $request)
    {
        $result = $this->serviceOrder->payCart($request, $this->repoCart, $this->repoOrders, $this->repoProduct);
        return to_route($this->route['home'])->with('success', 'Đã Lên Đơn Thành Công');
    }

}
