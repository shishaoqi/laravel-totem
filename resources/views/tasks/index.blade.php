@extends("totem::layout")
@section('page-title')
    @parent
    - Tasks
@stop
@section('title')
    <div class="uk-flex uk-flex-between uk-flex-middle">
        <h4 class="uk-card-title uk-margin-remove">{{ trans('totem::tasks.tasks') }}</h4>
        <form class="uk-display-inline uk-search uk-search-default">
            <span class="uk-icon uk-search-icon">
                <icon name="search" :scale="100"></icon>
            </span>

            <input class="uk-search-input" type="search" placeholder="Search...">
        </form>
    </div>
@stop
@section('main-panel-content')
    <table class="uk-table uk-table-responsive" cellpadding="0" cellspacing="0" class="mb1">
        <thead>
            <tr>
                <th>{{ trans('totem::tasks.description') }}</th>
                <th>{{ trans('totem::tasks.average_runtime') }}</th>
                <th>{{ trans('totem::tasks.last_run') }}</th>
                <th>{{ trans('totem::tasks.next_run') }}</th>
                <th class="uk-text-center">{{ trans('totem::tasks.execute') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr class="{{$task->is_active ?: 'uk-text-danger'}}">
                    <td>
                        <a href="{{route('totem.task.view', $task)}}">
                            {{str_limit($task->description, 30)}}
                        </a>
                        <span class="uk-float-right uk-hidden@s uk-text-muted">{{ trans('totem::tasks.command') }}</span>
                    </td>
                    <td>
                        {{ number_format(  $task->averageRuntime / 1000 , 2 ) }} seconds
                        <span class="uk-float-right uk-hidden@s uk-text-muted">{{ trans('totem::tasks.average_runtime') }}</span>
                    </td>
                    @if($last = $task->lastResult)
                        <td>
                            {{$last->ran_at->toDateTimeString()}}
                            <span class="uk-float-right uk-hidden@s uk-text-muted">{{ trans('totem::tasks.last_run') }}</span>
                        </td>
                    @else
                        <td>
                            N/A
                            <span class="uk-float-right uk-hidden@s uk-text-muted">{{ trans('totem::tasks.last_run') }}</span>
                        </td>
                    @endif
                    <td>
                        {{$task->upcoming}}
                        <span class="uk-float-right uk-hidden@s uk-text-muted">{{ trans('totem::tasks.next_run') }}</span>
                    </td>
                    <td class="uk-text-center@m">
                        <execute-button :data-task="{{$task}}" url="{{route('totem.task.execute', $task)}}" icon-name="play" button-class="uk-button-link"></execute-button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="uk-text-center" colspan="5">
                        <img class="uk-svg" width="50" height="50" src="{{asset('/vendor/totem/img/funnel.svg')}}">
                        <p>{{ trans('totem::tasks.no_task') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@stop
@section('main-panel-footer')
    <a class="uk-button uk-button-primary uk-button-small" href="{{route('totem.task.create')}}">{{ trans('totem::tasks.new_task') }}</a>
    {{$tasks->links('totem::partials.pagination')}}
@stop