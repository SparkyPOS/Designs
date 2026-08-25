@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="notify__area">
        @forelse($notifications as $notification)
            <div class="notify-item-wrapper">
                <a class="notify__item @if ($notification->is_read == Status::NO) unread--notification @endif"
                    href="{{ route('user.notifications.read', $notification->id) }}">
                    <div class="notify__content d-flex justify-content-between">
                        <div>
                            <h6 class="title">{{ __($notification->title) }}</h6>
                            <span class="date">
                                <i class="las la-clock"></i>
                                {{ diffForHumans($notification->created_at) }}
                            </span>
                        </div>
                    </div>
                </a>
                <button type="button" class="btn btn--xs btn-outline--danger notify-delete-btn confirmationBtn"
                    data-question="@lang('Are you sure to delete the notification?')"
                    data-action="{{ route('user.notifications.delete.single', $notification->id) }}">
                    <i class="las la-trash me-0"></i>
                </button>
            </div>
        @empty
             <div class="card">
                <div class="card-body h-100 flex-center">
                    <div class="empty-notification-list text-center py-xxl-5 py-lg-4 py-md-3 py-2">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                        <h3 class="text-muted mt-3">@lang('No notification found.')</h3>
                    </div>
                </div>
            </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    @if ($hasUnread)
        <a href="{{ route('user.notifications.read.all') }}" class="btn btn--sm btn-outline--base-two">
            <i class="las la-check"></i>
            @lang('Mark All as Read')
        </a>
    @endif
    @if (!blank($notifications))
        <button class="btn btn--sm btn-outline--danger confirmationBtn"
            data-action="{{ route('user.notifications.delete.all') }}" data-question="@lang('Are you sure to delete all notifications?')"><i
                class="las la-trash"></i>@lang('Delete all Notification')</button>
    @endif
@endpush

@push('style')
    <style>
        .notify-item-wrapper {
            position: relative;
        }

        .notify__item:not(:last-child) {
            margin-bottom: 5px;
        }

        .unread--notification {
            background-color: #d6d6e633 !important;
        }

        .notify__item {
            display: block;
            text-decoration: none !important;
            align-items: center;
            padding: 10px 15px;
            padding-right: 55px;
            background: #fff;
            border: 1px solid #e5e5e5;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            transition: all ease 0.3s;
        }

        .notify__item .notify__content {
            padding-left: 15px;
            color: #555555;
        }

        .notify__item .notify__content .title {
            font-size: 16px;
            margin: 0;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        @media (max-width:375px){
            .notify__item .notify__content .title {
                margin-right: 20px;
            }
        }

        .notify__item .notify__content .date {
            font-size: 12px;
            line-height: 1.5;
            display: flex;
            align-items: center;
        }

        .notify-item-wrapper .notify-delete-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endpush
