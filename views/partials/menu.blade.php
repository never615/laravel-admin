@if(!isset($item['children']))
    <li>
        {{--<a href="{{ Route::has($item['route_name'])?route($item['route_name']):""}}"><i class="fa {{$item['icon']}}"></i> --}}
        <a href="{{ admin_url($item['url'])}}"><i class="fa {{$item['icon']}}"></i>
            <span>{{$item['title']}}</span>
        </a>
    </li>
@else
    <li class="treeview">
        <a href="#">
            <i class="fa {{$item['icon']}}"></i>
            <span>{{$item['title']}}</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="treeview-menu">
            @foreach($item['children'] as $item)
                @include('admin::partials.menu', $item)
            @endforeach
        </ul>
    </li>
@endif
