{!! "<?php" !!}

namespace PHPSTORM_META {

@foreach($methods as $method)
    override({!! $method !!}(0), map([
        '' => '@',
@foreach($bindings as $abstract => $concrete)
        '{!! $abstract !!}' => \{!! $concrete !!}::class,
@endforeach
    ]));
@endforeach

    override(\Illuminate\Support\Arr::add(0), type(0));
    override(\Illuminate\Support\Arr::except(0), type(0));
    override(\Illuminate\Support\Arr::first(0), elementType(0));
    override(\Illuminate\Support\Arr::last(0), elementType(0));
    override(\Illuminate\Support\Arr::get(0), elementType(0));
    override(\Illuminate\Support\Arr::only(0), type(0));
    override(\Illuminate\Support\Arr::prepend(0), type(0));
    override(\Illuminate\Support\Arr::pull(0), elementType(0));
    override(\Illuminate\Support\Arr::set(0), type(0));
    override(\Illuminate\Support\Arr::shuffle(0), type(0));
    override(\Illuminate\Support\Arr::sort(0), type(0));
    override(\Illuminate\Support\Arr::sortRecursive(0), type(0));
    override(\Illuminate\Support\Arr::where(0), type(0));
    override(\head(0), elementType(0));
    override(\last(0), elementType(0));
    override(\optional(0), type(0));
    override(\tap(0), type(0));
    override(\with(0), type(0));
}
