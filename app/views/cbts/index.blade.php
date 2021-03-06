{{--

The MIT License (MIT)

WebCBT - Web based Cognitive Behavioral Therapy tool

http://webcbt.github.io

Copyright (c) 2014 Prashant Shah <pshah.webcbt@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

--}}

@extends('layouts.master')

@section('head')

<script type="text/javascript">

$(document).ready(function() {
        $('[data-toggle="popover"]').popover('show');
});

</script>

@stop

@section('page-title', 'CBT Exercises')

@section('content')

{{ HTML::linkAction('CbtsController@getCreate', 'New CBT Exercise', array(), array('class' => 'btn btn-primary')) }}

<span class="pull-right">
        <form name="filter" method="GET">
        <select name="options[]" class="selectpicker" multiple title='All'>
                <option value="R" {{ (isset($options_selected['R'])) ? "selected" : '' }}>Resolved</option>
                <option value="U" {{ (isset($options_selected['U'])) ? "selected" : '' }}>Unresolved</option>
                @if ($tags->count() > 0)
                <optgroup label="Tags">
                        @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}" {{ (isset($options_selected[$tag->id])) ? "selected" : '' }}>
                                        {{ $tag->name }}
                                </option>
                        @endforeach
                </optgroup>
                @endif
        </select>
        <input type="submit" class="btn btn-info" value="Go">
        </form>
</span>

<br />
<br />

@if ($show_help)
<div class="alert alert-warning" role="alert">
<b><i class="fa fa-exclamation-triangle"></i> Instructions :</b> Click on each thought to dispute it. Once you have disputed all thoughts, next step is to complete the Post-dispute section of the CBT exercise by clicking on the "Actions" button. Finally once you are sataisfied with the results of the doing the CBT exercise you can mark it as resolved by clicking on the same "Actions" button.
</div>
@endif


<table class="table table-hover">
        <thead>
                <tr>
                        <th class="col-width-1">Date</th>
                        <th>Situation</th>
                        <th>Thoughts</th>
                        <th>Feelings</th>
                        <th>Physical Symptoms</th>
                        <th>Behaviours</th>
                        <th>Resolved</th>
                        <th></th>
                </tr>
        </thead>
        <tbody>
                @foreach ($cbts as $cbt)
                <tr>
                        <td>
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $cbt->date), $dateformat_php) }}
                                <br />
                                {{ date_format(date_create_from_format('Y-m-d H:i:s', $cbt->date), 'h:i A') }}
                        </td>
                        <td>
                                {{ $cbt->situation }}<br />
                                <span class="small-text">
                                created on {{ date_format(date_create_from_format('Y-m-d H:i:s', $cbt->created_at), $dateformat_php) }}
                                </span>
                                <br />
                                @define $tag = $cbt->tag
                                @if (isset($tag))
                                <span style="color:#{{ $tag['color'] }}; background:#{{ $tag['background'] }};" class="tag">
                                        {{ $tag['name'] }}
                                </span>
                                @endif
                        </td>
                        <td>
                                <ul class="list-unstyled">
                                @foreach ($cbt->cbtThoughts as $thought)
                                        <li class="list-pad">
                                                @if ($thought['is_disputed'] == 0)
                                                        {{ HTML::linkAction(
                                                                'ThoughtsController@getDispute',
                                                                $thought->thought,
                                                                array($thought->id),
                                                                array('class' => 'link-pending')) }}
                                                @else
                                                        {{ HTML::linkAction(
                                                                'ThoughtsController@getDispute',
                                                                $thought->thought,
                                                                array($thought->id),
                                                                array('class' => 'link-completed')) }}
                                                @endif
                                        </li>
                                @endforeach
                                </ul>
                                @if ($show_help)
                                        <i data-toggle="popover" data-content="Click on the above thoughts to dispute it. If the color is red then the thought needs to be disputed, if its green then the thought is disputed." data-placement="bottom"></i>
                                @endif
                        </td>
                        <td>
                                <ul class="list-unstyled">
                                @foreach ($cbt->cbtFeelings as $feeling)
                                        @if ($feeling->status == 'B')
                                                <li>
                                                        {{ $feeling->feeling->name }}
                                                        @if ($feeling->feeling->type == 1)
                                                                <span class="badge alert-success">
                                                        @elseif ($feeling->feeling->type == 2)
                                                                <span class="badge alert-danger">
                                                        @else
                                                                <span class="badge">
                                                        @endif
                                                                {{ $feeling->intensity }}
                                                        </span>
                                                </li>
                                        @endif
                                @endforeach
                                </ul>
                        </td>
                        <td>
                                <ul class="list-unstyled">
                                @foreach ($cbt->cbtSymptoms as $symptom)
                                        @if ($symptom->status == 'B')
                                                <li>
                                                        {{ $symptom->symptom->name }}
                                                        @if ($symptom->symptom->type == 1)
                                                                <span class="badge alert-success">
                                                        @elseif ($symptom->symptom->type == 2)
                                                                <span class="badge alert-danger">
                                                        @else
                                                                <span class="badge">
                                                        @endif
                                                                {{ $symptom->intensity }}
                                                        </span>
                                                </li>
                                        @endif
                                @endforeach
                                </ul>
                        </td>
                        <td>
                                <ul class="list-unstyled">
                                @foreach ($cbt->cbtBehaviours as $behaviour)
                                        @if ($behaviour->status == 'B')
                                                <li>{{ $behaviour->behaviour }}</li>
                                        @endif
                                @endforeach
                                </ul>
                        </td>
                        <td>
                                @if ($cbt->is_resolved == 1)
                                        Yes
                                @else
                                        No
                                @endif
                        </td>
                        <td>
                                <!-- Split button -->
                                <div class="btn-group">
                                <button type="button" class="btn btn-primary" data-toggle="dropdown">Actions</button>
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@getPostdispute',
                                                'Post-dispute',
                                                array($cbt->id),
                                                array('class' => '')) }}
                                </li>
                                <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@getAnalysis',
                                                'Show analysis',
                                                array($cbt->id),
                                                array('class' => '')) }}
                                </li>
                                @if ($cbt['is_resolved'] == 0)
                                        <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@putResolved',
                                                'Mark as resolved',
                                                array($cbt->id),
                                                array('class' => '', 'data-method' => 'PUT')) }}
                                        </li>
                                @else
                                        <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@putUnresolved',
                                                'Mark as unresolved',
                                                array($cbt->id),
                                                array('class' => '', 'data-method' => 'PUT')) }}
                                        </li>
                                @endif
                                <li class="divider"></li>
                                <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@getEdit',
                                                'Edit exercise',
                                                array($cbt->id),
                                                array('class' => '')) }}
                                </li>
                                <li>
                                        {{ HTML::linkAction(
                                                'CbtsController@deleteDestroy',
                                                'Delete exercise',
                                                array($cbt->id),
                                                array(
                                                        'class' => '',
                                                        'data-method' => 'DELETE',
                                                        'data-confirm' => 'Are you sure you want to delete the CBT exercise ?'
                                                )) }}
                                </li>
                                </ul>
                                </div>
                        </td>
                </tr>
                @endforeach
        </tbody>
</table>

<div class="text-center paginator-padding">
        {{ $cbts->links() }}
</div>

@stop
