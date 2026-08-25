<div class="{{blank($cartProducts)? 'col-12' : 'col-xxl-9 col-lg-8'}}">
    <div class="cart-header mb-4">
        <h3 class="cart-header__title">
            <i class="las la-shopping-cart me-2"></i>
            @lang('Shopping Cart')
        </h3>
        <p class="desc mb-0">{{ count($cartProducts) }} @lang('Items')</p>
    </div>
    @if (!blank($cartProducts))
        <div class="cart-table table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>@lang('Product')</th>
                        <th>@lang('Quantality')</th>
                        <th>@lang('Price')</th>
                        <th>@lang('Subtotal')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalAmount = 0;
                        $shippingFee = 0;
                        $deliveryDays = 0;
                    @endphp
                    @foreach ($cartProducts as $cartProduct)
                        @php
                            $salePrice = $cartProduct->product->getSalePrice($cartProduct->product_variant_id);
                            $totalPrice =
                                $cartProduct->product->getSalePrice($cartProduct->product_variant_id) *
                                $cartProduct->quantity;
                            $totalAmount += $totalPrice;
                            $shippingFee = (float) ($cartProduct->vendor->shipping_fee ?? 0);
                            $deliveryDays = (int) ($cartProduct->vendor->delivery_days ?? 0);
                        @endphp
                        <tr>
                            <td data-label="@lang('Product Name')">
                                <div class="product-sidebar-item d-flex">
                                    <div class="product-sidebar-item__img">
                                        <a href="{{ route('product.details', $cartProduct->product->slug) }}"
                                            class="d-block">
                                            <img class="fit-image" src="{{ $cartProduct->product->mainImage(true) }}"
                                                alt="@lang('image')">
                                        </a>
                                    </div>
                                    <div class="product-sidebar-item__content">
                                        <h4 class="product-sidebar-item__title mb-1">
                                            <a href="{{ route('product.details', $cartProduct->product->slug) }}"
                                                class="line-limitation-1">
                                                {{ __($cartProduct->product->name) }}
                                                @if ($cartProduct->productVariant)
                                                    <small class="d-block">{{ __($cartProduct->productVariant->name) }}</small>
                                                @endif
                                            </a>
                                        </h4>
                                        <div class="cart-item-bottom">
                                            <div class="cart-item-bottom__content">
                                                <p class="writing-by">@lang('By')
                                                    {{ __($cartProduct->product?->vendor?->company_name) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="@lang('Quantality')">
                                <ul class="qnty-cart-list">
                                    <li class="qnty-cart-list__item">
                                        <div class="product-qty">
                                            <button type="button"
                                                class="product-qty__btn product-qty__decrement decrementProductQty"
                                                data-cart-id="{{ $cartProduct->id }}"><i
                                                    class="fas fa-minus"></i></button>
                                            <input type="number" name="quantity"
                                                class="product-qty__value product-qty__btn productQuantity productQuantity{{ $cartProduct->id }}"
                                                value="{{ getAmount($cartProduct->quantity) }}"
                                                data-cart-id="{{ $cartProduct->id }}">
                                            <button type="button"
                                                class="product-qty__btn product-qty__increment incrementProductQty"
                                                data-cart-id="{{ $cartProduct->id }}"><i
                                                    class="las la-plus"></i></button>
                                        </div>
                                    </li>
                                </ul>
                            </td>
                            <td data-label="@lang('Price')"> <strong>{{ showAmount($salePrice) }}</strong></td>
                            <td data-label="@lang('Subtotal')">{{ showAmount($totalPrice) }}</td>

                            <td data-label="@lang('Action')">
                                <div class="action-button-group">
                                    <button class="action-button delete deleteProduct confirmationBtn" type="button"
                                        data-question="@lang('Are you sure to delete the product?')" data-action="{{ $cartProduct->id }}">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <a class="action-button edit" href="{{ route('product.cart.design', $cartProduct->id) }}">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
    <div class="text-center">
           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="128" height="128" color="#ddd" fill="none">
                <path d="M8 16L16.7201 15.2733C19.4486 15.046 20.0611 14.45 20.3635 11.7289L21 6" stroke="currentColor" stroke-width="1" stroke-linecap="round"></path>
                <path d="M6 6H8M22 6H18.5" stroke="currentColor" stroke-width="1" stroke-linecap="round"></path>
                <path d="M10.5 3L13.5 6M13.5 6L16.5 9M13.5 6L10.5 9M13.5 6L16.5 3" stroke="currentColor" stroke-width="1" stroke-linecap="round"></path>
                <circle cx="6" cy="20" r="2" stroke="currentColor" stroke-width="1"></circle>
                <circle cx="17" cy="20" r="2" stroke="currentColor" stroke-width="1"></circle>
                <path d="M8 20L15 20" stroke="currentColor" stroke-width="1" stroke-linecap="round"></path>
                <path d="M2 2H2.966C3.91068 2 4.73414 2.62459 4.96326 3.51493L7.93852 15.0765C8.08887 15.6608 7.9602 16.2797 7.58824 16.7616L6.63213 18" stroke="currentColor" stroke-width="1" stroke-linecap="round"></path>
            </svg>
        <p class="text-center">
            @lang('No custom creations yet. Add a design and make it yours!')
        </p>
    </div>
    @endif
</div>
@if (!blank($cartProducts))
    <div class="col-xxl-3 col-lg-4">
        <div class="checkout-information">
            <h3 class="title mb-3">@lang('Summary')</h3>

            <ul class="checkout-information__list mt-3">
                <li>
                    <span>@lang('Subtotal')</span> <span>{{ showAmount($totalAmount) }}</span>
                </li>
                <li>
                    <span>@lang('Delivery fee')</span> <span>{{ showAmount($shippingFee) }}</span>
                </li>
            </ul>
            <div class="checkout-information__total">
                <span>@lang('Total')</span> <span>{{ showAmount($totalAmount + $shippingFee) }}</span>
            </div>
            <button class="btn--base btn w-100">@lang('Order Now') </button>
        </div>
        <div class="checkout-box__inner">
            <div class="checkout-box">
                <h3 class="mb-3">@lang('Shipping')</h3>
                <div class="shipping-list">
                    <div class="accordion custom--accordion address-accordion" id="addressAccordion">
                        @forelse ($addresseses as $address)
                            <div class="accordion-item">
                                <p class="accordion-header fw-bold flex-align flex-nowrap w-100 position-relative">
                                    <label for="radioAddress{{ $address->id }}"
                                        class="form--radio flex-fill flex-align flex-nowrap py-3 pe-3">
                                        <input class="form-check-input flex-shrink-0 mt-0 me-2" type="radio"
                                            name="shipping_address_id" required id="radioAddress{{ $address->id }}"
                                            value="{{ $address->id }}" @checked($address->default)>
                                        <span
                                            class="text-base w-100 fs-16 clamp-1 pe-2">{{ __($address->address) }}</span>
                                    </label>
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#address-{{ $address->id }}" aria-expanded="false"
                                        aria-controls="address-{{ $address->id }}">
                                    </button>
                                </p>
                                <div id="address-{{ $address->id }}" class="accordion-collapse collapse"
                                    data-bs-parent="#addressAccordion">
                                    <div class="accordion-body">
                                        <ul class="address-info">
                                            <li class="address-info__item">
                                                <strong class="label">@lang('Address')</strong>
                                                <span class="value">{{ __($address->address) }}</span>
                                            </li>
                                            <li class="address-info__item">
                                                <strong class="label">@lang('Zip Code')</strong>
                                                <span class="value">{{ __($address->zip) }}</span>
                                            </li>
                                            <li class="address-info__item">
                                                <strong class="label">@lang('City')</strong>
                                                <span class="value">{{ __($address->city) }}</span>
                                            </li>
                                            <li class="address-info__item">
                                                <strong class="label">@lang('State')</strong>
                                                <span class="value">{{ __($address->state) }}</span>
                                            </li>
                                            <li class="address-info__item">
                                                <strong class="label">@lang('Country')</strong>
                                                <span class="value">{{ __($address->country) }}</span>
                                            </li>
                                            <li class="address-info__item">
                                                <strong class="label">@lang('Phone')</strong>
                                                <span
                                                    class="value">{{ __($address->mobile_code) }}{{ __($address->mobile) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <h3>@lang('You have no shipping address')</h3>
                            <a href="{{ route('user.shipping.address') }}">@lang('Add Address')</a>
                        @endforelse
                    </div>

                </div>
            </div>
            <div class="checkout-delivery d-flex gap-2 align-items-center">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"
                        fill="none">
                        <path
                            d="M13.4007 22.6667H20.0006V8.8C20.0006 8.58783 19.9164 8.38434 19.7663 8.23431C19.6163 8.08429 19.4128 8 19.2006 8H1.33398M7.53398 22.6667H4.80065C4.69559 22.6667 4.59156 22.646 4.4945 22.6058C4.39744 22.5656 4.30925 22.5066 4.23497 22.4324C4.16068 22.3581 4.10175 22.2699 4.06155 22.1728C4.02134 22.0758 4.00065 21.9717 4.00065 21.8667V15.3333"
                            stroke="#0F0F0F" stroke-width="1.8" stroke-linecap="round"></path>
                        <path d="M2.66797 12H8.0013" stroke="#0F0F0F" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                        <path
                            d="M20 12H27.48C27.6346 12 27.7859 12.0449 27.9156 12.1291C28.0453 12.2134 28.1478 12.3334 28.2107 12.4747L30.5973 17.8453C30.6428 17.9473 30.6664 18.0577 30.6667 18.1693V21.8667C30.6667 21.9717 30.646 22.0758 30.6058 22.1728C30.5656 22.2699 30.5066 22.3581 30.4324 22.4324C30.3581 22.5066 30.2699 22.5656 30.1728 22.6058C30.0758 22.646 29.9717 22.6667 29.8667 22.6667H27.3333M20 22.6667H21.3333"
                            stroke="#0F0F0F" stroke-width="1.8" stroke-linecap="round"></path>
                        <path
                            d="M10.6667 25.3333C11.3739 25.3333 12.0522 25.0524 12.5523 24.5523C13.0524 24.0522 13.3333 23.3739 13.3333 22.6667C13.3333 21.9594 13.0524 21.2811 12.5523 20.781C12.0522 20.281 11.3739 20 10.6667 20C9.95942 20 9.28115 20.281 8.78105 20.781C8.28095 21.2811 8 21.9594 8 22.6667C8 23.3739 8.28095 24.0522 8.78105 24.5523C9.28115 25.0524 9.95942 25.3333 10.6667 25.3333ZM24 25.3333C24.7072 25.3333 25.3855 25.0524 25.8856 24.5523C26.3857 24.0522 26.6667 23.3739 26.6667 22.6667C26.6667 21.9594 26.3857 21.2811 25.8856 20.781C25.3855 20.281 24.7072 20 24 20C23.2928 20 22.6145 20.281 22.1144 20.781C21.6143 21.2811 21.3333 21.9594 21.3333 22.6667C21.3333 23.3739 21.6143 24.0522 22.1144 24.5523C22.6145 25.0524 23.2928 25.3333 24 25.3333Z"
                            stroke="#FD384F" stroke-width="1.8" stroke-miterlimit="1.5" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </div>
                <p class="desc fs-14">@lang('Delivered In') : {{ getAmount($deliveryDays) }} @lang('days')</p>
            </div>
        </div>
    </div>
@endif
