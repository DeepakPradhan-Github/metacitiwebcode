<div class="box-body no-padding">
    <div class="table-responsive">
      <table class="table table-hover">
            <thead> 

                    <tr>
<th> @lang('view_pages.s_no')
<span style="float: right;">
</span>
</th>
<th> @lang('view_pages.zone_name')
<span style="float: right;">
</span>
</th><th> @lang('view_pages.type')
<span style="float: right;">
</span>
</th><th> @lang('view_pages.price_type')
<span style="float: right;">
</span>
</th><th> @lang('view_pages.status')
<span style="float: right;">
</span>
</th><th> @lang('view_pages.action')
<span style="float: right;">
</span>
</th>
                </tr>
                </thead>
                <tbody>
                    @php  $i= $results->firstItem(); @endphp
                    @forelse ($results as $key => $result)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $result->zoneType->zone->serviceLocation->name }}</td>
                            <td>{{ $result->zoneType->vehicleType->name }}</td>
                            <td>
                                @if ($result->price_type == 1)
                                    <span class="btn btn-success btn-sm">{{ __('view_pages.ride_now') }}</span>
                                @else
                                    <span class="btn btn-danger btn-sm">{{ __('view_pages.ride_later') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($result->zoneType->active)
                            <td><button class="btn btn-success btn-sm">@lang('view_pages.active')</button></td>
                                @else
                            <td><button class="btn btn-danger btn-sm">@lang('view_pages.inactive')</button></td>

                                @endif
                            </td>
                            <td>

                            <button type="button" class="btn btn-info dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@lang('view_pages.action')
                            </button>

                            <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                            <a class="dropdown-item" href="{{url('vehicle_fare/edit', $result->id)}}"><i class="fa fa-pencil"></i>@lang('view_pages.edit')</a>

                            <a href="#" data-bs-toggle="modal" data-bs-target="#show-price-modal" data-price="{{ $result }}" class="dropdown-item show-price-modal"> <i class="fa fa-dot-circle-o"></i>
                            {{ __('view_pages.view') }}
                            </a> 

                            @if($result->zoneType->active)
                                <a class="dropdown-item" href="{{url('vehicle_fare/toggle_status', $result->id)}}">
                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.inactive')</a>
                            @else
                                <a class="dropdown-item" href="{{url('vehicle_fare/toggle_status', $result->id)}}">
                                <i class="fa fa-dot-circle-o"></i>@lang('view_pages.active')</a>
                            @endif

                             <a class="dropdown-item sweet-delete" href="#" data-url="{{url('vehicle_fare/delete',$result->id)}}"><i class="fa fa-trash-o"></i>@lang('view_pages.delete')</a>
                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div id="no_data" class="lead no-data text-center">
                                    <img src="{{ asset('assets/img/dark-data.svg') }}">
                                    <h4 class="text-center">@lang('view_pages.no_data_found')</h4>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="intro-y g-col-12 d-flex flex-wrap flex-sm-row flex-sm-nowrap align-items-center">
    <nav class="w-full w-sm-auto me-sm-auto">
        <ul class="pagination">
            {{ $results->links('pagination::bootstrap-4') }}
        </ul>
    </nav>
</div>