@php
    $config = [
    'appName'=>config('app.name'),
    'locale' =>$locale = app()->getLocale(),
    'userInfo'=>$data['userInfo'],
    'module'=>$data['module'],
    'status'=>$data['status'],
    'message'=>$data['message']
    ];
@endphp
<script>window.config = {!! json_encode($config); !!};</script>
{{--@if (isset($globals))--}}
{{--    var Globals = @json($globals);--}}
{{--@else--}}
{{--    var Globals = {};--}}
{{--@endif--}}
<script src="{{ mix('js/app.js') }}"></script>
