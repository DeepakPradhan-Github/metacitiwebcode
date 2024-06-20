@extends('admin.layouts.app')
@section('title', 'Edit Vehicle Fare')

@push('page-styles')
@endpush

@section('content')
    <!-- BEGIN: Top Bar -->
    <div class="top-bar justify-content-end">
        <!-- BEGIN: Breadcrumb -->
        <div class="-intro-x breadcrumb me-auto d-none d-sm-flex"> <a href="#" onclick="window.history.back()">Vehicle Fare</a> 
            <i data-feather="chevron-right" class="breadcrumb__icon"></i> <a href="" class="breadcrumb--active">Edit Vehicle Fare</a>
        </div>
        <!-- END: Breadcrumb -->
        @include('admin.layouts.topnavbar')

    </div>
    <!-- END: Top Bar -->

    <div class="grid columns-12 gap-6 mt-5">
        <div class="intro-y g-col-12 g-col-lg-12">
            <!-- BEGIN: Form Layout -->
            <div class="intro-y box p-5">
                <form method="post" action="{{ url('vehicle_fare/update', $zone_price->id) }}">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-lg-6 mt-4">
                            <label>{{ __('view_pages.zone') }}</label>
                            <div class="mt-2">
                                <select name="zone" id="zone" placeholder="{{ __('view_pages.select') }}" class="tom-select w-full" disabled >
                                    <option value="{{ $zone_price->zoneType->zone->id }}">{{ $zone_price->zoneType->zone->name }}</option>
                                </select>
                            </div>
                            <span class="text-danger">{{ $errors->first('zone') }}</span>
                        </div>

                        <div class="col-12 col-lg-6 mt-4">
                            <label>{{ __('view_pages.types') }}</label>
                            <div class="mt-2">
                                <select name="type" id="type" placeholder="{{ __('view_pages.select') }}" class="tom-select w-full" disabled >
                                    <option value="{{ $zone_price->zoneType->vehicleType->id }}">{{ $zone_price->zoneType->vehicleType->name }}</option>
                                </select>
                            </div>
                            <span class="text-danger">{{ $errors->first('type') }}</span>
                        </div>

                        @php
                        $card = $cash = $wallet = '';
                    @endphp
                    @if (old('payment_type'))
                        @foreach (old('payment_type') as $item)
                            @if ($item == 'card')
                                @php
                                    $card = 'selected';
                                @endphp
                            @elseif($item == 'cash')
                                @php
                                    $cash = 'selected';
                                @endphp
                            @elseif($item == 'wallet')
                                @php
                                    $wallet = 'selected';
                                @endphp
                            @endif
                        @endforeach
                    @else
                        @php
                            $paymentType = explode(',',$zone_price->zoneType->payment_type);
                        @endphp
                        @foreach ($paymentType as $val)
                            @if ($val == 'card')
                                @php
                                    $card = 'selected';
                                @endphp
                            @elseif($val == 'cash')
                                @php
                                    $cash = 'selected';
                                @endphp
                            @elseif($val == 'wallet')
                                @php
                                    $wallet = 'selected';
                                @endphp
                            @endif
                        @endforeach
                    @endif
                        <div class="col-12 col-lg-6 mt-4">
                            <label>{{ __('view_pages.payment_type') }}</label>
                            <div class="mt-2">
                                <select name="payment_type[]" id="payment_type" placeholder="{{ __('view_pages.select') }}" class="tom-select w-full" required  multiple>
                                    <option value="cash" {{ $cash }}>@lang('view_pages.cash')</option>
                                    <option value="wallet" {{ $wallet }}>@lang('view_pages.wallet')</option>
                                </select>
                            </div>
                            <span class="text-danger">{{ $errors->first('payment_type') }}</span>
                        </div>
                    </div>

                    @if ($zone_price->price_type == 1)
                        <div class="row">
                            <div class="d-flex align-items-center p-5 border-bottom border-gray-200 dark-border-dark-5">
                                <h2 class="fw-medium fs-base me-auto">
                                    Ride Now
                                </h2>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="ride_now_base_price" class="form-label">@lang('view_pages.base_price')  (@lang('view_pages.kilometer'))</label>
                                <input id="ride_now_base_price" name="ride_now_base_price" value="{{ old('ride_now_base_price', $zone_price->base_price) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_base_price') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_distance" class="form-label">@lang('view_pages.price_per_distance')  (@lang('view_pages.kilometer'))</label>
                                <input id="ride_now_price_per_distance" name="ride_now_price_per_distance" value="{{ old('ride_now_price_per_distance', $zone_price->price_per_distance) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_distance')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_price_per_distance') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="base_distance" class="form-label">@lang('view_pages.base_distance')</label>
                                <input id="ride_now_base_distance" name="ride_now_base_distance" value="{{ old('ride_now_base_distance', $zone_price->base_distance) }}" type="number" min="0" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.base_distance')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_base_distance') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_time" class="form-label">@lang('view_pages.price_per_time')</label>
                                <input id="ride_now_price_per_time" name="ride_now_price_per_time" value="{{ old('ride_now_price_per_time', $zone_price->price_per_time) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_price_per_time') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="cancellation_fee" class="form-label">@lang('view_pages.cancellation_fee')</label>
                                <input id="ride_now_cancellation_fee" name="ride_now_cancellation_fee" value="{{ old('ride_now_cancellation_fee', $zone_price->cancellation_fee) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')" required>
                                <span class="text-danger">{{ $errors->first('ride_now_cancellation_fee') }}</span>
                            </div>
                        </div>

                    @else
                        <div class="row">
                            <div class="d-flex align-items-center p-5 border-bottom border-gray-200 dark-border-dark-5">
                                <h2 class="fw-medium fs-base me-auto">
                                    Ride Later
                                </h2>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="ride_later_base_price" class="form-label">@lang('view_pages.base_price')  (@lang('view_pages.kilometer'))</label>
                                <input id="ride_later_base_price" name="ride_later_base_price" value="{{ old('ride_later_base_price', $zone_price->base_price) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.base_price')" required>
                                <span class="text-danger">{{ $errors->first('ride_later_base_price') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_distance" class="form-label">@lang('view_pages.price_per_distance')  (@lang('view_pages.kilometer'))</label>
                                <input id="ride_later_price_per_distance" name="ride_later_price_per_distance" value="{{ old('ride_later_price_per_distance', $zone_price->price_per_distance) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_distance')" required>
                                <span class="text-danger">{{ $errors->first('ride_later_price_per_distance') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="base_distance" class="form-label">@lang('view_pages.base_distance')</label>
                                <input id="ride_later_base_distance" name="ride_later_base_distance" value="{{ old('ride_later_base_distance', $zone_price->base_distance) }}" type="number" min="0" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.base_distance')" required>
                                <span class="text-danger">{{ $errors->first('ride_later_base_distance') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="price_per_time" class="form-label">@lang('view_pages.price_per_time')</label>
                                <input id="ride_later_price_per_time" name="ride_later_price_per_time" value="{{ old('ride_later_price_per_time', $zone_price->price_per_time) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.price_per_time')" required>
                                <span class="text-danger">{{ $errors->first('ride_later_price_per_time') }}</span>
                            </div>

                            <div class="col-12 col-lg-6 mt-4">
                                <label for="cancellation_fee" class="form-label">@lang('view_pages.cancellation_fee')</label>
                                <input id="ride_later_cancellation_fee" name="ride_later_cancellation_fee" value="{{ old('ride_later_cancellation_fee', $zone_price->cancellation_fee) }}" type="text" class="form-control w-full" placeholder="@lang('view_pages.enter') @lang('view_pages.cancellation_fee')" required>
                                <span class="text-danger">{{ $errors->first('ride_later_cancellation_fee') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                    <div class="text-end mt-5">
                        <button type="submit" class="btn btn-primary w-24">{{ __('view_pages.save') }}</button>
                    </div>
                </form>
            </div>
            <!-- END: Form Layout -->
        </div>
    </div>
@endsection

@push('scripts-js')
@endpush