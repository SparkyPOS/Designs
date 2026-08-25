@extends('Template::layouts.frontend')

@section('content')
    <div class="py-60 cart">
        <form action="{{ route('product.order') }}" method="post">
            @csrf
            <div class="container custom--container">
                <div class="row gy-4" id="checkoutProucts">
                    @include('Template::partials.cart_products')
                </div>
            </div>
        </form>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';

            $('#checkoutProucts').on('click', '.decrementProductQty', function() {
                let cartId = $(this).data('cart-id');
                let qty = parseInt($('.productQuantity' + cartId).val()) - 1;
                if (qty >= 1) {
                    $('.productQuantity' + cartId).val(qty);
                    updateCart($(this).data('cart-id'), qty);
                }
            });

            $('#checkoutProucts').on('click', '.incrementProductQty', function() {
                let cartId = $(this).data('cart-id');
                updateCart($(this).data('cart-id'), parseInt($('.productQuantity' + cartId).val()) + 1);
            });

            $('#checkoutProucts').on('input', '.productQuantity', function() {
                if ($(this).val() < 1) {
                    $(this).val(1);
                }
                updateCart($(this).data('cart-id'), $(this).val());
            });

            function updateCart(cartId, qty) {
                let formData = {
                    cart_id: cartId,
                    quantity: qty,
                    _token: "{{ csrf_token() }}",
                }
                $.ajax({
                    type: "post",
                    url: "{{ route('product.update.cart') }}",
                    data: formData,
                    success: function(response) {
                        if (response.status == "error") {
                            notify('error', response.message);
                            return;
                        }
                        $('#checkoutProucts').html(response.data.html)
                    }
                });
            }

            $('#confirmationModal').on('submit', 'form', function(e) {
                e.preventDefault();
                let formData = {
                    cart_id: $(this).attr('action'),
                    _token: "{{ csrf_token() }}",
                }

                $.ajax({
                    type: "post",
                    url: "{{ route('product.delete.cart') }}",
                    data: formData,
                    success: function(response) {
                        if (response.status == "error") {
                            notify('error', response.message);
                            return;
                        }
                        $('#checkoutProucts').html(response.data.html)
                        $('#totalCartItems').text(response.data.totalProduct);
                    }
                });

                $('#confirmationModal').modal('hide');
            });

        })(jQuery);
    </script>
@endpush
