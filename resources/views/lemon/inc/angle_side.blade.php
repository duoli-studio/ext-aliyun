<!-- sidebar-->
<aside class="aside">
    <!-- START Sidebar (left)-->
    <div class="aside-inner">
        <nav data-sidebar-anyclick-close class="sidebar">
            <!-- START sidebar nav-->
            <ul class="nav">
                @foreach($_menus as $nav_key => $nav)
                    @if($nav['flat_links'])
                        <li class="nav-heading" data-rel="{!! $nav_key !!}">
                            <span>{!! $nav['icon'] !!} {!! $nav['title'] !!}</span>
                        </li>
                    @endif
                    @if (is_array($nav['groups']) && count($nav['groups']))
                        @foreach($nav['groups'] as $nav_group)
							<?php $group_link = $nav['links'][$nav_group]; ?>
                            @if (isset($group_link['links']) && !empty($group_link['links']))
                                @foreach($group_link['links'] as $sub)
                                    <li class="{!! route_current($sub['route'], 'active') !!} "
                                        data-rel-self="{!! $nav_key !!}">
                                        <a href="{{ isset($sub['is_url']) ? $sub['url'] : route_url($sub['route'], null, $sub['param'])}}"
                                           title="{{$sub['title']}}">
                                            {!! $sub['icon'] !!}
                                            <span>{{$sub['title']}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>