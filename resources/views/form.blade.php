@extends('panelio::layout.layout')

@section('body')
    @php
        $values = [];
        $old_values = old();
        foreach ($customFields as $customField) {
            $name = $customField->params['name'] ?? null;
            if ($name) {
                $values[$name] = $old_values[$name] ?? $settingValues[$name] ?? $customField->attributes['value'] ?? null;
            }
        }
    @endphp
    {!! $form->render($values) !!}
@endsection
